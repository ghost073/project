<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
* API
*
* @author   shaozhigang
* @date     2014/03/18
*/
class Member extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('xml');
    }
    
    /**
    * 自动注册
    */
    public function test() {
        // XML返回的数据
        $code   = HEZI_SUCCESS;
        $msg    = 'ok';
        $result = '';
        try {

        } catch (Exception $e) {
            $code = ($cc = $e->getCode()) ? $cc : HEZI_OTHERERROR; 
            $msg  = ($mm = $e->getMessage()) ? $mm : 'other fail' ;
        }

        // xml返回数据
        $this->xml->setInCharset('gbk');
		$this->xml->setOutCharset('utf8');
		$this->xml->setHeadCharset('gbk');
        echo $this->xml->export($result, $code, $msg);
		exit;
    }
}