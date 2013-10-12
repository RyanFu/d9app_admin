<?PHP
namespace Gate\Package\Meeting;

/**
 * 会议室预定
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class MeetingBook {

    private static $instance;

    private static $table = 't_crab_meeting_book';

    private static $col = 'book_id, user_id, room_id, meeting_topic, invite_users, start_time, end_time, create_time, others';

    private static $fields = array(
            'book_id'       => 0,
            'user_id'       => 0,
            'room_id'       => 0,
            'meeting_topic' => '',
            'invite_users'  => '',
            'start_time'    => '',
            'end_time'      => '',
            'others'        => '',
    );

    private static $update_fields = array(
        'room_id'       => 'int',
        'meeting_topic' => 'string',
        'invite_users'  => 'string',
        'start_time'    => 'string',
        'end_time'      => 'string',
        'others'        => 'string',
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
     * 查询会议室
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataById($id = array(), $start_time = '', $end_time = '', $start = 0, $num = 99){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 0 ";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sqlRoom = " and room_id IN ({$idStr}) and status = 1 ";
        }else{
            $sqlRoom = "";
        }

        if (!empty($start_time)) {
            $sql .= " or (start_time <= '{$start_time}' and end_time > '{$start_time}' {$sqlRoom}) ";
            $sqlData["start_time"] = $start_time;
        }

        if (!empty($end_time)) {
            $sql .= " or (start_time < '{$end_time}' and end_time > '{$end_time}' {$sqlRoom}) ";
            $sqlData["end_time"] = $end_time;
        }

        if (!empty($start_time) && !empty($end_time)) {
            $sql .= " or (start_time > '{$start_time}' and end_time < '{$end_time}' {$sqlRoom}) ";
            $sqlData["end_time"] = $end_time;
        }

        $sql .= " LIMIT {$start}, {$num} ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

    /**
     * 查询会议室
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataByUser($id = array(), $start_time = '', $end_time = '', $start = 0, $num = 20){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 0 ";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sqlRoom = " and user_id IN ({$idStr}) and status = 1 ";
        }else{
            $sqlRoom = "";
        }

        if (!empty($start_time)) {
            $sql .= " or (start_time <= '{$start_time}' and end_time > '{$start_time}' {$sqlRoom}) ";
            $sqlData["start_time"] = $start_time;
        }

        if (!empty($end_time)) {
            $sql .= " or (start_time < '{$end_time}' and end_time > '{$end_time}' {$sqlRoom}) ";
            $sqlData["end_time"] = $end_time;
        }

        if (!empty($start_time) && !empty($end_time)) {
            $sql .= " or (start_time > '{$start_time}' and end_time < '{$end_time}' {$sqlRoom}) ";
            $sqlData["end_time"] = $end_time;
        }

        $sql .= " LIMIT {$start}, {$num} ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);
        return $result;
    }

    /**
     * 添加
     */
    public static function addData($params) {

        if (!isset($params['user_id']) || !isset($params['room_id']) || !isset($params['start_time']) || !isset($params['end_time'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        $sql = "INSERT INTO " . self::$table . " (user_id, room_id, meeting_topic, invite_users, start_time, end_time, others) VALUES (:_user_id, :_room_id, :meeting_topic, :invite_users, :start_time, :end_time, :others)";

        $params = array(
            '_user_id'      => $params['user_id'],
            '_room_id'      => $params['room_id'],
            'meeting_topic' => $params['meeting_topic'],
            'invite_users'  => $params['invite_users'],
            'start_time'    => $params['start_time'],
            'end_time'      => $params['end_time'],
            'others'        => $params['others'],
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
     * 查询用户参与的会议
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getMeetingDataByUser($id = array(), $start_time = '', $end_time = '', $start = 0, $num = 20){
        if (empty($id)) {
            return array();
        }

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE status = 1 ";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sql .= " and user_id IN ({$idStr}) ";
        }

        if (!empty($start_time)) {
            $sql .= " and start_time > :start_time ";
            $sqlData["start_time"] = $start_time;
        }

        if (!empty($end_time)) {
            $sql .= " and end_time < :end_time ";
            $sqlData["end_time"] = $end_time;
        }

        $sql .= " LIMIT {$start}, {$num} ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);
        return $result;
    }

    /**
     * 查询会议室
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataByBook($id = array()){

        if (empty($id)){
            return FALSE;
        }

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE status = 1 ";
        $sqlData = array();

        !is_array($id) && $id = array($id);
        $idStr = "'" . implode("','", $id) . "'";
        $sql .= " and book_id IN ({$idStr}) ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

} 
