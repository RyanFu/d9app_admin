<?php
namespace Gate\Libs;

use \Gate\Libs\Memcache;

class Session {

    private $user = NULL;
    private $mark = 'oa_mlsoatoken';
    private $markvalue = NULL;
    private $memHandle = NULL;

    public static function Singleton() {
        static $single = NULL;
        is_null($single) && $single = new self();
        return $single;
    }

    private function __construct() {
        $this->memHandle = Memcache::instance();
    }

    public function __get($field) {
        if (!empty($this->user))
            return $this->user->$field;
        return NULL;
    }

    public function set($user) {
        $this->user = $user;
    }

    public function load($COOKIE) {
        if (isset($COOKIE[$this->mark])) {
            $this->markvalue = $COOKIE[$this->mark];
            $value = $this->memHandle->get($this->markvalue);
            if(is_object($value))
                $this->user = $value;
				if( !isset($value->id) && isset($value->user_id)){
					$this->user->id = $value->user_id;
				}
        }
        return $this;
    }

    private function store($user) {
        $this->memHandle->set($this->markvalue, $user);
    }

    public function reflash($user) {
        $this->store($user);
    }

    public function marked($user) {
        $this->markvalue = \Gate\Libs\Utilities::getUniqueId();
        $this->store($user);
        //set cookie
        setcookie($this->mark, $this->markvalue, time() + 3600 * 24 * 365, '/', 'd9app.com');
    }

    public function destory() {
        $this->store(NULL);
        $this->user = NULL;
        setcookie($this->mark, NULL, 0, '/', 'd9app.com');
    }

}
