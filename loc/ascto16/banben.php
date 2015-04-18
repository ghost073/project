<?php
/**
我就按32位算，2.2.6.0组合出来就是十六进制：0x02020600，等于十进制33687040
2.5.5.11就是十六进制：0x0205050B，等于十进制：33883403
*/

$version = '2.5.5.11';
$version = '2.3.0.0';
$new_arr = array();
$ver_arr = explode('.', $version);
$ver_count = count($ver_arr);
$str = 0;
foreach ($ver_arr AS $key => $val)
{
    $new_arr[] = str_pad(dechex($val), 2, '0', STR_PAD_LEFT);;
}
$hex_str = '0x' . implode('', $new_arr);
var_dump($hex_str);
$dec_str = hexdec($hex_str);
var_dump($dec_str);