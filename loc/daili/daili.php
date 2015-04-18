<?php
//http://www.fromzerotoseo.com/php-curl-proxy-checker/
//php禁止通过代理服务器注册会员
if($_SERVER['HTTP_VIA'] || $_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_CACHE_CONTROL'] || $_SERVER['HTTP_CACHE_INFO']) {
exit('为防止恶意注册，本站不允许使用Proxy访问!!');
}