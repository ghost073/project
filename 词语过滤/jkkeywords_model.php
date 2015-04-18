<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * 屏蔽词model
 *
 * @author shao
 * @date   2015/01/16 
 */
include_once SERVER_ROOT . '/system/core/xredis.php';
class jkkeywords_model extends CI_Model {

	private $table_name = 'jkkeywords';

	public function __construct() {
		parent::__construct();
	}
    
    /**
    * 插入单条屏蔽词数据
    *
    * @param    array   $data       要插入的数据
    *
    * @return   int                 最后插入的ID
    */
    public function addJkkeywords($data) {
        if (empty($data)) {
            return false;
        }

        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        // 参数过滤
        foreach ($data AS $key=>$val) {
            $data[$key] = $db->escape_str($val);
        }
        
        $key_str = "`" . implode("`,`", array_keys($data)) . "`";
        $val_str = "'" . implode("','", $data) . "'";
        $sql = "INSERT IGNORE INTO `{$this->table_name}` ({$key_str}) VALUES ({$val_str})";
        $query = $db->query($sql);
        $res = $db->insert_id();
        //更新缓存
        $this->getAllJkwordByte(false);
        $db->close();
        return $res;
    }
    /**
     * 批量添加关键词 二维数组 
     *
     * @param arary $data 关键词
     *
     */
    public function addKeyWords($data) {
        if (!$data) {
            return fasle;
        }
        $keys   = '';
        $result = array();
        foreach ($data as $key => $val) {
            if (!$keys) {
                $keys = array_keys($val);
            }
            foreach ($val as $k => $v) {
                $v = mysql_escape_string($v);
                $val[$k] = $v;
            }
            $result[] = "('" . implode("','", $val) . "')";
        }
        $key_str = "`".implode('`,`', $keys)."`";
        $val_str = implode(',', $result);
        $sql   = "INSERT IGNORE INTO `{$this->table_name}` ({$key_str}) VALUES {$val_str}";
        $db    = $this->load->database('default', TRUE);
        $query = $db->query($sql);
        //更新缓存
        $this->getAllJkwordByte(false);
        return true;
    }
    
    /**
    * 根据条件获得屏蔽词
    *
    * @param    array       $where      条件数组
    * @param    int         $page       当前页
    * @param    int         $page_size  每页显示多少条
    *
    * @return   array   
    */
    public function getJkkeywordsBy($where, $page=0, $page_size=0) {
        $db = $this->load->database('default', TRUE);
        if (false === $db) {
            return false;
        }
        
        $where_sql = $this->makeWhereSql($where);
        $sort_sql = $this->makeSortSql($where);
        $sql = "SELECT * FROM `{$this->table_name}`";
        $sql.= $where_sql;
        $sql.= $sort_sql;

        $page = intval($page);
        $page_size = intval($page_size);
        if (($page > 0) && ($page_size > 0)) {
            $start = ($page - 1) * $page_size;
            $start = max($start, 0);
            $sql .= " LIMIT {$start},{$page_size}";
        }
        $query = $db->query($sql);
        $res = array();

        foreach ($query->result_array() as $key=>$val) {
            $res[$val['id']] = $val;
        }
        $db->close();
        return $res;
    }
    
    /**
    * 根据条件获得屏蔽词条数
    *
    * @param    array       $where      条件数组
    *
    * @return   array   
    */
    public function getJkkeywordsTotalBy($where) {
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
     * 根据ID 删除屏蔽词
     * @author shaozhigang
     * @date   2014-06-09
     * 
     * @param  int     $id 	  	要删除的ID
     * 
     * @return bool 			删除是否成功
     */
    public function delJkkeywordsById($id) {
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
     * 生成order by 语句
     * 
     * @param  array    $where  传进来的参数条件
     * @param  obj      $db     数据库链接资源，用来过滤
     *
     * @return string   sql语句
     */
    private function makeSortSql($where) {
        if (!isset($where['orderby']) || (empty($where['orderby']))) {
            return '';
        }

        $sql_arr = array();

        foreach ($where['orderby'] as $key => $val) {
            $key = mysql_escape_string($key);
            $val = mysql_escape_string($val);
            $sql_arr[] = "`{$key}` {$val}";
        }

        $sql = '';
        if (count($sql_arr) > 0) {
            $sql = ' ORDER BY ' . implode(',', $sql_arr);
        }

        return $sql;
    }
    
    /**
    * 组装SQL
    *
    * @param    array   $data   要组装的数据
    * @param    obj   $db     数据库链接资源，用来过滤
    *
    * @return   string          组装完成的SQL
    */
    private function makeWhereSql($where) {
        if (empty($where)) {
            return '';
        }
        $sql_arr = array();

        $sql = '';
        if (count($sql_arr) > 0) {
            $sql = ' WHERE ' . implode(' AND ', $sql_arr);
        }
        return $sql;
    }
    
    /**
    * 所有监控的数据
    *
    * @param    bool    $is_cache   是否缓存
    *
    * @return   array
    */
    public function getAllJkword($is_cache = true) {
        $redis = new XRedis();
        $redis->mconnect(REDIS_TIEBA2, 1);
        $cache_key = 'tieba_jkkeyword';

        $result = array();

        if ($is_cache === TRUE) {
            $result = $redis->getArray($cache_key);
            if ($result !== false) {
                $redis->close();
                return $result;
            }
        }
        
        $where = array();
        // 屏蔽词条数
        $total = $this->getJkkeywordsTotalBy($where);
        if ($total > 0) {
            // 分批获得屏蔽词数据存入数组
            $page_size = 500;
            $max_page = ceil($total/$page_size);
            $where['orderby'] = array(
                'id' => 'asc',
            );
            for ($page=1; $page<=$max_page; $page++) {
                // 屏蔽词
                $word_arr = $this->getJkkeywordsBy($where, $page, $page_size);
                foreach ($word_arr as $val) {
                    $result[] = $val['keyword'];
                }
            }
        }
        
        $cache_time = 3600*6;
        $redis->setArray($cache_key, $result, $cache_time);
        $redis->close();
        return $result;
    }
    
    /**
    * 按字节编译关键词
    *
    * @param    bool    $is_cache   是否缓存
    *
    * @return   array
    */
    public function getAllJkwordByte($is_cache = true) {
        $redis = new XRedis();
        $redis->mconnect(REDIS_TIEBA2, 1);
        $cache_key = 'tieba_jkkeywordbyte';

        $result = array();

        if ($is_cache === TRUE) {
            $result = $redis->getArray($cache_key);
            if ($result !== false) {
                $redis->close();
                return $result;
            }
        }
        
        $where = array();
        // 屏蔽词条数
        $total = $this->getJkkeywordsTotalBy($where);
        if ($total > 0) {
            // 分批获得屏蔽词数据存入数组
            $page_size = 500;
            $max_page = ceil($total/$page_size);
            $where['orderby'] = array(
                'id' => 'asc',
            );
            for ($page=1; $page<=$max_page; $page++) {
                // 屏蔽词
                $word_arr = $this->getJkkeywordsBy($where, $page, $page_size);
                
                // 按字节编译数据
                foreach ($word_arr as $val) {
                    
                    $keyword = $val['keyword'];
                   
                    if ($keyword === '') {
                        continue;
                    }
                    $temp='$result';
                    $len=strlen($keyword);
                    
                    for($i=0;$i<$len;$i++){
                        $temp.="['key']['".$keyword{$i}."']";
                    }
                    // 有些过滤词繁体字gbk解析不了会报错
                    @eval($temp.="['val']=1;");
                }
                
            }
        }
        
        $cache_time = 3600*6;
        $redis->setArray($cache_key, $result, $cache_time);
        $redis->close();
        return $result;
    }
}
