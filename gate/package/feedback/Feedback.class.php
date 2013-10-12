<?PHP
namespace Gate\Package\Feedback;

use \Gate\Package\Helper\DBGateHelper;

class Feedback {

    private static $instance;

    private static $table = 't_crab_user_feedback';

    private static $col = 'feedback_id, user_id, feedback_detail, type, create_time, status';

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self(); 
        }   
        return self::$instance;
    }

    public function __construct() {

    }

    public static function addData($params) {
        if (!isset($params['user_id']) || !isset($params['feedback_detail']))
            return FALSE;

        $sql = 
            'INSERT INTO ' . self::$table . 
            ' (user_id, feedback_detail, type) 
            VALUES (:_user_id, :feedback_detail, :_type)';

        $sql_data = array(
            '_user_id' => empty($params['user_id']) ? 0 : $params['user_id'],
            'feedback_detail' => $params['feedback_detail'],
            '_type' => $params['type'],
        );

        $ret = DBGateHelper::getConn()->write($sql, $sql_data);
        return $ret === FALSE ? FALSE : DBGateHelper::getConn()->getInsertId();
    }
} 
