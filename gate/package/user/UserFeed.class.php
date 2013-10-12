<?PHP
namespace Gate\Package\User;

/**
 * 时间管理 
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class UserFeed {

    private static $instance;

    private static $table = 't_crab_staff_feed';

    private static $col = 'feed_id, user_id, user_name, feed_type, feed_title, feed_body, dateline';

    private static $fields = array(
            'user_id'       => 0,
            'user_name'     => '',
            'feed_type'     => '',
            'feed_title'    => '',
            'feed_body'     => '',
    );

    private static $update_fields = array(
        'user_id'       => 'int',
        'user_name'     => 'string',
        'feed_type'     => 'string',
        'feed_title'    => 'string',
        'feed_body'     => 'string',
    );

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self(); 
        }   
        return self::$instance;
    }

    public function __construct() {

    }

    /**
     * 查询feed表
     * @param array $id
     * @return array
     * @access public
     */
    public static function getDataById($id = array()){

        if (empty($id)) {
            return FALSE;
        }

        !is_array($id) && $id = array($id);
        $idStr = "'" . implode("','", $id) . "'";

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE feed_id IN ({$idStr}) ";

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 查询feed表
     * @param array $id
     * @param int $start
     * @param int  $num
     * @access public
     */
    public static function getDataByUser($id = array(), $start = 0, $num = 20){
        if (empty($id)) {
            return array();
        }

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 1";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sql .= " and user_id IN ({$idStr}) ";
        }


        $sql .= " ORDER BY dateline DESC LIMIT {$start}, {$num} ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);
        return $result;
    }

    /**
     * 添加
     */
    public static function addFeed($params) {

        if (!isset($params['user_id']) || !isset($params['feed_type']) || !isset($params['feed_title']) || !isset($params['feed_body'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        $sql = "INSERT INTO " . self::$table . " (user_id, user_name, feed_type, feed_title, feed_body) VALUES (:_user_id, :user_name, :feed_type, :feed_title, :feed_body)";

        $params = array(
            '_user_id'      => $params['user_id'],
            'user_name'     => $params['user_name'],
            'feed_type'     => $params['feed_type'],
            'feed_title'    => $params['feed_title'],
            'feed_body'     => $params['feed_body'],
        );

        $ret = DBGateHelper::getConn()->write($sql, $params);
        return $ret === FALSE ? FALSE : DBGateHelper::getConn()->getInsertId();
    }

    /**
     * 更新
     */
    public static function updateData($params) {

        if (!isset($params['time_id']) || !isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);

        $sql = "UPDATE " . self::$table . " SET ";
        $sqlData = array();

        foreach ($params as $key => $value) {
            switch (self::$update_fields[$key]) {
                case 'int':
                    $sql .= "`{$key}` = :_{$key},";
                    $sqlData["_{$key}"] = $value;
                    break;
                case 'string':
                    $sql .= "`{$key}` = :{$key},";
                    $sqlData[$key] = $value;
                    break;
                default:
                    break;
            }
        }

        $sql = rtrim($sql, ",");
        $sql .= " WHERE time_id = :_time_id and user_id = :_user_id ";
        $sqlData['_time_id'] = $params['time_id'];
        $sqlData['_user_id'] = $params['user_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

} 
