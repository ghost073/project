<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
* 常用MODEL数据
*
* @author   shaozhigang
* @date     2014/04/11
*/
include_once SERVER_ROOT.'/system/core/xredis.php';
class Model extends CI_Model {
    // 表名
    private $table_name = 'test';
    
    public function __construct() {
        parent::__construct();
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
            $like_name = trim(mysql_escape_string($where['like:name']));
            if (!empty($like_name)) {
                $sql_arr[] = "`name` LIKE '%{$like_name}%'";
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
     * 生成group by sql语句
     * 
     * @param  array    $where  传进来的参数条件
     * 
     * @return string   sql语句
     */
    private function makeGroupSql($where) {
        if (!isset($where['groupby']) || (empty($where['groupby']))) {
            return '';
        }

        $sql_arr = array();

        foreach ($where['groupby'] as $val) {
            $val = trim(mysql_escape_string($val));
            $sql_arr[] = "{$val}";
        }

        $sql = '';
        if (count($sql_arr) > 0) {
            $sql = ' GROUP BY ' . "`" . implode('`,`', $sql_arr) . "`";
        }

        return $sql;
    }
    
    /**
    * 组装LIMIT sql
    *
    * @param    int $page       当前页
    * @param    int $page_size  每页条数
    *
    * @return   string  limit语句
    */
    private function makeLimitSql($page = 0, $page_size = 0) {
        $sql = '';
        $page = intval($page);
        $page_size = intval($page_size);
        // 分页返回数据
        if (($page > 0) && ($page_size > 0)) {
            $start = ($page - 1) * $page_size;
            $start = max($start, 0);
            $sql .= " LIMIT {$start},{$page_size}";
        }
        
        return $sql;
    }
    
    ############### 单表 ####################
    /**
    * 批量插入统计数据
    *
    * @param   array   $data       要插入的数据
    *
    * @return  bool            
    */
    public function addDataBatch($data) {
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
            foreach($val as $kk => $vv) {
                $data2[] = $db->escape_str($vv);
            }            
            $values[] = "('".implode("','", $data2)."')";
        }
        
        $value2 = implode(',', $values);
        $sql = "INSERT IGNORE INTO `{$this->table_name}` ({$keys}) values {$value2}";
        $query  = $db->query($sql);
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
        if (empty($data)) {
            return false;
        }
        
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        foreach ($data AS $key=>$val) {
            $data[$key] = $db->escape_str($val);
        }
        $key_str = "`" . implode("`,`", array_keys($data)) . "`";
        $val_str = "'" . implode("','", $data) . "'";
        $sql = "INSERT INTO `{$this->table_name}` ({$key_str}) VALUES ({$val_str})";
        $query = $db->query($sql);
        $res = $db->insert_id();
        $db->close();
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
        $id = intval($id);
        if (empty($data) || ($id < 1)) {
            return false;
        }
        $db = $this->load->database('default', TRUE);
        if (FALSE === $db) {
            return false;
        }
        
        $set_arr = array();
        foreach ($data as $key=>$val) {
            $val = $db->escape_str($val);
            $set_arr[] = "`{$key}`='{$val}'";
        }
        $set_str = implode(',', $set_arr);
        $sql = "UPDATE `{$this->table_name}` SET {$set_str} WHERE `id`='{$id}'";
        $query = $db->query($sql);
        $db->close();
        return true;
    }
    
   
    /**
     * 根据ID 删除统计数据
     * 
     * @param  int     $id 	  	要删除的ID
     * 
     * @return bool 			删除是否成功
     */
    public function delDataById($id) {
    	$id = intval($id);
    	if ($id < 1) {
    		return false;
    	}

    	$db = $this->load->database('default', true);
    	if (false === $db) {
    		return false;
    	}

    	$sql = "DELETE FROM `{$this->table_name}` WHERE `id`='{$id}'";
    	$query = $db->query($sql);
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
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        $where_sql = $this->makeWhereSql($where);
        $groupby_sql = $this->makeGroupSql($where);
        $orderby_sql = $this->makeSortSql($where);
        $limit_sql = $this->makeLimitSql($page, $page_size);
       
        $sql = "SELECT * FROM `{$this->table_name}`";
        $sql.= $where_sql . $groupby_sql . $orderby_sql .  $limit_sql;

        $query = $db->query($sql);
        $res = array();

        foreach ($query->result_array() as $key=>$val) {
            $res[$val['id']] = $val;
        }

        $db->close();
        return $res;
    }
    
    /**
    * 根据id获得统计数据
    *
    * @param    int         $id     要查询的ID
    *
    * @return   array   
    */
    public function getDataById($id) {
        $id = intval($id);
        if ($id < 1) {
            return false;
        }
        
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        $sql = "SELECT * FROM `{$this->table_name}` WHERE `id`='{$id}'";
        $query = $db->query($sql);
        $res = array();
        $row = $query->row_array();
        $db->close();
        return $row;
    }
    
    /**
    * 根据条件获得统计条数
    *
    * @param    array       $where      条件数组
    *
    * @return   array   
    */
    public function getDataTotalBy($where) {
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        $where_sql = $this->makeWhereSql($where);
        $sql = "SELECT COUNT(*) AS `new_total` FROM `{$this->table_name}`";
        $sql.= $where_sql;
        $query = $db->query($sql);
        $res = 0;
        $row = $query->row_array();
        if (!empty($row)) {
            $res = intval($row['new_total']);
        }
        $db->close();
        return $res;
    }
    
    /**
     * 更改统计某个值+-操作
     * 
     * @param  int 	 $id  	统计ID
     * @param  int   $num 	变化的值
     * @param  string $op   操作： add:添加 dev:减少
     * 
     * @return bool
     */
    public function chgDataById($where, $id) {
        // 参数过滤
        $id = intval($id);
        if (($id < 1) || (empty($where))) {
            return false;
        }

        $set_arr = array();

        // 回复数
        if (isset($where['replies'])) {
            $replies = intval($where['replies']);
            $set_arr[] = "`replies`=`replies`+{$replies}";
        }

        // 浏览数
        if (isset($where['views'])) {
            $views = $db->escape_str($where['views']);
            $set_arr[] = "`views`=`views`+{$views}";
        }
        
        if (count($set_arr) < 1) {
            return false;
        }
        
        $set_sql = implode(",", $set_arr);
        
        $sql = "UPDATE `{$this->table_name}` SET {$set_sql}";
        $sql.= " WHERE `id`='{$tid}'";
        
        $db = $this->load->database('default', true);
        if (false === $db) {
            return false;
        }
        $query = $db->query($sql);
        $db->close();
        return true;
    }
    
    ############### 缓存中获得数据 #################
    /**
    * 根据条件获得统计ID
    *
    * @param    array       $where      条件数组
    * @param    int         $page       当前页
    * @param    int         $page_size  每页显示多少条
    *
    * @return   array   
    */
    public function getDataIdsBy($where, $page=0, $page_size=0) {
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        $where_sql = $this->makeWhereSql($where);
        $order_sql = $this->makeSortSql($where);
        $sql = "SELECT `id` FROM `{$this->table_name}`";
        $sql.= $where_sql.$order_sql;

        $page = intval($page);
        $page_size = intval($page_size);
        if (($page > 0) && ($page_size > 0)) {
            $start = ($page - 1) * $page_size;
            $start = max($start, 0);
            $sql .= " LIMIT {$start},{$page_size}";
        }
        $query = $db->query($sql);
        $id_arr = array();
        
        foreach ($query->result_array() as $key=>$val) {
            $id_arr[] = $val['id'];
        }

        $db->close();
        return $id_arr;
    }
    
     /**
     * 通过id数组获得对应的统计信息
     * 
     * @param  array  $id_arr   统计ID数组
     * @param  bool   $is_cache 是否缓存中取数据
     * 
     * @return array            电影数据
     */
    public function getDataByIds(array $id_arr, $is_cache = TRUE) {
        if (empty($id_arr)) {
            return array();
        }

        foreach ($id_arr as $key => $value) {
            $value = intval($value);
            if ($value < 1) {
                unset($id_arr[$key]);
                continue;
            }
            
            $id_arr[$key] = $value;         
        }

        if (empty($id_arr)) {
            return array();
        }

        // 去重，重排键名
        $ids = $id_arr = array_values(array_unique($id_arr));

        $redis = new XRedis();
        $redis->mconnect(REDIS_TVHOME, 1);
        $cache_key = 'aaa_bbb_';

        $result = array();

        if ($is_cache === TRUE) {
            $result = $redis->getRLists(&$ids, $cache_key);
            if (($result !== false) && (empty($ids))) {
                $redis->close();
                return $result;
            }
        }

        $db = $this->load->database('default', true);
        if ($db === false) {
            return false;
        }

        $id_str = is_array($ids) ? "'" . implode("','", $ids) . "'" : $ids;
        $sql = "SELECT * FROM `{$this->table_name}` WHERE `id` IN ({$id_str})";
        $query = $db->query($sql);
        $result2 = array();
        
        foreach ($query->result_array() as $key => $val) {
            $result2[$val['id']] = $val;
        }
        
        $db->close();
        $cache_time = 3600;
        $redis->setRLists($result2, $cache_key, $cache_time);
        $redis->close();
        // 合并数据
        $result = $result + $result2;
        
        // 按参数中id顺序返回
        $res = array();
        foreach ($id_arr as $id) {
            if (isset($result[$id])) {
                $res[$id] = $result[$id];
            }
        }
        return $res;
    }
    
    /**
    * 根据id获得统计数据
    *
    * @param    int         $id         要查询的ID
    * @param    bool        $is_cache   是否使用缓存
    * @return   array   
    */
    public function getDataById($id, $is_cache = TRUE) {
        $id = intval($id);
        if ($id < 1) {
            return false;
        }
        $result = $this->getDataByIds(array($id), $is_cache);
        $res = array();
        if (!empty($result) && isset($result[$id])) {
            $res = $result[$id];
        }

        return $res;
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
        // 返回的数据
        $res = array();
        // 根据条件获得ID数组
        $id_arr = $this->getDataIdsBy($where, $page, $page_size);
        
        if (empty($id_arr)) {
            return $res;
        }
        
        // 根据ID数组获得数据信息
        $res = $this->getDataByIds($id_arr, TRUE);
        return $res;
    }
    
    /**
    * 根据时间，统计日期类型查询统计数据
    *
    * @param    string  $day    日期
    * @param    int     $day_type   日期类型
    * @param    int     $limit      取多少条数据
    * @return   array
    */
    public function getDataStaAll($day, $day_type = BF_PLAYGAME_DAY, $limit = 100) {
        $day = trim($day);
        $day_type = intval($day_type);
        if (empty($day) || ($day_type < 1)) {
            return false;
        }   
        
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        $day = $db->escape_str($day);
        
        $limit = intval($limit);
        
        $sql = "SELECT `game_id`, SUM(`user_num`) AS `sum_user_num`,SUM(`open_time`) AS `sum_open_time`, SUM(`open_num`) AS `sum_open_num`";
        $sql.= " FROM `{$this->table_name}`";
        $sql.= " WHERE `day`='{$day}' AND `day_type`='{$day_type}'";
        $sql.= " GROUP BY `game_id`";
        $sql.= " ORDER BY `sum_open_time` DESC";
        $sql.= " LIMIT {$limit}";

        $query = $db->query($sql);
        
        $res = array();
        
        foreach ($query->result_array() as $key => $val) {
            $tmp = array();
            $tmp['game_id'] = intval($val['game_id']);
            $tmp['user_num'] = intval($val['sum_user_num']);
            $tmp['open_time'] = intval($val['sum_open_time']);
            $tmp['open_num'] = intval($val['sum_open_num']);
            $res[$val['game_id']] = $tmp;
        }
        
        $db->close();
        
        return $res;
    }
}