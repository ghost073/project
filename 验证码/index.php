<?php    
/**
 * 验证码图片
 * 
 */
public function captcha() {
    // 引入所需文件
    $this->load->library('captcha');
    // 显示验证码
    $this->captcha->entry();
    exit;
}

public function check() {
    $this->load->library('captcha');
    // 验证码
    $captcha = $this->postString('captcha');
    $captcha = trim(mb_convert_encoding($captcha,'gbk','utf-8'));
    // 判断验证码是否正确,验证完后删除验证码
    $code_res = $this->captcha->check($captcha, true);

    if (false === $code_res) {
        throw new Exception('验证码输入错误', 141);
    }
}