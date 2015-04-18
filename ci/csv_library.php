<?php
/**
* 输出CSV 文件
*
* @author   shaozhigang
* @date     2014/04/11     
*/
class Csv {
    // 文件名
    private $filename = '';
    // 文件句柄
    private $fp;
    
    public function __construct() {
        
    }
    
        //某一天启动明细
    public function start_detail() {
        // 获得参数
        $appid  = intval($this->input->get('id'));
        // 日期
        $day    = trim($this->input->get('day'));
        if (($appid < 1) || (empty($day))) {
            show_404();
        }
        // 当天日期
        $now_day = date('Y-m-d');
        // 最小日期
        $min_day = '2014-02-01';
        // 不在时间范围内日期修正
        if ((strcasecmp($day, $now_day)>0) || (strcasecmp($day, $min_day)<0)) {
            $day = $now_day;
        }
        // 引入所需MODEL
        $this->load->model('app_info_model');
        $this->load->model('app_start_model');

        //根据appid 获得app信息
        $appinfo = $this->app_info_model->app_info_model->getAppInfoByIds(array($appid));
        //不存在的app
        if (!isset($appinfo[$appid]) && !$appinfo[$appid]) {
            exit('no found app');
        } 
        $appinfo = $appinfo[$appid];
        
        // 应用平台
        $os_type = ($appinfo['os_type']==1)?'苹果':'安卓';
        // 文件名
        $filename = $day.'-'.$os_type.'-'.$appinfo['name'].'-用户详情.csv';
        
        //csv输出头
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        
        // 标题数组
        $title_arr = array(
            0=>'日期',
            1=>'安卓MAC (IOS IDFA)',
            2=>'用户IP',
            3=>'启动次数',
            4=>'SDK版本',
            5=>'登陆时间',
        );
        // 转换成gbk
        foreach ($title_arr as $key=>$val) {
            $title_arr[$key] = mb_convert_encoding($val, 'gbk', 'utf-8');
        }

        // 输出标题
        fputcsv($fp, $title_arr);
        
        // 获得查询日期启动数据条数
        $start_total = $this->app_start_model->getStartNumByAppDay($appid, $day);
        // 启动总数小于1直接退出
        if ($start_total < 1) {
            // 关闭文件句柄
            fclose($fp);
            exit;
        }
        
        // 计数器
        $cnt = 0;
        // 每隔$max_cnt行，刷新一下输出buffer
        $max_cnt = 5000;
        
        // 每页返回条数
        $page_size = 500;
        // 最大页数
        $max_page = ceil($start_total/$page_size);
        for ($page=1;$page<=$max_page;$page++) {
            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($cnt >= $max_cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }
            
            // 偏移量
            $offset = ($page-1)*$page_size;
            // 获取查询日期启动数据
            $start_data = $this->app_start_model->getStartListByAppDay($appid, $day, $offset, $page_size);
            // 如果数据为空进行下一个循环
            if (empty($start_data)) {
                continue;
            }
            // 循环拼装数据，key值与title数组对应
            foreach ($start_data as $val) {
                // 计数器+1
                $cnt++;
                
                $content_arr = array(
                    0 => $val['day'],       // 日期
                    1 => $val['famac'],     // 安卓MAC (IOS IDFA)
                    2 => $val['ip_address'],// 用户IP
                    3 => $val['total'],     // 启动次数
                    4 => $val['version'],   // SDK版本
                    5 => $val['ctime'],     // 登陆时间
                );
                // 写入输出
                fputcsv($fp, $content_arr);
            }
            // 销毁不用变量
            unset($start_data);
        }
        // 关闭文件句柄
        fclose($fp);
        exit;
    }
}