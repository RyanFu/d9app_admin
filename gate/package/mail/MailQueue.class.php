<?PHP
namespace Gate\Package\Mail;

/**
 * 会议室预定
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class MailQueue {

    private static $instance;

    private static $table = 't_crab_mail_queue';

    private static $col = 'queue_id, book_id, room_id, user_id, subject, message, start_time, create_time, is_send';

    private static $limit = 3;

    private static $fields = array(
            'queue_id'      => 0,
            'book_id'       => 0,
            'room_id'       => 0,
            'user_id'       => 0,
            'subject'       => '',
            'message'       => '',
            'start_time'    => '',
    );

    private static $update_fields = array(
        'book_id'       => 'int',
        'room_id'       => 'int',
        'user_id'       => 'int',
        'subject'       => 'string',
        'message'       => 'string',
        'start_time'    => 'string',
        'is_send'       => 'int',
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
     * 创建时
     * @access public
     */
    public static function getData(){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . ' WHERE is_send = 0 order by create_time asc limit ' . self::$limit;

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 开会前提醒
     * @param int $time
     * @access public
     */
    public static function getRemind($time = 0){

        if (empty($time)) {
            //5分钟
            $time = time()+300;
        }
        $start = date('Y-m-d H:i:s', $time);
        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE is_send = 1 and start_time <= '{$start}' order by start_time asc limit " . self::$limit;

        $result = DBGateHelper::getConn()->read($sql, array());
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

        $sql = "INSERT INTO " . self::$table . " (book_id, room_id, user_id, subject, message, start_time) VALUES (:_book_id, :_room_id, :_user_id, :subject, :message, :start_time)";

        $params = array(
            '_book_id'      => $params['book_id'],
            '_room_id'      => $params['room_id'],
            '_user_id'      => $params['user_id'],
            'subject'       => $params['subject'],
            'message'       => $params['message'],
            'start_time'    => $params['start_time'],
        );

        $ret = DBGateHelper::getConn()->write($sql, $params);
        return $ret === FALSE ? FALSE : DBGateHelper::getConn()->getInsertId();
    }

    /**
     * 更新
     */
    public static function updateBookData($params = array()) {

        if (!isset($params['user_id']) || !isset($params['book_id'])){
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
        $sql .= " WHERE book_id = :_book_id and user_id = :_user_id and room_id = :_room_id ";
        $sqlData['_book_id'] = $params['book_id'];
        $sqlData['_user_id'] = $params['user_id'];
        $sqlData['_room_id'] = $params['room_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

    /**
     * 更新
     */
    public static function updateData($queue_id = array()) {

        if (!isset($queue_id)){
            return FALSE;
        }

        $sql = " UPDATE " . self::$table . " SET is_send = 1  WHERE 1 ";

        if (!empty($queue_id)) {
            !is_array($queue_id) && $id = array($queue_id);
            $idStr = "'" . implode("','", $queue_id) . "'";
            $sql .= " and queue_id IN ({$idStr}) ";
        }

        return DBGateHelper::getConn()->write($sql, array());
    }

    /**
     * 删除
     */
    public static function deleteData($queue_id = array()) {

        if (!isset($queue_id)){
            return FALSE;
        }

        $sql = " delete from " . self::$table . " WHERE 1 ";

        if (!empty($queue_id)) {
            !is_array($queue_id) && $id = array($queue_id);
            $idStr = "'" . implode("','", $queue_id) . "'";
            $sql .= " and queue_id IN ({$idStr}) ";
        }

        return DBGateHelper::getConn()->write($sql, array());
    }

    /**
     * 删除
     */
    public static function deleteDataByUser($book_id = 0, $user_id = array()) {

        if (empty($book_id)){
            return FALSE;
        }

        $sql = " delete from " . self::$table . " ";
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

    /**
     * 删除
     */
    public static function deleteDataByBook($book_id = 0) {

        if (empty($book_id)){
            return FALSE;
        }

        $sql = " delete from " . self::$table . " ";
        $sqlData = array();

        $sql .= " WHERE book_id = :_book_id ";
        $sqlData['_book_id'] = $book_id;

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

} 
