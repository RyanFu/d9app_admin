<?PHP
namespace Gate\Package\Meeting;

/**
 * 会议室
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class MeetingRoom {

    private static $instance;

    private static $table = 't_crab_meeting_room';

    private static $col = 'room_id, room_no, room_name, room_position, room_capacity';

    private static $fields = array(
            'room_id'       => 0,
            'room_name'     => '',
            'room_position' => '',
            'room_capacity' => 0,
    );

    private static $update_fields = array(
        'room_name'     => 'string',
        'room_position' => 'string',
        'room_capacity' => 'int',
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
     */
    public static function getDataById($id = array()){

        if (empty($id)) {
            return FALSE;
        }

        !is_array($id) && $id = array($id);
        $idStr = "'" . implode("','", $id) . "'";

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE room_id IN ({$idStr}) ";

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 查询会议室容量
     */
    public static function getDataByCapacity($capacity = 0){

        !is_int($capacity) && $$capacity = intval($$capacity);

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE room_capacity >= {$capacity} ";

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 查询列表
     */
    public static function getAllData($start = 0, $num = 99){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 1 LIMIT {$start}, {$num}";

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 添加
     */
    public static function addData($params) {

        if (!isset($params['room_name'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        $sql = "INSERT INTO " . self::$table . " (room_name, room_position, room_capacity) VALUES (:room_name, :room_position, :_room_capacity)";

        $params = array(
            'room_name'         => $params['room_name'],
            'room_position'     => $params['room_position'],
            '_room_capacity'    => $params['room_capacity'],
        );

        $ret = DBGateHelper::getConn()->write($sql, $params);
        return $ret === FALSE ? FALSE : DBGateHelper::getConn()->getInsertId();
    }

    /**
     * 更新
     */
    public static function updateData($params) {

        if (!isset($params['room_name']) || !isset($params['room_id'])){
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
        $sql .= " WHERE room_id = :_room_id ";
        $sqlData['_room_id'] = $params['room_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

    /**
     * 删除
     */
    public static function deleteData($params) {

        if (!isset($params['room_id'])){
            return FALSE;
        }

        $sql = "delete from " . self::$table . " ";
        $sqlData = array();

        $sql .= " WHERE room_id = :_room_id ";
        $sqlData['_room_id'] = $params['room_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

} 
