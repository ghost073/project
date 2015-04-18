<?php
public function tijiao() {
    // 正文，富文本编辑器提供内容
    $content = trim(mb_convert_encoding($this->input->post('content'),'gbk','utf-8'));
    // 只保留IMG,br标签,
    $content1 = strip_tags($content, '<img>');

    // 与JS保持一致计算长度
    $patten_con_1 = '/<(?:img|embed).*?>/i';
    $patten_con_2 = '/\r\n|\n|\r|<br \/>/';

    $content_1 = preg_replace($patten_con_1, 'K', $content1);
    $content_2 = preg_replace($patten_con_2, '', $content_1);

    // 正文长度
    $content_len = mb_strlen($content_2, 'gbk');
    if (($content_len < 1) || ($content_len > 2000)) {
        throw new Exception('内容长度在1-2000字符之间', 101);
    }

    // 替换IMG标签 src="data:image" 格式数据太大替换为空
    $img_patten = '/<img[^>]*src=[\'"]?(data:image[^>\'"\s]*)[\'"]?[^>]*>/i';
    $content = preg_replace($img_patten, '', $content);
    // 替换不以img3.07073.com开头的图片
    $img_patten='/<img[^>]*src=[\'"]?(?!http:\/\/(img3|img1)\.07073\.com)([^>\'"\s]+)[\'"]?[^>]*>/i';
    $content = preg_replace($img_patten, '', $content);

    // 判断是否包含屏蔽词
    $is_shield = $this->tool_model->isShieldJkword($content);
    if ($is_shield) {
        throw new Exception('输入标题或内容包含敏感词汇,请重新输入！', 102);
    }

    // xss过滤
    include_once APPPATH.'libraries/HTMLPurifier.standalone.php';
    $filter_cnf = HTMLPurifier_Config::createDefault();
    $filter_cnf->set('Core.Encoding', 'gbk');
    $purifier = new HTMLPurifier($filter_cnf);
    $content = $purifier->purify($content);
}