<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 创建表相关表
*
* @author   shaozhigang
* @date     2014/07/26
*/
class create_table extends CI_Model {

    private $table_name = '';

    public function __construct(){
        parent::__construct();
    }
    /**
     * 获得月表名 ,201401 格式
     *
     * @param int $time
     *
     */
    public function getMonthTableYm($time) {
        $time = is_numeric($time) ? date('Ym', $time) : date('Ym', strtotime($time));
        if ($this->table_name == '') {
            exit('table_name must set');
        }
        return sprintf($this->table_name,$time);
    } 

    /**
     * 获得年表名 ,2014 格式
     *
     * @param int $time
     *
     */
    public function getYearTableY($time) {
        $time = is_numeric($time) ? date('Y', $time) : date('Y', strtotime($time));
        if ($this->table_name == '') {
            exit('table_name must set');
        }
        return sprintf($this->table_name,$time);
    }   
    
    /**
     * 设置表名前缀
     *
     * @param string $fix
     *
     */
    public function setTableFix($fix) {
        $this->table_name = $fix;
    }
    /**
     * 定时建detail相关表 都为月相关表 201401后缀
     *
     */
    public function creaTableYm($sql_file) {
        if (!file_exists($sql_file)) {
            return false;
        }
        $sql = file_get_contents($sql_file);
        
        $db     = $this->load->database('default', true);
        if ($db === false) {
            return false;
        }
        $year  = date('Y');
        $month = date('n');
        for ($i = -1; $i <= 5; $i++) {
            $time = mktime(0, 0, 0, $month+$i, 1, $year);
            // 获得201401格式表名
            $table_name = $this->getMonthTableYm($time);

            //存在表 跳过
            if ($this->isExistTable($table_name)) {
                continue;
            }
            $suff = date('Ym', $time);
            $sql2  = sprintf($sql, $suff);
            $db->query($sql2);
        }
        $db->close();
        return true;
    }

    /**
     * 检查是否存在表
     *
     * @param string $table
     */
    public function isExistTable($table) {
        $db     = $this->load->database('default', true);
        if ($db === false) {
            return false;
        }
        $sql = "show tables like '{$table}'";
        $query  = $db->query($sql);
        $res = $query->row_array();
        return $res;
    }
    
    /**
     * 定时建detail相关表 都为年相关表 2014后缀
     *
     */
    public function creaTableY($sql_file) {
        if (!file_exists($sql_file)) {
            return false;
        }
        $sql = file_get_contents($sql_file);
        
        $db     = $this->load->database('default', true);
        if ($db === false) {
            return false;
        }

        $year  = date('Y');
        $month = date('n');
    
        // 下一个月的年份
        $time = mktime(0, 0, 0, $month+1, 1, $year);
        // 获得201401格式表名
        $table_name = $this->getYearTableY($time);

        //存在表 跳过
        if ($this->isExistTable($table_name)) {
            return false;
        }
        $suff = date('Y', $time);
        $sql2  = sprintf($sql, $suff);
        $db->query($sql2);
        
        $db->close();
        return true;
    }
}