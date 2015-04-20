<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed')};
/**
* 设首后台AJAX操作
*
* @date     2014/02/20
* @author   shaozhigang
*/
class ajax extends CI_Controller {
    public function __construct() {
    //sdaf
        parent::__construct();
        $this->load->helper('function');
    }
    
    /**
	* 重新定义调用规则
	*
	* @param	string	$method	参数传递要使用的方法
	* @param	array	$params	传递的参数
	*/
	public function _remap($method, $params = array())
	{
        $code   = 100;
        $msg    = 'ok';
        $result = '';
        try {
            if (!method_exists($this, $method)) {
                throw new Exception('method faild', 191);
            }
            return call_user_func_array(array($this, $method), $params);
        } catch (Exception $e) {
            $code = ($cc = $e->getCode()) ? $cc : 190; 
            $msg  = ($mm = $e->getMessage()) ? $mm : 'other fail' ;
        }
        json_msg($code, $msg, $result);
	}
    
    /**
    * 更改一级跳转URL
    */
    public function test() {
        $code   = 100;
        $msg    = 'ok';
        $result = '';
        try {
            // 参数过滤
            $id = intval($this->input->get('id'));
            if ($id < 1) {
                throw new Exception('no param', 101);
            }
            $result = array(
                'id' => 1,
                'name'=>'ok',
            );
        } catch (Exception $e) {
            $code   = ($cc = $e->getCode()) ? $cc : 190;
            $msg    = ($mm = $e->getMessage()) ? $mm : 'other fail';
        } 
        json_msg($code, $msg, $result);
    }
}