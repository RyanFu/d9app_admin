<?php
namespace Gate\Libs;
use Gate\Package\Address\Staffinfo;

abstract class Controller {

	protected $request = NULL;
	protected $module = NULL;
	protected $action = NULL;
	protected $head = 200;
    //receive format data
	protected $view = "";
 	protected $view_switch = TRUE;

	protected $error_code = 0;
	protected $message = 'OK';

	protected $client = NULL;

	protected $userId = 0;
    protected $username = '';
	/**
	 * Subclasses must implement this method to route requests.
	 */
	abstract public function run();

	public function __construct($request, $module, $action) {
		$this->request = $request;
		$this->module = $module;
		$this->action = $action;
		$this->userId = $this->request->session->id;

		$this->userId = 1;
		$this->username = 'admin';

		/*
        if(!empty($this->userId)){
            $param = array('redmineid'=>$this->userId);
            $getparm = array('sid', 'name_c');
            $user=Staffinfo::getInstance()->GetStaffinfo($param,$getparm,0);
            if(isset($user[0]['sid']) && !empty($user[0]['sid'])){
                $this->userId = $user[0]['sid'];
                $this->username = $user[0]['name_c'];
            }else{
                $this->userId = 0;
            }
		}
		 */
	}

	public function control() {
		try {
			$this->run();
		}
		catch (\Exception $e) {
            if (!$e instanceof \Gate\Libs\VException) {
                $log = new \Phplib\Tools\Liblog('gate_error_log', 'normal');
                $message = $e->getMessage();
                $log->w_log($message . " by file " . $e->getFile() . " in line " . $e->getLine());
			    $this->setError(400, 11011, $message);
            }
            else {
                $http_code = $e->getHttpCode();
                $error_code = $e->getCode();
                $message = $e->getEMessage();
			    $this->setError($http_code, $error_code, $message);
            }
		}
	}

    public function echoView() {
		$this->echoHeader();
		echo $this->formatJson();
	}

    //support template output
    public function echoTemplate() {

    	//add view switch in controller
    	if ($this->view_switch) {
	        $templateDriver = TemplateDriver::getInstance();
	        $templateDriver->loadParam($this->view, $this->module, $this->action, $this->request->session);

	        $templateDriver->loadTemplate();
    	}else{
    		$this->echoView();
    	}
    }

	public function checkStatusValid() {
		if (200 == $this->head) {
			return TRUE;
		}
		return FALSE;
	}

	
	private function formatJson() {
		if (200 === $this->head) {
			$response = $this->view;
		}
		else {
			$response = array(
				'error_code' => $this->error_code,
				'message' => $this->message,
			);
		}
		return json_encode($response);
	}

	protected function setError($head = 200, $errorCode = 0, $message = 'OK') {
		$this->head = $head;
		$this->error_code = $errorCode;
		$this->message = $message;
	}

	protected function echoHeader() {
		if (200 == $this->head) {
			header('Content-Type: text/plain; charset=UTF-8');
			return;
		}
		$this->setHeaderByHttpStatusCode($this->head);
	}

	protected function setHeaderByHttpStatusCode($code) {
		$codes = array(
			'400' => '400 Bad Request',
			'401' => '401 Unauthorized',
			'404' => '404 Not Found',
		);

		if (!isset($codes[$code])) {
			throw new \Exception(sprintf("Unknown HTTP status code: %s.", $code));
		}

		header("HTTP/1.1 {$codes[$code]}");
	}

	protected function authorizeOrderBackend() {
		$token = $this->request->REQUEST['token'];
		//md5('wearedootaman');
		$rightToken = '4b99ac82830933d95aece3b4f5e47bbf';
		if (empty($token) || $token != $rightToken) {
			throw new \Gate\Libs\VException('in valid token:' . $token, 29010);
		}
		$white = array();
		$adminUid = $this->request->REQUEST['admin_uid'];
		if (empty($adminUid) || (!empty($white) && !in_array($adminUid, $white))) {
			throw new \Gate\Libs\VException('in valid admin uid', 29011);
		}
		return $adminUid;
	}

}
