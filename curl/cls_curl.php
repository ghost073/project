<?php
/**
* curl模拟请求类
* 
* @author	shaozhigang
* @date		2013/09/23
*/
// 是否使用cookie常量
define('COOKIE_GET_FILE', 1);	// 获得服务器返回COOKIE存入cookie文件
define('COOKIE_SET_FILE', 2);	// 携带cookie文件中的COOKIE请求
define('COOKIE_SET', 3);	// 使用COOKIE字符串请求

class cls_curl
{
	// 回调对象
	private $obj = null;
	// 回调方法
	private $callback = null;
	
	public function __construct()
	{
		if (!extension_loaded('curl'))
		{
			die('no extension curl!');
		}
	}
	
	/**
	* 执行抓取
	*
	* @param	array	$url	url数组 格式：array(0 => array(url=>1,option=>array()));
	*
	* @return	unknow
	*/	
	public function exec($url_arr)
	{
		if ((FALSE !== empty($url_arr)) || (FALSE === is_array($url_arr)))
		{
			return FALSE;
		}
		if (count($url_arr) > 1)
		{
			$data = $this->execMulti($url_arr);
		}
		else
		{
			$curr_url_arr = current($url_arr);
			$data = $this->execOne($curr_url_arr['url'], $curr_url_arr['option']);
		}
		return $data;
	}
	
    /**
	* 并行抓取所有的内容
    *
	* @param	array	$url_arr	请求的地址及配置数组	
	*								格式：array(0=>array(url=>1,option=>array()), 1=>array(url=>2,option=>array()));
	* 
	* @return	array	返回的数据
	*/
	public function execMulti($url_arr)
    {
        $curl = $data = array();
        $mh = curl_multi_init();
        foreach($url_arr AS $k => $v)
        {
            $curl[$k] = $this->addHandle($mh, $v['url'], $v['option']);
        }

        $this->execMulitHandle($mh);

        foreach($url_arr AS $k => $v)
        {
            $data[$k] = curl_multi_getcontent($curl[$k]);
            curl_multi_remove_handle($mh, $curl[$k]);
			curl_close($curl[$k]);
			unset($curl[$k]);
        }
        curl_multi_close($mh);
        return $data;
    }
    
    /**
	* 只抓取一个网页的内容。
    *
	* @param	string	$url	请求地址
	* @param	array	$options	curl配置选项
	*
	*
	*/
	public function execOne($url, $options = array())
    {
        if (FALSE !== empty($url)) 
		{
            return FALSE;
        }
        $ch = curl_init();
		$options['url'] = $url;
        $this->setOneOption($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
	
	/**
	* 并行抓取对先返回的数据先行处理
	*
	* @param	array	$url_arr	url数组 格式：array(0 => array(url=>1,option=>array()));
	* @param	obj	$obj	回调函数对象
	* @param	string	$callback	回调方法
	* 
	* @return 	unknow
	*/
	public function execMultiRoll($url_arr, $obj = null, $callback = null)
    {
        $curl = $data = array();
        $mh = curl_multi_init();
        foreach($url_arr AS $k => $v)
        {
            $curl[$k] = $this->addHandle($mh, $v['url'], $v['option']);
        }
				
		if (!is_null($obj))
		{
			$this->obj = $obj;
		}
		
		if (!is_null($callback))
		{
			$this->callback = $callback;
		}
		$this->execMulitHandleRoll($mh);
    }
    
    /**
	* 内部函数，设置某个handle 的选项
    *
	* @param	resource		$ch			curl资源
	* @param	array			$options	curl配置方法
	*
	*options参数：
	*	url : 请求URL
	*	return_data : bool [true:返回信息 false:直接输出到浏览器]
	*	port ：链接的端口
	*	pseudo_proxy　：　是否使用伪ip [true:是 false:不使用]
	*	pseudo_proxy_ip : 伪IP地址
	* 	proxy ： 是否使用代理
	* 	proxy_type : 代理类型
	* 	proxy_host ：代理主机地址
	* 	proxy_port : 代理主机端口
	*  	post_data ： POST请求的数据
	*   useragent ：浏览器
	*   followlocation ： 递归的返回给服务器
	*   header ：是否将头信息输出　[true:是 false:否]
	*   ssl ：ssl验证 [true:是 false:否]
	*   connect_time ： 设置链接时间
	*   timeout ：允许执行的最大秒数
	*   gzip　：是否启用zip [true:是 false:否]
	*   use_cookie : 使用cookie
	*   cookie_file : cookie文件名 或 字符串
	*   referer ： 来源
	*   http_header ：请求头信息
	*/
	private function setOneOption($ch, $options = array())
    {	
    	// 发送的header头信息
    	$http_header = array();

		// 临时存储变量
		$tmp_options = array(
			CURLOPT_RETURNTRANSFER => TRUE,	// 返回数据变量
			CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1;)',	// 浏览器头
			CURLOPT_FOLLOWLOCATION => TRUE,	// 是否自动跳转
			CURLOPT_HEADER         => FALSE,	// 文件头信息
		);
		// 请求地址配置 string
		if (isset($options['url']))
		{
			$tmp_options[CURLOPT_URL] = $options['url'];
		}
		// 返回数据配置, bool
		if (isset($options['return_data']))
		{
			$tmp_options[CURLOPT_RETURNTRANSFER] = $options['return_data'];
		}
		// 连接的端口， int
		if (isset($options['port']))
		{
			$tmp_options[CURLOPT_PORT] = $options['port'];
		}

		// 伪IP，只设置forward一些信息
		if (isset($options['pseudo_proxy'])) {
			$http_header[] = 'CLIENT-IP:' . $options['pseudo_proxy_ip'];
			$http_header[] = 'X-FORWARDED-FOR:' . $options['pseudo_proxy_ip'];
		}

		// 使用代理配置
		if (isset($options['proxy']))
		{
			// 代理类型
			$proxy_type                     = (strtoupper($options['proxy_type']) == 'HTTP') ? CURLPROXY_HTTP : CURLPROXY_SOCKS5;
			$tmp_options[CURLOPT_PROXYTYPE] = $proxy_type;
			// 代理host
			$tmp_options[CURLOPT_PROXY]     = $options['proxy_host'];
			// 代理端口
			$tmp_options[CURLOPT_PROXYPORT] = $options['proxy_port'];
			// 代理验证方式
			if (isset($options['proxy_auth']))
			{
				// 代理验证类型
				$proxy_auth_type = ($options['proxy_auth_type'] == 'BASIC') ? CURLAUTH_BASIC : CURLAUTH_NTLM;
				$tmp_options[CURLOPT_PROXYAUTH] = $options['proxy_auth_type'];
				// 代理验证账号密码
				$user = "[{$options['proxy_auth_user']}]:[{$options['proxy_auth_pwd']}]";
				$tmp_options[CURLOPT_PROXYUSERPWD] = $user;
			}
		}
		// POST数据处理
        if (isset($options['post_data'])) 
        {
			$tmp_options[CURLOPT_POST]       = TRUE;
			$tmp_options[CURLOPT_POSTFIELDS] = $options['post_data'];
        }
		// 浏览器头
        if (isset($options['useragent'])) 
        {
            $tmp_options[CURLOPT_USERAGENT] = $options['useragent'];
        }
		// 启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
        if (isset($options['followlocation'])) 
        {
            $tmp_options[CURLOPT_FOLLOWLOCATION] = $options['followlocation'];
        }
		// 是否将头文件的信息作为数据流输出(HEADER信息)
        if (isset($options['header']))
        {
            $tmp_options[CURLOPT_HEADER] = $options['header'];
        }
		// ssl支持
		if (isset($options['ssl']) && (TRUE === $options['ssl']))
		{
			//不对认证证书来源的检查
			$tmp_options[CURLOPT_SSL_VERIFYPEER] = FALSE;
			//从证书中检查SSL加密算法是否存在
			$tmp_options[CURLOPT_SSL_VERIFYHOST] = TRUE;
		}
		
		// 设置连接等待时间,0不等待
		if (isset($options['connect_time']))
		{
			$tmp_options[CURLOPT_CONNECTTIMEOUT] = $options['connect_time'];
		}
        // 设置curl允许执行的最长秒数
		if (isset($options['timeout']))
		{
			$tmp_options[CURLOPT_TIMEOUT] = $options['timeout'];
		}
		// 设置客户端是否支持 gzip压缩
		if(isset($options['gzip']) && (TRUE === $options['gzip']))
        {
			$tmp_options[CURLOPT_ENCODING] = $options['gzip'];
		}
		// 是否使用到COOKIE，根据值不同做不同操作
		if (isset($options['use_cookie'])) {
			// 文件名或COOKIE信息，多个cookie用 ; 隔开
			$cookie_file = $options['cookie_file'];

			switch ($options['use_cookie']) {
				case COOKIE_SET_FILE:	// 使用此文件的COOKIE
					$tmp_options[CURLOPT_COOKIEFILE] = $cookie_file;
					break;
				case COOKIE_GET_FILE:	// 设置保存COOKIE的文件名
					$tmp_options[CURLOPT_COOKIEJAR] = $cookie_file;
					break;		
				case COOKIE_SET:	// 直接使用cookie字符串
					$tmp_options[CURLOPT_COOKIE] = $cookie_file;
				default:
					break;
			}	
		}

		// 来源
		if (isset($options['referer'])) {
			$tmp_options[CURLOPT_COOKIE] = $options['referer'];
		}

		// 请求头信息
		if (isset($options['http_header'])) {
			$http_header = array_merge($options['http_header'], $http_header);
		}

		// 加入http header
		if (!empty($http_header)) {
			$tmp_options[CURLOPT_HTTPHEADER] = $http_header;
		}
	
		// 设置CURL配置		
        curl_setopt_array($ch, $tmp_options);
    }

    /**
	* 添加一个新的并行抓取 handle
    *
	* @param	resource	$mh	并行抓取的资源
	* @param	string	$url	请求的url
	* @param	array	$options	curl配置项
	*
	* @return	resouce	添加的curl资源
	*/
	private function addHandle($mh, $url, $options = array())
    {
		if (FALSE !== empty($url))
		{
			return FALSE;
		}
        $ch = curl_init();
		$options['url'] = $url;
        $this->setOneOption($ch, $options);
        curl_multi_add_handle($mh, $ch);
        return $ch;
    }
    
    // 并行抓取
    private function execMulitHandle($mh)
    {
        do {
            $mrc = curl_multi_exec($mh,$active); 
        } 
        while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && ($mrc == CURLM_OK)) 
        {
            if (curl_multi_select($mh) != -1)
            {
                do {
                    $mrc = curl_multi_exec($mh, $active); 
                } 
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
    }
	
	 // 并行抓取，谁先返回处理谁 执行返回的数据
    private function execMulitHandleRoll($mh)
    {
        do {
            $mrc = curl_multi_exec($mh,$active); 
        } 
        while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && ($mrc == CURLM_OK)) 
        {
			// 阻塞直到cURL批处理连接中有活动连接。 
			// 成功时返回描述符集合中描述符的数量。失败时，select失败时返回-1，否则返回超时(从底层的select系统调用). 
			$res = curl_multi_select($mh, 1);
			
            if ($res != -1)
            {
                do {
					// $active 一个用来判断操作是否仍在执行的标识的引用。 
                    $mrc = curl_multi_exec($mh, $active); 
                } 
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
				
				while ($done = curl_multi_info_read($mh)) {
			
				//if ($done['result'] == CURLM_OK)
				//{
					//$done 完成的请求句柄  
					$info   = curl_getinfo($done['handle']);  
					$output = curl_multi_getcontent($done['handle']);  
					$error  = curl_error($done['handle']);
					
					// 判断回调方法是否可用
					if (!is_null($this->obj) && !is_null($this->callback) && is_callable(array($this->obj, $this->callback)))
					{
						call_user_func(array($this->obj, $this->callback), $done['result'], $info, $output);
					}
					// 删除完成的句柄
					curl_multi_remove_handle($mh, $done['handle']);
					curl_close($done['handle']);
				//}
				}
            }
        }
    }
}