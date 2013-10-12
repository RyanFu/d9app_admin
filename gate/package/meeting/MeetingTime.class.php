<?PHP
namespace Gate\Package\Meeting;

/**
 * 会议室预定
 * @author hepang@meilishuo.com
 * @since 2013-09-04
 */

use \Gate\Package\Helper\DBGateHelper;

class MeetingTime {

    private static $instance;

    private static $table = 'v_crab_meeting_time';

    private static $col = 'book_id, room_id, founder_user_id, user_id, meeting_topic, start_time, end_time';

    private static $fields = array(
            'map_id'       => 0,
            'book_id'      => 0,
            'user_id'      => 0,
            'status'       => 0,
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
     * 查询会议室时间
     * @param array $id
     * @access public
     */
    public static function getDataById($id = array(), $start_time = '', $end_time = ''){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 0 ";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sqlRoom = " and room_id IN ({$idStr}) ";
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

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

    /**
     * 查询用户时间
     * @param array $id
     * @return string $start_time
     * @return string $end_time
     * @access public
     */
    public static function getDataByUser($id = array(), $start_time = '', $end_time = ''){

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 0 ";
        $sqlData = array();

        if (!empty($id)) {
            !is_array($id) && $id = array($id);
            $idStr = "'" . implode("','", $id) . "'";
            $sqlRoom = " and user_id IN ({$idStr}) ";
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

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

    /**
     * 查询预定时间
     * @param array $id
     * @access public
     */
    public static function getDataByBook($id = array()){

        if (empty($id)) {
            return FALSE;
        }

        $sql = ' SELECT ' . self::$col . ' FROM ' . self::$table . " WHERE 1 ";
        $sqlData = array();

        !is_array($id) && $id = array($id);
        $idStr = "'" . implode("','", $id) . "'";
        $sql .= " and book_id IN ({$idStr}) ";

        $result = DBGateHelper::getConn()->read($sql, $sqlData);//var_dump($sql, $sqlData, $result);
        return $result;
    }

} 
