<?php
/* 
* 解密程序操作
*
* @author shaozhigang1986@126.com
* @version v1.0 2012/08/06 16:10
**/
$config = require_once('config.php');

// 遍历文件
function getFileFrom($pathName, $exts = FALSE)
{
    $result = $tmp = array();
    if ($exts != FALSE)
    {
        foreach ($exts AS $key => $val)
        {
            $exts[$key] = '*.' . $val;
        }
    }
    $filter = !$exts ? '*' : '{' . implode(',', $exts) . '}';
    $pattern = $pathName . '/' . $filter;
    $filename = glob($pattern, GLOB_BRACE);
    foreach ($filename AS $val)
    {
        if (is_dir($val))
        {
            $tmp = getFileFrom($val, $exts);
        }
        else
        {
            $result[] = $val;
        }
    }
    //$result = array_merge($result, $tmp);
    //var_dump($result);
    return $result;
}

// 循环遍历目录结构,遍历出文件
function recurDir($pathName, $patten = FALSE)
{
    static $result = array();
    $temp = array();
    // 检查目录是否有效或可读
    if (!is_dir($pathName) || !is_readable($pathName))
    {
        return null;
    }
    // 得到目录下所有文件
    $allFiles = scandir($pathName);
    // 循环遍历所有文件
    foreach ($allFiles AS $filename)
    {
        // 如果是 . 或者 .. 的话则略过
        if (in_array($filename, array('.','..')))
        {
            continue;
        }
        // 得到文件完整名字
        $fullName = $pathName . '/' . $filename;
        // 如果该文件是目录的话，递归调用recurDir
        if (is_dir($fullName))
        {
            recurDir($fullName, $patten);
        }
        else
        {
            // 把文件存入临时数组
            $temp[] = $fullName;
        }
    }
    
    // 最后把临时数组内容添加到结果数组，目录在前，文件在后
    foreach ($temp AS $f)
    {
        if (($patten === FALSE) || preg_match($patten, $f))
        {
            $result[] = $f;
        }
    }
    return $result;
}

/*
* 解密文件
*
* @param string $filename   文件名
* @param string $keyword    文件中包含的关键字
**/ 
function decryption($filename, $keyword, $output_file)
{
    //$filename="input/1.php";//要解密的文件  
    $lines = file($filename);//0,1,2行  
    if (empty($keyword) === FALSE)
    {
        if (strpos($lines[0], $keyword) === FALSE)
        {
            return  FALSE;
        }
    }
    
    //第一次base64解密  
    $content="";  
    if(preg_match("/O0O0000O0\('.*'\)/",$lines[1],$y))  
    {  
        $content=str_replace("O0O0000O0('","",$y[0]);  
        $content=str_replace("')","",$content);  
        $content=base64_decode($content);  
    }  

    //第一次base64解密后的内容中查找密钥  
    $decode_key="";  
    if(preg_match("/\),'.*',/",$content,$k))  
    {  
        $decode_key=str_replace("),'","",$k[0]);  
        $decode_key=str_replace("',","",$decode_key);  
    }  

    //查找要截取字符串长度  
    $str_length="";  
    if(preg_match("/,\d*\),/",$content,$k))  
    {  
        $str_length=str_replace("),","",$k[0]);  
        $str_length=str_replace(",","",$str_length);  
    }  

    //直接还原密文输出 
    createDir(dirname($output_file));
    $fp1 = fopen($output_file, 'w+');

    if ($str_length > 0) {
        //截取文件加密后的密文  
        $Secret=substr($lines[2],$str_length);  
        var_dump( $decode_key,$Secret);
        var_dump(strtr($Secret,$decode_key,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='));exit;
        $str = "<?php\n".base64_decode(strtr($Secret,$decode_key,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'))."?>";
    } else {
        $str = file_get_contents($filename);
    }
     
    fwrite($fp1, $str);
    fclose($fp1);
    return TRUE;
}

/*
* 功能：循环检测并创建文件夹
* 参数：$path 文件夹路径
* 返回：
*/
function createDir($path)
{
    if (!file_exists($path))
    {
        createDir(dirname($path));
        mkdir($path, 0777);
    }
}

// 源目录下的所有文件
$file_arr = recurDir($config['INPUT_PATH'], $config['FILENAME_PATTEN']);
foreach ($file_arr AS $fkey => $fval)
{
    $replace_count = 1;
    $out_file = str_replace ($config['INPUT_PATH'], $config['OUTPUT_PATH'], $fval, $replace_count);
    if (decryption($fval, $config['KEY_WORD'], $out_file) === TRUE)
    {
        echo '源文件:' . $fval;
        echo ' 目标文件:' . $out_file;
        echo "<br />";
    }
}
?>