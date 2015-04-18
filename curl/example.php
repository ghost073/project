<?php
/**
 * curl调用例子
 *
 * @author	幽灵
 * @date	2014/05/15 
 */
set_time_limit(0);
include_once 'cls_curl.php';

// 浏览器头
$GLOBALS['gUSERAGENT'] = array(
	'Netscape &3'           => 'Mozilla/3.0 (Win95; I)',
	'WinPhone7'             => 'Mozilla/4.0 (compatible: MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; SAMSUNG; SGH-i917)',
	'WinPhone7.5'           => 'Mozilla/5.0 (compatible: MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; SAMSUNG; SGH-i917)',
	'&Safari5 (Win7)'       => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
	'Safari6 (Mac)'         => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8) AppleWebKit/536.25 (KHTML, like Gecko) Version/6.0 Safari/536.25',
	'iPad'                  => 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25',
	'iPhone6'               => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A405 Safari/8536.25',
	'IE'                    => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2)',
	'IE &6 (XPSP2)'         => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
	'IE &7 (Vista)', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1)',
	'IE 8 (Win2k3 x64)'     => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.2; WOW64; Trident/4.0)',
	'IE &8 (Win7)'          => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)',
	'IE 8 (IE7 CompatMode)' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0)',
	'IE 9 (Win7)'           => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
	'IE 10 (Win8)'          => 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)',
	'IE 11 (Win8.1)'        => 'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko',
	'Opera'                 => 'Opera/9.80 (Windows NT 6.2; WOW64) Presto/2.12.388 Version/12.11',
	'opera1'                => 'Opera/9.80 (Windows NT 6.1; U; Edition IBIS; zh-cn) Presto/2.10.229 Version/11.60',
	'Firefox 3.6'           => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.7) Gecko/20100625 Firefox/3.6.7',
	'Firefox 24'            => 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0',
	'Firefox Phone'         => 'Mozilla/5.0 (Mobile; rv:18.0) Gecko/18.0 Firefox/18.0',
	'Firefox (Mac)'         => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0',
	'firefox 8'             => 'Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1',
	'google'                => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.77 Safari/534.24',
	'Chrome (Win8)'         => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.8 Safari/537.36',
	'ChromeBook'            => 'Mozilla/5.0 (X11; CrOS armv7l 2913.260.0) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.99 Safari/537.11',
	'GoogleBot Crawler'     => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
	'Kindle Fire (Silk)'    => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_3; en-us; Silk/1.0.22.79_10013310) AppleWebKit/533.16 (KHTML=> like Gecko) Version/5.0 Safari/533.16 Silk-Accelerated=true',
);
// 浏览器头
$GLOBALS['gBROWER_HEADER'] = array(
    'firefox' => array(
        'useragent' => 'Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1',
        'header' => array(
            'Host' => '',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'zh-cn,zh;q=0.5',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Charset' => 'GB2312,utf-8;1=0.7,*;q=0.7',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
    ),
    'ie' => array(
        'useragent' => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2)',
        'header' => array(
            'Host' => '',
            'Accept' => 'image/jpeg, application/x-ms-application, image/gif, application/xaml+xml, image/pjpeg, application/x-ms-xbap, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*',
            'Accept-Language' => 'zh-CN',
            'Accept-Encoding' => 'gzip, deflate',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
    ),
    'google' => array(
        'useragent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.77 Safari/534.24',
        'header' => array(
            'Host' => '',
            'Accept' => 'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
            'Accept-Language' => 'zh-CN,zh;q=0.8',
            'Accept-Encoding' => 'gzip,deflate,sdch',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept-Charset' => 'GBK,utf-8;q=0.7,*;q=0.3',
        ),        
    ),
    'opera' => array(
        'useragent' => 'Opera/9.80 (Windows NT 6.1; U; Edition IBIS; zh-cn) Presto/2.10.229 Version/11.60',
        'header' => array(
            'Host' => '',
            'Accept' => 'text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/webp, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
            'Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8',
            'Accept-Encoding' => 'gzip, deflate',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
    ),
);

class shua
{
    public function __construct()
    {
    
    }
    
    // 随机IP
    public static function random_ip()
    {
        $ip = $sep = '';
        for ($i = 0;$i < 4;$i ++)
        {
            $ip .= $sep . mt_rand(40, 210);
            $sep = '.';
        }
        return $ip;
    }
    
    // 请求baidu.com
    public static function req_baidu()
    {
    	// url
    	$url = 'http://www.baidu.com';
		// curl类
		$cls_curl = new cls_curl();
		// 浏览器头
		// 随机取出一个配置
		$brower_key = array_rand($GLOBALS['gBROWER_HEADER']);
		$brower_arr = $GLOBALS['gBROWER_HEADER'][$brower_key];
		// 浏览器
		$useragent = $brower_arr['useragent'];
		// http header
		$http_header = $brower_arr['header'];
		// 获得服务器返回COOKIE存入cookie文件
		$use_cookie = COOKIE_GET_FILE;
		// 文件 E:/web/loc/curl/1.txt
		$cookie_file = 'E:/web/loc/curl/curl/1.txt';
		$timeout = 3;

		$options = array(
			'return_data' =>TRUE,
			'useragent'   => $useragent,
			'http_header' => $http_header,
			'use_cookie'  => $use_cookie,
			'cookie_file' => $cookie_file,
			'timeout'     => $timeout,
			);
		// 执行请求
		$res = $cls_curl->execOne($url, $options);
		var_dump($res);
    }

    /**
     * 多进程采集，谁先到处理谁, windows下调用有问题
     * 
     * @author shaozhigang
     * @date   2014-05-17
     * 
     * @return [type]     [description]
     */
    public function multi_curl() {

    	$options = array(
    		'return_data' => TRUE,
    		'timeout'     => 15,
    		);

    	$url_arr = array (
	0 		 => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=90048798&matches_requested=20&start_at_match_id=363481940',
	'option' => $options,
	),
	1        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=77886627&matches_requested=20&start_at_match_id=364261559',
	'option' => $options,
	),
	2        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=100992353&matches_requested=20&start_at_match_id=236822815',
	'option' => $options,
	),
	3        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=90048798&matches_requested=20&start_at_match_id=363481940',
	'option' => $options,
	),
	4        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=77886627&matches_requested=20&start_at_match_id=364261559',
	'option' => $options,
	),
	5        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=100992353&matches_requested=20&start_at_match_id=236822815',
	'option' => $options,
	),
	6        => array (
	'url'    => 'http://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=3222C499657AB1BBBEFA3BE5632BB701&account_id=90048798&matches_requested=20&start_at_match_id=363481940',
	'option' => $options,
	),
	);

    	$cls_curl    = new cls_curl();
    	// 批量采集,谁先返回处理谁
    	$cls_curl->execMultiRoll($url_arr, $this, 'callback');	
    }



    public function callback($status, $info, $data)
	{
		// status 不为0 采集出现错误
		if (0 == $status)
		{
			$status_name = 'success';
		}
		else
		{
			$status_name = 'fail';
		}

		// 解析url
		$url_info = parse_url($info['url']);
		// 解析query
		parse_str($url_info['query'], $param_arr);
		// account_id
		$account_id = $param_arr['account_id'];
		// 保存到的文件名
		$file_name = './' . $status_name . '.txt';
		// 打开文件
		$fp = fopen($file_name, 'a+');
		$str = var_export($info, TRUE);
		fwrite($fp, $str);
		fclose($fp);
	}

	// 请求 500.com
    public static function req_500()
    {	
    	// 随机IP
    	$pseudo_proxy_ip = self::random_ip();

    	// url 
    	//$url = 'http://bbs.500.com/?fromuid=17521319';
    	$url = 'http://bbs.tvhome.com/?fromuid=3';
		// curl类
		$cls_curl    = new cls_curl();
		// 浏览器头
		$useragent_key = array_rand($GLOBALS['gUSERAGENT']);
		$useragent = $GLOBALS['gUSERAGENT'][$useragent_key];
		// 随机取出一个配置
		$brower_key  = array_rand($GLOBALS['gBROWER_HEADER']);
		$brower_arr  = $GLOBALS['gBROWER_HEADER'][$brower_key];
		// http header
		$http_header = $brower_arr['header'];
		// 获得服务器返回COOKIE存入cookie文件
		$use_cookie  = COOKIE_GET_FILE;
		// 文件 E:/web/loc/curl/1.txt
		$cookie_file = 'E:/web/loc/curl/curl/req_500_' . $pseudo_proxy_ip . '.txt';
		$timeout = 3;

		$options = array(
			'return_data' => FALSE,
			'useragent'   => $useragent,
			'http_header' => $http_header,
			//'use_cookie'  => $use_cookie,
			'cookie_file' => $cookie_file,
			'timeout'     => $timeout,
			'pseudo_proxy'=> true,
			'pseudo_proxy_ip' => $pseudo_proxy_ip,
			);
		// 执行请求
		$res = $cls_curl->execOne($url, $options);
		var_dump($res);
    }
}

$obj_shua = new shua();
$obj_shua->multi_curl();