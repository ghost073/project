<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * 工具类
 *
 * @author shaozhigang
 * @date   2015/01/08
 */
class tool_model extends CI_Model {

    public function __construct() {
       
    }
    
    /**
     * 采集获取内容
     *
     */
    public function fetchUrl($url, $timeout=10, $post = array()) {
        $ci = curl_init();   //初始化一个CURL的会话
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout); 
        curl_setopt($ci, CURLOPT_HEADER, false);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);

        // POST方式提交数据
        if (!empty($post)) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            curl_setopt($ci, CURLOPT_POSTFIELDS, $post);
        }

        $temp=curl_exec($ci);//执行CURL会话
        curl_close($ci);
        return $temp;
    }
    
    /**
     * 后台分页
     * 
     * @param  [type] $base_url  
     * @param  [type] $total     [description]
     * @param  [type] $page_size [description]
     * @return [type]            [description]
     */
    public function adminPage($base_url, $total, $page_size) {
        // 引入分页类
        $this->load->library('pagination');
        // 分页配置
        $page_config = array();
        $page_config['base_url']             = $base_url;
        $page_config['use_page_numbers']     = TRUE;
        $page_config['query_string_segment'] = 'p';
        $page_config['page_query_string']    = TRUE;
        $page_config['anchor_class']         = 'class="number"';
        $page_config['prev_link']            = '上一页';
        $page_config['next_link']            = '下一页';
        $page_config['first_link']           = '首页';
        $page_config['last_link']            = '末页';
        $page_config['total_rows']           = $total;
        $page_config['per_page']             = $page_size;
        // 初始化分页类
        $this->pagination->initialize($page_config);
        // 生成分页
        $page_str = $this->pagination->create_links();

        // 当前页
        $page = $this->pagination->cur_page;

        $page_arr = array(
            'page_str' => $page_str,
            'page'     => $page,
        );

        return $page_arr;
    }

    /**
     * 获得核心系统数据
     * 
     * @param  [type] $url     url
     * @param  [type] $timeout 超时时间
     * @param  array  $post    请求数据
     * 
     * @return [type]          [description]
     */
    public function fetchDataform($url, $timeout = 3, $post = array()) {
        $data_string = json_encode($post);
        $time = time();
        // 加密
        $sign = 1;

        $url .= '&time='.$time.'&secretkey='.$sign;

        $ch = curl_init();   //初始化一个CURL的会话
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // POST方式提交数据
        if (!empty($post)) {
            $header = array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: '.strlen($data_string)
            );
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        //执行CURL会话
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $res;
    }
}