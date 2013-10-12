<?PHP
namespace Gate\Package\Meeting;

/**
 * 会议室预定
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class MeetingBookUser {

    private static $instance;

    private static $table = 't_crab_meeting_book_user';

    private static $col = 'map_id, book_id, user_id, status';

    private static $fields = array(
            'map_id'       => 0,
            'book_id'      => 0,
            'user_id'      => 0,
            'status'       => 0,
    );

    private static $update_fields = array(
        'book_id'       => 'int',
        'user_id'       => 'int',
        'status'        => 'int',
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
     * 查询
     * @param array $id
     * @access public
     */
    public static function getDataById($id = array(), $status = 1){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE status =:_status ";
        $sqlData = array('_status' => $status);

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sql .= " and book_id IN ({$idStr}) ";
        }

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

    /**
     * 查询
     * @param array $id
     * @access public
     */
    public static function getDataByUser($id = array(), $status = 1){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 1 ";
        $sqlData = array('_status' => $status);

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sql .= " and user_id IN ({$idStr}) ";
        }

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

    /**
     * 添加
     */
    public static function addData($params) {

        if (!isset($params['user_id']) || !isset($params['book_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        $sql = "INSERT INTO " . self::$table . " (book_id, user_id) VALUES (:_book_id, :_user_id)";

        $params = array(
            '_book_id'      => $params['book_id'],
            '_user_id'      => $params['user_id'],
        );

        $ret = DBGateHelper::getConn()->write($sql, $params);
        return $ret === FALSE ? FALSE : DBGateHelper::getConn()->getInsertId();
    }

    /**
     * 更新
     */
    public static function updateData($params) {

        if (!isset($params['book_id']) || !isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);

        $sql = "UPDATE " . self::$table . " SET ";
        $sqlData = array();

        foreach ($params as $key => $value) {
            if (isset(self::$update_fields[$key])) {
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
        }

        $sql = rtrim($sql, ",");
        $sql .= " WHERE book_id = :_book_id and user_id = :_user_id ";
        $sqlData['_book_id'] = $params['book_id'];
        $sqlData['_user_id'] = $params['user_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

    /**
     * 删除
     */
    public static function deleteData($params) {

        if (!isset($params['book_id']) || !isset($params['user_id'])){
            return FALSE;
        }

        $sql = " UPDATE " . self::$table . " set status = 0 ";
        $sqlData = array();

        $sql .= " WHERE book_id = :_book_id and user_id = :_user_id ";
        $sqlData['_book_id'] = $params['book_id'];
        $sqlData['_user_id'] = $params['user_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

    /**
     * 删除
     */
    public static function deleteBookData($book_id = 0, $user_id = array()) {

        if (empty($book_id)){
            return FALSE;
        }

        $sql = " UPDATE " . self::$table . " set status = 0 ";
        $sqlData = array();

        $sql .= " WHERE book_id = :_book_id ";
        $sqlData['_book_id'] = $book_id;

        if (!empty($user_id)) {
            !is_array($user_id) && $id = array($user_id);
            $idStr = "'" . implode("','", $user_id) . "'";
            $sql .= " and user_id IN ({$idStr}) ";
        }

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

} 
