<?PHP
namespace Gate\Package\Time;

/**
 * 时间管理 
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class TimeManage {

    private static $instance;

    private static $table = 't_crab_time_manage';

    private static $col = 'time_id, user_id, start_time, end_time, create_time, dowhat, color, others';

    private static $fields = array(
            'time_id'       => 0,
            'user_id'       => 0,
            'start_time'    => '',
            'end_time'      => '',
            'dowhat'        => '',
            'color'         => '',
            'others'        => '',
    );

    private static $update_fields = array(
        'time_id'       => 'int',
        'user_id'       => 'int',
        'start_time'    => 'string',
        'end_time'      => 'string',
        'dowhat'        => 'string',
        'color'         => 'string',
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
     * 查询时间表
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataById($id = array()){

        if (empty($id)) {
            return FALSE;
        }

        !is_array($id) && $id = array($id);
        $idStr = "'" . implode("','", $id) . "'";

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE status=1 and time_id IN ({$idStr}) ";

        $result = DBGateHelper::getConn()->read($sql, array());
        return $result;
    }

    /**
     * 查询时间表
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataByUser($id = array(), $start_time = '', $end_time = '', $start = 0, $num = 20){
        if (empty($id)) {
            return array();
        }

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE status=1";
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
     * 添加
     */
    public static function addData($params) {

        if (!isset($params['user_id']) || !isset($params['dowhat']) || !isset($params['start_time']) || !isset($params['end_time'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        $sql = "INSERT INTO " . self::$table . " (user_id, start_time, end_time, dowhat, color, others) VALUES (:_user_id, :start_time, :end_time, :dowhat, :color, :others)";

        $params = array(
            '_user_id'      => $params['user_id'],
            'start_time'    => $params['start_time'],
            'end_time'      => $params['end_time'],
            'dowhat'        => $params['dowhat'],
            'color'         => $params['color'],
            'others'        => $params['others'],
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

    /**
     * 删除- 更新状态
     */
    public static function updateStatusByTimeId($params) {

        if (!isset($params['time_id'])){
            return FALSE;
        }

        $sql = "UPDATE " . self::$table . " SET status=0";
        $sqlData = array();

        $sql .= " WHERE time_id = :_time_id";
        $sqlData['_time_id'] = $params['time_id'];

        return DBGateHelper::getConn()->write($sql, $sqlData);
    }

} 
