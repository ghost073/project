<?php
/**
* 字符串处理函数
*
* @author	shaozhgiang
* @date	2013/11/13
*
*/

if (!function_exists('json_msg')) {
/**
* 输出json格式化的数据
* errno 为100 代表操作正常
* 
* @param    int     $errno      状态码
* @param    string  $errstr     状态码说明
* @param    mix     $errinfo    返回的数据 array/string
* @param    array   $extinfo    额外附加信息
* 
* @return   unknow
*/
function json_msg($errno, $errstr, $errinfo = '', $extinfo = array()) {
    $err_arr = array('errno' => $errno, 'errstr' => $errstr);
    // 有附加信息补充
    if (!empty($extinfo)) {
        $err_arr = array_merge($err_arr, $extinfo);
    }

    if (FALSE === empty($errinfo))
    {
        $err_arr['info'] = $errinfo;
    }
    echo json_encode($err_arr);
    exit;
}
}

/**
* 输出jsonp格式化的数据
* errno 为100 代表操作正常
*
* @param    string  $callback   jsonp回调函数名
* @param    int     $errno      状态码
* @param    string  $errstr     状态码说明
* @param    mix     $errinfo    返回的数据 array/string
* @param    array   $extinfo    额外附加信息
*
* @return   unknow
*/
function jsonp_msg($callback, $errno, $errstr, $errinfo = '', $extinfo = array()) {
    $err_arr = array('errno' => $errno, 'errstr' => $errstr);
    // 有附加信息补充
    if (!empty($extinfo)) {
        $err_arr = array_merge($err_arr, $extinfo);
    }
    
    if (FALSE === empty($errinfo)) {
        $err_arr['info'] = $errinfo;
    }
    echo $callback . '(' . json_encode($err_arr) . ')';
    exit;
}

if (!function_exists('format_arr_key2key')) {
/**
 * 以某个键名的值为KEY,另一个键名的值为val
 * 
 * @param  array    $arr    要格式化的数组
 * @param  string   $k_k    以这个键名为key
 * @param  string   $k_v    以这个键名为val
 * @param  int      $is_arr 格式化成的形式
 *                          1: 格式 array(id值=>name值);
 *                          2: 格式 array(groupid值=>array(0=>name值1,1=>name值2));
 * @return [type]         [description]
 */
function format_arr_key2key($arr, $k_k, $k_v, $is_arr = 1){
    if (!is_array($arr) || empty($arr)) {
        return array();
    }
    $res = array();
    foreach ($arr AS $val) {
        switch($is_arr)
        {
            case 2:
                $res[$val[$k_k]][] = $val[$k_v];
                break;
            case 1:
            default:
                $res[$val[$k_k]] = $val[$k_v];
                break;
        }
    }
    return $res;
}
}

if (!function_exists('format_arr_key2arr')) {
/**
 * 以某个键名的值为KEY的数组
 * 
 * @param  array    $arr    要格式化的数组
 * @param  string   $k_k    以这个键名为key
 * @param  int      $is_arr 格式化成的形式
 *                          1: 格式 array(id值=>array());
 *                          2: 格式 array(groupid值=>array(0=>array(),1=>array()));
 *                          3: 格式 array(0=>id值1, 1=>id值2)
 * @return [type]         [description]
 */
function format_arr_key2arr($arr, $k_k, $is_arr = 1) {
    if (!is_array($arr) || empty($arr)) {
        return array();
    }
    $res = array();
    foreach ($arr AS $val) {
        switch($is_arr) {
            case 2:
                $res[$val[$k_k]][] = $val;
                break;
            case 3:
                $res[] = $val[$k_k];
                break;
            case 1:
            default:
                $res[$val[$k_k]] = $val;
                break;
        }
    }
    return $res;
}
}

if (!function_exists('uc_authcode')) {
/**
 * uc加密解密方法
 * 
 * @param  string $string    要加密的字符串
 * @param  string $operation 加密解密操作
 * @param  string $key       加密解密KEY
 * @param  int    $expiry    过期时间
 *
 *  @return string            加密串
 */
function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

    $ckey_length = 4;

    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
}

if (!function_exists('chg_url')) {
/**
 * 转化链接 (普通链接转换成迅雷，快车格式 或 相反转换)
 * 
 * @param  string $url  普通链接
 * @param  string $oper 操作 加密解密
 * @param  string $type 类型：thunder：迅雷 flashget:快车
 * 
 * @return string 转换后的url
 */
function chg_url($url = '', $oper = 'ENCODE', $type = 'thunder') {
    $new_url = '';
    $oper = strtoupper($oper);
    switch ($type) {
        case 'thunder':
            if ($oper == 'ENCODE') {
                $new_url = 'thunder://'.base64_encode('AA'. $url .'ZZ');
            } else {
                $new_url = substr(base64_decode(str_ireplace('thunder://','',$url)),2,-2);
            }
            break;
        case 'flashget':
            if ($oper == 'ENCODE') {
                $new_url = 'flashget://'.base64_encode($url);
            } else {
                $new_url = str_ireplace('[FLASHGET]','',base64_decode(str_ireplace('flashget://','',$url)));
            }
            break;
    }
    return $new_url;
}
}


if (!function_exists('format_num')) {
/**
 * 格式化数字
 * 大于1万，显示 1 + 万
 * 
 * @param  int    $num 要格式化的数字
 * 
 * @return string       格式化显示的数字
 */
function format_num($num = 0) {
    $num = intval($num);
    if ($num < 10000) {
        return $num;
    }

    $num_str = floor($num);
    $num_str .= '万';
    return $num_str;
}
}

if (!function_exists('version2dec')) {
/**
* 版本号转成10进制数字 
*
* @param    string  $version    版本号：2.5.5.11
*
* @return   int 10进制数字 33883403
*/
function version2dec($version)
{
    if (empty($version))
    {
        return 0;
    }
    
    $new_arr = array();
    $ver_arr = explode('.', $version);
    foreach ($ver_arr AS $key => $val)
    {
        $new_arr[] = str_pad(dechex($val), 2, '0', STR_PAD_LEFT);;
    }
    $hex_str = '0x' . implode('', $new_arr);
    $dec_str = hexdec($hex_str);
    return $dec_str;
}
}

/**
* 输出json格式化的数据
* errno 为100 代表操作正常
* 
* @param	int	$errno	状态码
* @param	string	$errstr	状态码说明
* @param	mix		$errinfo	返回的数据 array/string
*
* @return	unknow
*/
if (!function_exists('json_msg')) {
function json_msg($errno, $errstr, $errinfo = '')
{
    $err_arr = array('errno' => $errno, 'errstr' => $errstr);
    if (FALSE === empty($errinfo))    {
        $err_arr['info'] = $errinfo;
    }
    echo tb_json_encode($err_arr);
    exit;
}
}
/**
* gbk编码下json_encode
* 
* @param	mix	$value	要json的值
*
* @return	mix	格式化后的数据
*/
if (!function_exists('tb_json_encode')) {
function tb_json_encode($value)
{
    return json_encode(tb_json_convert_encoding($value, "GBK", "UTF-8"));
}
}
/**
* gbk编码下json_decode
* 
* @param	string	$str	json格式化的值
* @param	bool	$assoc	true:返回数组
*
* @return	mix	json解析后的数据
*/
if (!function_exists('tb_json_decode')) {
function tb_json_decode($str, $assoc = TRUE)
{
    return tb_json_convert_encoding(json_decode($str, $assoc), "UTF-8", "GBK");
}
}
/**
* 字符串编码转换
* 
* @param	mix	$m	要转化的值
* @param	string	$from	转换的源编码
* @param	string	$to	转换的目的编码
*
* @return	mix	转化完编码后的数据
*/
if (!function_exists('tb_json_convert_encoding')) {
function tb_json_convert_encoding($m, $from, $to)
{
    switch(gettype($m)) 
    {
        case 'integer':
        case 'boolean':
        case 'float':
        case 'double':
        case 'NULL':
            return $m;
        case 'string':
            return mb_convert_encoding($m, $to, $from);
        case 'object':
            $vars = array_keys(get_object_vars($m));
            foreach($vars as $key) 
            {
                $m->$key = tb_json_convert_encoding($m->$key, $from ,$to);
            }
            return $m;
        case 'array':
            foreach($m as $k => $v) 
            {
                $m[tb_json_convert_encoding($k, $from, $to)] = tb_json_convert_encoding($v, $from, $to);
            }
            return $m;
            default:
            break;
    }
    return $m;
} 
}

/**
* 生成随机值
*
* @param    int     $num        生成的长度
*
* @return   string              随机值
*/
if (!function_exists('random_str')) {
    function random_str($num = 6) {
        // 参数过滤
        $num = intval($num);
        if ($num < 1) {
            $num = 6;
        }
        // 生成密码使用的初始值
        $init_str = '0123456789abcdefghijklmnopqrstuvwxyz';
        // 返回的值
        $new_str = '';

        for ($i = 1; $i <= $num; $i ++) {
            // 字符串顺序随机
            $str = str_shuffle($init_str);
            // 截取指定长度
            $str = substr($str, $i, 1);

            $new_str .= $str;
        }

        return $new_str;
    }
}

/**
* 生成随机数字字符串
*
* @param    int     $num        生成的长度
*
* @return   string              随机值
*/
if (!function_exists('random_num')) {
    function random_num($num = 3) {
        // 参数过滤
        $num = intval($num);
        if ($num < 1) {
            $num = 3;
        }
        // 生成密码使用的初始值
        $init_str = '0123456789';
        // 返回的值
        $new_str = '';

        for ($i = 1; $i <= $num; $i ++) {
            // 字符串顺序随机
            $str = str_shuffle($init_str);
            // 截取指定长度
            $str = substr($str, $i, 1);

            $new_str .= $str;
        }

        return $new_str;
    }
}


/**
 * 数字转成EXCEL对应列值
 *
 * @param  int $index 数字索引 1开始
 * @param  int $start 从哪个位置开始
 *
 * @return string
 */
if (!function_exists('int2chr')) {
function int2chr($index, $start = 64) {
    $str = '';
    if (floor($index / 26) > 0) {
        $str .= IntToChr(floor($index / 26)-1);
    }
    return $str . chr($index % 26 + $start);
}
}