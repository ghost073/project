<?php
if (PHP_SAPI != 'cli' || !isset($argv)) {
    header('Location:/');
    exit;
}

// 自定义数据
// 需要发送邮件的用户
$gMAIL_USER = array(
	'1@qq.com',	// shaozhigang
);

// redis服务器配置
$gREDIS_SERVER = array(
	'services' => array(
		array(
			'name' => '127本地测试服务器',	// 服务器名称
			'host' => '127.0.0.1',	// 服务器地址
			'port' => '6379',	// 端口号
			'max_use_memory' => 6*1024*1024*1024,	// 允许最大使用内存 字节
		),
);

class daemon_jkredis extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * 服务器信息
     * 
     */

    function index() {
        // 发送邮件的用户， redis服务器信息
        global $gMAIL_USER, $gREDIS_SERVER;
        if (empty($gMAIL_USER) || empty($gREDIS_SERVER)) {
            exit('请先配置文件');
        }

        //  引入所需文件
        $this->load->helper(array('functions'));

        // redis服务器配置信息
        $services = isset($gREDIS_SERVER['services']) ? $gREDIS_SERVER['services'] : array();

        // 发送的内容
        $info_arr = array();

        foreach ($services as $key => $server) {

            $redis = new Redis();

            // 连接redis服务器
            try {
               $redis->connect($server['host'], $server['port']);
            } catch (Exception $e) {
                die('ERROR: Could not connect to Redis ('.$server['host'].':'.$server['port'].')');
            }

            // 认证信息
            if (isset($server['auth']) && ($server['auth'] !== '')) {
                if (!$redis->auth($server['auth'])) {
                    die('ERROR: Authentication failed ('.$server['host'].':'.$server['port'].')');
                }
            }

            // 各库信息
            $db_arr = array();

            // redis服务器信息
            $rinfo = $redis->info();
            foreach ($rinfo as $k => $v) {
                // 格式化KEY 为可读
                $nk = preg_replace('/[\s\n]/', '', $k);
                $nk = str_replace(array('#Server','#Stats', '#Clients', '#Memory', '#Persistence', '#Replication', '#CPU', '#Keyspace'), '', $nk);
                $rinfo[$nk] = $v;

                // 以db关键字开头的各库
                if (0 === strpos($nk, 'db')) {
                    $db_arr[$nk] = $v;
                }
            }

            // 组装需要的数据
            $tmp                     = array();
            $tmp['name']             = $server['name']; // 服务器名
            $tmp['host']             = $server['host']; // 服务器地址
            $tmp['version']          = $rinfo['redis_version']; // redis版本
            $tmp['memory_use']       = intval($rinfo['used_memory']); // 内存使用量
            $tmp['memory_use_human'] = $rinfo['used_memory_human']; // 内存使用量 可读
            $tmp['max_use_memory']   = intval($server['max_use_memory']); // 内存阀值
            $tmp['uptime_in_days']   = $rinfo['uptime_in_days']; // 服务开启天数
            $tmp['dbs'] = $db_arr;  // 各库信息
  
            // 关闭reids
            $redis->close();

            $info_arr[$key] = $tmp;
        }

        // 发送的邮件内容
        $msg = '<table border="1" style="border-collapse: collapse; width:1000px;">';
        $msg.= '<tr><th>服务器名称</th><th>地址</th><th>redis版本</th><th>内存使用量</th><th>服务天数</th><th>库信息</th></tr>';
        // 发送的邮件标题
        $subject = 'redis 内存使用监控';

        // 循环发送邮件
        foreach ($info_arr as $key => $val) {
            
            // 发送的邮件内容
            $msg .= '<tr>';
            $msg .= '<td>'.$val['name'].'</td>';
            $msg .= '<td>'.$val['host'].'</td>';
            $msg .= '<td>'.$val['version'].'</td>';

            // 超过阀值，标红
            if ($val['memory_use'] > $val['max_use_memory']) {
                $msg .= '<td style="color:red">'.$val['memory_use_human'].'</td>';    
            } else {
                $msg .= '<td>'.$val['memory_use_human'].'</td>';
            }

            $msg .= '<td>'.$val['uptime_in_days'].'天</td>';
            
            if (!empty($val['dbs'])) {
                $msg .= '<td>';
                foreach ($val['dbs'] as $dk=>$dv) {
                    $msg .= $dk . ' : ' . $dv . "<br />";
                }
                $msg .= '</td>';
            }
            
            $msg .= '</tr>';
        }
        $msg.= '</table>';

        // 发送邮件
        $this->sendEmail($gMAIL_USER, $subject, $msg);
        echo 'ok';
    }


    /**
     * 发送邮件
     * 
     * @param  array    $to      接收人邮件数组
     * @param  string   $subject 主题
     * @param  string   $msg     内容
     * 
     * @return bool
     */
    private function sendEmail(array $to, $subject, $msg) {
        if (empty($to) || empty($subject) || empty($msg)) {
            return false;
        }

        // 用户存在执行发送邮件操作
        $this->load->library(array('phpmail'));
        $email_res = $this->phpmail->sendMail($to, $subject, $msg);
        return $email_res;
    }
}
