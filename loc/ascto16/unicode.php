<?php
/**
* 汉字转Unicode编码
 * @param string $str 原始汉字的字符串
 * @param string $encoding 原始汉字的编码
 * @param boot $ishex 是否为十六进制表示（支持十六进制和十进制）
 * @param string $prefix 编码后的前缀
 * @param string $postfix 编码后的后缀
 */
function unicode_encode($str, $encoding = 'UTF-8', $ishex = false, $prefix = '&#', $postfix = ';') {
	$str = iconv($encoding, 'UCS-2', $str);
	$arrstr = str_split($str, 2);
	$unistr = '';
	for($i = 0, $len = count($arrstr); $i < $len; $i++) {
		$dec = $ishex ? bin2hex($arrstr[$i]) : hexdec(bin2hex($arrstr[$i]));
		$unistr .= $prefix . $dec . $postfix;
}
	return $unistr;
}
 
/**
 * Unicode编码转汉字
 * @param string $str Unicode编码的字符串
 * @param string $decoding 原始汉字的编码
 * @param boot $ishex 是否为十六进制表示（支持十六进制和十进制）
 * @param string $prefix 编码后的前缀
 * @param string $postfix 编码后的后缀
 */
function unicode_decode($unistr, $encoding = 'UTF-8', $ishex = false, $prefix = '&#', $postfix = ';') {
	$arruni = explode($prefix, $unistr);
$unistr = '';
	for($i = 1, $len = count($arruni); $i < $len; $i++) {
	if (strlen($postfix) > 0) {
			$arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
		}
		$temp = $ishex ? hexdec($arruni[$i]) : intval($arruni[$i]);
		$unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
	}
	return iconv('UCS-2', $encoding, $unistr);
}
 
header('Content-Type: text/html; charset=UTF-8');

// ♥ = U+2665
// ( = U+0028
// ^ = U+005E
// $ = U+0024
// \ = U+005c
$str = '\\';
$uni_str = unicode_encode($str, 'UTF-8', TRUE, '\u');
var_dump($uni_str);

$uni_str1 = '\u7ecf;';
$uni_str1 = '\u551f;';
$uni_str1 = '\u9fa0;';
$uni_str1 = '\u4E00;';
$uni_str1 = '\u3400;';
$str1 = unicode_decode($uni_str1, 'UTF-8', TRUE, '\u', ';');
var_dump($str1);
exit;

/*

// UTF-8字符串测试
$str = '龕龖龗龘龙龚龛龜龝龞龟龠龡龢龣龤龥';
var_dump($str);
 
// 简单的
$uni_str = mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8');
var_dump($uni_str);
 
$str3 = mb_convert_encoding($uni_str, 'UTF-8', 'HTML-ENTITIES');
var_dump($str3);
 
$uni_str = unicode_encode($str);
var_dump($uni_str); // &#40853;&#40854;&#40855;&#40856;&#40857;&#40858;&#40859;&#40860;&#40861;&#40862;&#40863;&#40864;&#40865;&#40866;&#40867;&#40868;&#40869;
 
$str2 = unicode_decode($uni_str);
var_dump($str2); // 龕龖龗龘龙龚龛龜龝龞龟龠龡龢龣龤龥
 */
 
$str = '喊';
$uni_str = unicode_encode($str, 'UTF-8', true, '\u', '');
var_dump($uni_str); // \u9f95\u9f96\u9f97\u9f98\u9f99\u9f9a\u9f9b\u9f9c\u9f9d\u9f9e\u9f9f\u9fa0\u9fa1\u9fa2\u9fa3\u9fa4\u9fa5
 
$uni_str = '\uA4CF';
$str2 = unicode_decode($uni_str, 'UTF-8', true, '\u', '');
var_dump($str2); // 龕龖龗龘龙龚龛龜龝龞龟龠龡龢龣龤龥
 exit;
 
// GBK字符串测试
$str = 'PHP汉字转UNICODE';
 
$str = iconv('UTF-8', 'GBK//IGNORE', $str);
$uni_str = unicode_encode($str, 'GBK');
var_dump($uni_str); // &#80;&#72;&#80;&#27721;&#23383;&#36716;&#85;&#78;&#73;&#67;&#79;&#68;&#69;
 
$str2 = unicode_decode($uni_str, 'GBK');
$str2 = iconv('GBK', 'UTF-8', $str2);
var_dump($str2); // PHP汉字转UNICODE
 
$uni_str = unicode_encode($str, 'GBK', true, '\u', '');
var_dump($uni_str); // \u0050\u0048\u0050\u6c49\u5b57\u8f6c\u0055\u004e\u0049\u0043\u004f\u0044\u0045
 
$str2 = unicode_decode($uni_str, 'GBK', true, '\u', '');
$str2 = iconv('GBK', 'UTF-8', $str2);
var_dump($str2); // PHP汉字转UNICODE