<?php if ( ! defined('BASEPATH')) {exit('No direct script access allowed');}
/**
* 关键字匹配替换model
*
* @date 2014/04/17
*/
class tool_keyword_model extends CI_Model {
    private $rword = '';//编译后的关键词数组
    public function __construct() {
        $this->load->model('jkkeywords_model');
        // 获得按字节编译的关键词
        $this->rword = $this->jkkeywords_model->getAllJkwordByte();
    }
    
    /**
     +----------------------------------------------------------
     * 过滤关键字
     +----------------------------------------------------------
     * @access	public
	 * @para	article		string		文章内容
	 * @para	type		bool		$type=1 标红 $type=2 换成 *
	 * @return	type		string		文章内容替换结果
     +----------------------------------------------------------
     */
	public function replace($article,$type=1){
		$len=strlen($article);
		$begin=$end=array();
		for($i=0;$i<$len;$i++){
			if($n=$this->find_keyword($article,$this->rword,$i)){

				$begin[]=$i;
				$end[]=$i+$n;
				//换成*
				if($type==2){
					for($n;$n>0;$n--){
						$article{$i}='*';
						$i++;
					}
				}
				$i=$i+$n;
			}
		}
		//标红
		if($type==1){
			$len=count($begin);
			for($k=$len;$k>=0;$k--){
				if(isset($end[$k])) {
                    $article=$this->insertstr($article,$end[$k],'</font>');
                }
				if(isset($begin[$k])) {
                    $article=$this->insertstr($article,$begin[$k],'<font color=red>');
                }
			}
		}
		return	$article;
	}
    
    
    /**
     +----------------------------------------------------------
     * 查找关键字
     +----------------------------------------------------------
     * @access	public
	 * @para	article		string		文章内容
     +----------------------------------------------------------
     */
	public function findKeyword($article){
		$len=strlen($article);
		for($i=0;$i<$len;$i++){
			if($n=$this->find_keyword($article,$this->rword,$i)){
                // 查找到关键字就退出
                return true;
			}
		}

		return	false;
	}
    
	/**
     +----------------------------------------------------------
     * 递归查找指定位置是否有关键词
     +----------------------------------------------------------
     * @access	public
     +----------------------------------------------------------
     */
	public function find_keyword($article,$rword,$i,$pos=1) {
		if($pos>20) {
            return false;
        }
	
        if (isset($article{$i}) && isset($rword['key'][$article{$i}])) {
            $temp = $rword['key'][$article{$i}];
            
            if (isset($temp['val']) && ($temp['val'] == 1)) {
                return $pos;
            }
            if (!isset($temp['key']) || empty($temp['key'])) {
                return false;
            }
        } else {
            return false;
        }
		$pos++;
		$rword = $rword['key'][$article{$i}];
		return	$this->find_keyword($article,$rword,$i+1,$pos);
	}

	/**
     +----------------------------------------------------------
     * 指定位置插入字符
     +----------------------------------------------------------
     * @access	public
     +----------------------------------------------------------
     */

	public function insertstr($str,$pos,$instr){
		return	substr($str,0,$pos).$instr.substr($str,$pos,strlen($str));
	}
}