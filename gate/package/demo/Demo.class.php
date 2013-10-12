<?php
namespace Gate\Package\Demo;

use \Gate\Package\Helper\DBDolphinHelper;
use \Gate\Package\Helper\MemcacheHelper;

class Demo{

	private static $instance;

	private static $cache_time = 500;

	private static $table = 't_dolphin_twitter_info';

	private static $col = 'twitter_id, twitter_author_uid, twitter_images_id, twitter_content, twitter_htmlcontent, twitter_create_ip, twitter_create_time';

	private $data = array();

    public function __construct() {

	}

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self(); 
        }   
        return self::$instance;
    }

	/**
     * 查询page view
     */
	public static function getDataInfo($twitter_id = 1){

		if (empty($twitter_id)) {
			return FALSE;
		}

        $cacheHelper = MemcacheHelper::instance();
		$cacheKey = "Demo:{$twitter_id}";
        $result = $cacheHelper->get($cacheKey);
		if (!empty($result)) {
			return $result;
		}

		$result = self::getDataInfoNoCache($twitter_id);

		if (!empty($result)) {
			$cacheHelper->set($cacheKey, $result, self::$cache_time);
		}

		return $result;
	}

	/**
     * 查询page view
     */
	private static function getDataInfoNoCache($twitter_id = 1){

		if (empty($twitter_id)) {
			return FALSE;
		}

		$sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . ' WHERE twitter_id = :_twitter_id ';

		$params = array(
			'_twitter_id' => $twitter_id,
		);

		$result = DBDolphinHelper::getConn()->read($sql, $params);
		return $result;
	}

}

