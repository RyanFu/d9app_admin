<?php
namespace Gate\Libs;

class Dispatcher {

	private $request = NULL;
	private $module = NULL;
	private $action = NULL;
	private $xhprof = FALSE;
    private $userid = NULL;

	public static function get() {
		static $singleton = NULL;
		is_null($singleton) && $singleton = new Dispatcher();
		return $singleton;
	}

	private function __construct() {
		try {
			$this->request = new \Gate\Libs\HttpRequest();
            $this->userid = $this->request->session->id;
		}
		catch (\Exception $e) {
			$log = new \Phplib\Tools\Liblog('gate_error_log', 'normal');
			$log->w_log($e->getMessage());
			echo json_encode(array('error_code' => 10002, 'message' => $e->getMessage()));
			die();
		}
	}

	public function dispatch() {
        //output HTML CODE
        if (OA_VIEW_SWITCH == 'ON') {
            $path_args = $this->request->path_args;
            // first arg is the module's name
            $module = array_shift($path_args);
            $action = array_shift($path_args);


            if ($this->userid) {
                empty($module) && $module = 'auth';
                empty($action) && $action = 'home';
            }
            else {
                $module = 'auth';
                $action = 'login';
            }

            $this->module = $module;
            $this->action = $action;

            $class = '\\Gate\\Modules\\' . ucwords($module) . '\\' . ucwords($action);
            $this->request->path_args = $path_args;
            if (!class_exists($class)) {
                $class = "\\Gate\\Modules\\Bad\\Badrequest";
            }
            $controller = new $class($this->request, $this->module, $this->action);
            $controller->control();
            $controller->echoTemplate();
        }
        //output json data
        else  {
            $path_args = $this->request->path_args;
            // first arg is the module's name
            $module = array_shift($path_args);
            empty($module) && $module = 'bad';
            $this->module = $module;

            $action = array_shift($path_args);
            empty($action) && $action = 'badrequest';
            $this->action = $action;
            // pass the control to module's Router class
            
            $class = '\\Gate\\Modules\\' . ucwords($module) . '\\' . ucwords($action);
            $this->request->path_args = $path_args;
            if (!class_exists($class)) {
                $class = "\\Gate\\Modules\\Bad\\Badrequest";
            }
            $controller = new $class($this->request);
            $controller->control();
            $controller->echoView();

        }



    }

	private function startAnylize() {
		if ($this->xhprof === TRUE) {
			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);	
		}
	}

	private function finishAnylize() {
		if ($this->xhprof === TRUE) {
			$xhprof_data = xhprof_disable();	
			$xhprof_obj = new \Gate\Package\User\Xhprof();
			$uniqid = uniqid();
			$author = $this->module . ':' . $this->action;
			$xhprof_obj->addData($uniqid, $xhprof_data, $author);
		}
	}

	public function get_request() {
		return $this->request;
	}

	public function get_module() {
		return $this->module;
	}

	public function get_action() {
		return $this->action;
	}
}
