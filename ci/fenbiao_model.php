<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
* 分表MODEL数据
*
* @author   shaozhigang
* @date     2014/04/11
*/
class Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
   ###################  分表 ############################
    private $pre_table = 'test_%s';
	/**
	 * 获得分表表名
	 *
	 * @param int|date $time
	 *
	 */
	private function getTable($time) {
		$time = is_numeric($time) ? date('Ym', $time) : date('Ym', strtotime($time));
		return sprintf($this->pre_table, $time);
	}
    
    /**
    * 分表表名周统计
    *
    * @param    mix $time   时间
    *
    */
    private function getTable($time) {
        // 操作日期的周一
        $this->load->library('dateformat');
        // 操作日期的周一
        $monday = $this->dateformat->getMonday($time);
		return sprintf($this->pre_table, date('Ym', strtotime($monday)));
    }
    
    #################### 组装SQL ###################
    /**
    * 组装SQL
    *
    * @param    array   $where      要组装的数据
    *
    * @return   string              组装完成的SQL
    */
    private function makeWhereSql($where) {
        if (empty($where)) {
            return '';
        }
        // 组装成的SQL
        $sql_arr = array();

        // id 
        if (isset($where['id'])) {
            $id = intval($where['id']);
            if ($id > 0) {
                $sql_arr[] = "`id`='{$id}'";
            }
        }
        
        if (isset($where['yu:recome'])) {
            $yu_recome = intval($where['yu:recome']);
            if ($yu_recome > 0) {
                $sql_arr[] = "`recome`&'{$yu_recome}'";
            }
        }
        
        // 电影名
        if (isset($where['like:name'])) {
            $name_like = trim(mysql_escape_string($where['like:name']));
            if (!empty($name_like)) {
                $sql_arr[] = "`name` LIKE '%{$name_like}%'";
            }
        }
        
        // 在appid之中
        if (isset($where['in:appid'])) {
            $appid_arr = $where['in:appid'];
            // appid过滤
            foreach ($appid_arr as $key=>$val) {
                $val = intval($val);
                if ($val<1) {
                    unset($appid_arr[$key]);
                }
            }
            
            if (!empty($appid_arr)) {
                $tmp = "'" . implode("','", $appid_arr) . "'";
                $sql_arr[] = "`appid` IN (" . $tmp . ")";
            }
        }

        // 英文首字母
        if (isset($where['letter'])) {
            $letter = trim(mysql_escape_string($where['letter']));
            if (!empty($letter)) {
                $sql_arr[] = "`letter`='{$letter}'";
            }
        }
        
        // 大于等于开始日期
        if (isset($where['gte:day'])) {
            $gte_day = trim(mysql_escape_string($where['gte:day']));
            if (!empty($gte_day)) {
                $sql_arr[] = "`day`>='{$gte_day}'";
            }
        }
        
        // 小于等于结束日期
        if (isset($where['lte:day'])) {
            $lte_day = trim(mysql_escape_string($where['lte:day']));
            if (!empty($lte_day)) {
                $sql_arr[] = "`day`<='{$lte_day}'";
            }
        }

        // 组装的sql
        $sql = '';
        if (count($sql_arr) > 0) {
            $sql = ' WHERE ' . implode(' AND ', $sql_arr);
        }
        return $sql;
    }

    /**
     * 生成order by 语句
     * 
     * @param  array    $where  传进来的参数条件
     * 
     * @return string   sql语句
     */
    private function makeSortSql($where) {
        if (!isset($where['orderby']) || (empty($where['orderby']))) {
            return '';
        }

        $sql_arr = array();

        foreach ($where['orderby'] as $key => $val) {
            // 参数过滤
            $key = trim(mysql_escape_string($key));
            $val = trim(mysql_escape_string($val));
            $sql_arr[] = "`{$key}` {$val}";
        }

        $sql = '';
        if (count($sql_arr) > 0) {
            $sql = ' ORDER BY ' . implode(',', $sql_arr);
        }

        return $sql;
    }
    
    /**
    * 批量插入多条统计数据,分表
    *
    * @param array $data
    *
    * @return   bool
    */
    public function addDataTableBatch($data) {
        if (!$data) {
            return false;
        }
        $db     = $this->load->database('default', true);
        if ($db === false) {
            return false;
        }
        //过滤字符防sql注入
        $values = array();
        $keys   = '';

        foreach($data as $val) {
            $data2 = array();
            if (!$keys) {
                $keys   = "`".implode("`,`",array_keys($val))."`";
            }
            // 参数过滤
            foreach($val as $kk => $vv) {
                $data2[] = $db->escape_str($vv);
            }
            // 表名对应数据
            $table_name = $this->getTable($val['ctime']);
            $values[$table_name][] = "('".implode("','", $data2)."')";
        }
        // 插入数据
        foreach ($values as $table_name => $val) {
            $value2 = implode(',',$val);
            $sql    = "INSERT IGNORE INTO {$table_name} ({$keys}) VALUES {$value2} ";
            $query  = $db->query($sql);
        }
        // 关闭数据库
        $db->close();
        return true;
    }
    
    /**
    * 插入单条统计数据
    *
    * @param    array   $data       要插入的数据
    *
    * @return   int                 最后插入的ID
    */
    public function addData($data) {
        // 参数判断
        if (empty($data)) {
            return false;
        }
        
        // 分表必须字段
        if (!isset($data['day'])) {
            return false;
        }
        // 获得表名
        $table_name = $this->getTable($data['day']);
        
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        // 参数过滤
        foreach ($data AS $key=>$val) {
            $data[$key] = $db->escape_str($val);
        }
        // key值
        $key_str = "`" . implode("`,`", array_keys($data)) . "`";
        // val值
        $val_str = "'" . implode("','", $data) . "'";
        // sql 
        $sql = "INSERT IGNORE INTO `{$table_name}` ({$key_str}) VALUES ({$val_str})";
        // query
        $query = $db->query($sql);
        // lastid
        $res = $db->insert_id();
        // 关闭数据库
        $db->close();
        // 返回数据
        return $res;
    }
    
    /**
    * 根据ID更新数据
    *
    * @param    array   $data       要更新的数据
    * @param    int     $id         要更新的ID
    *
    * @return   bool
    */
    public function updDataById($data, $id) {
        // 参数判断
        $id = intval($id);
        if (empty($data) || ($id < 1)) {
            return false;
        }
        
        // 分表必须字段
        if (!isset($data['day'])) {
            return false;
        }
        // 获得表名
        $table_name = $this->getTable($data['day']);
        
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (FALSE === $db) {
            return false;
        }
        // 参数过滤
        $set_arr = array();
        foreach ($data as $key=>$val) {
            $val = $db->escape_str($val);
            $set_arr[] = "`{$key}`='{$val}'";
        }
        // set 语句
        $set_str = implode(',', $set_arr);
        // sql 
        $sql = "UPDATE `{$table_name}` SET {$set_str} WHERE `id`='{$id}'";
        // query
        $query = $db->query($sql);
        // 关闭数据库
        $db->close();
        return true;
    }
    
    /**
    * 根据条件获得统计数据
    *
    * @param    array       $where      条件数组
    * @param    int         $page       当前页
    * @param    int         $page_size  每页显示多少条
    *
    * @return   array   
    */
    public function getDataListBy($where, $page=0, $page_size=0) {
         // 分表必须字段
        if (!isset($where['day'])) {
            return false;
        }
        // 获得表名
        $table_name = $this->getTable($where['day']);
        
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        // 查询条件sql
        $where_sql = $this->makeWhereSql($where);
        // 排序条件sql
        $order_sql = $this->makeSortSql($where);
        // sql
        $sql = "SELECT * FROM `{$table_name}`";
        $sql.= $where_sql . $order_sql;
        
        // 分页
        $page = intval($page);
        $page_size = intval($page_size);
        // 分页返回数据
        if (($page > 0) && ($page_size > 0)) {
            // 偏移量
            $start = ($page - 1) * $page_size;
            $start = max($start, 0);
            $sql .= " LIMIT {$start},{$page_size}";
        }
        // 执行SQL
        $query = $db->query($sql);
        // 返回值
        $res = array();

        foreach ($query->result_array() as $key=>$val) {
            $res[$val['id']] = $val;
        }

        // 关闭数据库
        $db->close();
        return $res;
    }
    
    /**
    * 根据条件获得统计条数
    *
    * @param    array       $where      条件数组
    *
    * @return   array   
    */
    public function getDataTotalBy($where) {
        // 分表必须字段
        if (!isset($where['day'])) {
            return false;
        }
        // 获得表名
        $table_name = $this->getTable($where['day']);
        
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        // 查询条件sql
        $where_sql = $this->makeWhereSql($where);
        
        // sql
        $sql = "SELECT COUNT(*) AS `new_total` FROM `{$table_name}`";
        $sql.= $where_sql;
        
        // 执行SQL
        $query = $db->query($sql);
        // 返回值
        $res = 0;
        $row = $query->row_array();
        if (!empty($row)) {
            $res = intval($row['new_total']);
        }
        // 关闭数据库
        $db->close();
        return $res;
    }
    
    /**
    * 根据日期，游戏ID和MAC 获得不凡玩游戏详细信息
    *
    * @param    array       $where      条件数组
    *
    * @return   array   
    */
    public function getDataBySer($day, $game_id, $mac) {
        // 参数过滤
        $day = trim($day);
        $game_id = intval($game_id);
        $mac = trim($mac);
        
        if (empty($day) || ($game_id < 1) || ($mac === '')) {
            return array();
        }
        
        // 获得表名
        $table_name = $this->getTable($day);
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
  
        // sql
        $sql = "SELECT * FROM `{$table_name}`";
        $sql.= " WHERE `day`='{$day}' AND `game_id`='{$game_id}' AND `mac`='{$mac}'";
        $sql.= " LIMIT 1";
        
        // 执行SQL
        $query = $db->query($sql);
        
        $row = $query->row_array();
      
        // 关闭数据库
        $db->close();
        return $row;
    }
    
    /**
    * 根据条件获得不凡玩游戏开启时长和次数
    *
    * @param    array       $where      条件数组
    *
    * @return   array   
    */
    public function getDataSumBy($where) {
        // 参数过滤
        if (!isset($where['day'])) {
            return false;
        }
        // 获得表名
        $table_name = $this->getTable($where['day']);
        
        // 数据库
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        // 查询条件sql
        $where_sql = $this->makeWhereSql($where);
        
        // sql
        $sql = "SELECT SUM(`open_time`) AS `sum_open_time`,SUM(`open_num`) AS `sum_open_num` FROM `{$table_name}`";
        $sql.= $where_sql;
        
        // 执行SQL
        $query = $db->query($sql);
        $row = $query->row_array();
        
        // 关闭数据库
        $db->close();
        return $row;
    }
}