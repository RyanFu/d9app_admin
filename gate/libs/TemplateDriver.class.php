<?php
namespace Gate\Libs;

class TemplateDriver {

    protected $formatData;
    protected $module;
    protected $action;
    protected $user;

    private $templatePath = TEMPLATE_PATH;
    private static $templateDriver = NULL; 
    

	public function __construct() {

	}

    public static function getInstance() {
        is_null(self::$templateDriver) && self::$templateDriver= new self();
        return self::$templateDriver;
    }

    public function loadParam($formatData, $module, $action, $user) {
        $this->formatData = $formatData;
        $this->module  = $module;
        $this->action = $action;
        $this->user = $user;
    }

    public function loadTemplate() {
		if(!empty($this->formatData)) {
			extract($this->formatData);
		}
        if ('auth' == $this->module && 'login' == $this->action) {
            require($this->templatePath . $this->module .  "/" . ucwords(strtolower($this->action)) . ".view.php");
        }
        else {
            $this->loadHeader();
            $templateFile = $this->templatePath . $this->module .  "/" . ucwords(strtolower($this->action)) . ".view.php";
            if (file_exists($templateFile)) {
                require($templateFile);
            }else{
                echo '<div class="alert alert-danger">Template File : <b>', $templateFile, '</b> is not found!</div>';
            }
            $this->loadFooter();
        }
    }

    private function loadHeader() {
        extract(array('nickname' => $this->user->user_name));
        require($this->templatePath . "header.view.php");
    }

    private function loadFooter() {
        require($this->templatePath . "footer.view.php");
    }

	
}
