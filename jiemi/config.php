<?php
header("Content-type:text/html;charset=utf-8");
/* 
* 解密程序配置文件
*
* @author shaozhigang1986@126.com
* @version v1.0 2012/08/06 16:10
**/
return array(
    'INPUT_PATH' => 'input',         //需要解密程序的源目录
    'OUTPUT_PATH' => 'output',        //解密完成后的文件存放目录
    //'KEY_WORD' =>   'This file is protected by copyright law',         //含有此关键字的程序进行解密操作
    'KEY_WORD' =>   '',         
    //'FILENAME_PATTEN' => FALSE,  // 匹配文件名正则 example : '/\.php$/i' 只查找PHP文件
    'FILENAME_PATTEN' => '/\.php$/i',  // 匹配文件名正则 example : '/\.php$/i' 只查找PHP文件
);
?>