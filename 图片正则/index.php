<?php
class a {
    public function index() {
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
    }
    
        /**
    * 格式化主题数据
    * @param    array   $topic_arr  主题数据
    *
    * @return   array
    */
    private function formatTopic($topic_arr) {
        if (!is_array($topic_arr) || empty($topic_arr)) {
            return array();
        }
        
        $this->load->library('dateformat');
        $this->load->model('tool_model');

        foreach ($topic_arr as $key=>$val) {
            // 人性化时间
            $val['last_date_1'] = $this->dateformat->formatTime2($val['last_reply_time']);
            // 标题长度26字符
            $val['title_1'] = mb_strimwidth($val['title'], 0, 54, '...', 'gbk');
            
            // 主题内容
    		$content = htmlspecialchars_decode($val['content']);

    		// 主题图片
    		$img_patten = '/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i';
    		preg_match_all($img_patten, $content, $img_arr);

    		if (!empty($img_arr[1])) {
    			$imgs = array();
    			foreach ($img_arr[1] as $img_key => $img_val) {
                    // 去掉表情
                    $biaoqing = 'kindeditor4.1.10/plugins/emoticons/images';
                    if (false !== strpos($img_val, $biaoqing)) {
                        continue;
                    }
    				// 格式化图片大小
    				$imgs[$img_key]['img_100_133'] = cut_pic_url($img_val, 100, 133);
    				// 原图
    				$imgs[$img_key]['img'] = $img_val;
    			}
                // 只要3个图片
    			$val['imgs'] = array_slice($imgs, 0, 3);
    		}

    		// 去掉html标签
    		$content_decode = trim(preg_replace('/\s\s+/', ' ', strip_tags($content)));

    		// 截取字符串长度
            $content_1 = mb_strimwidth($content_decode, 0, 70, '...', 'gbk');
            $val['content_1'] = $content_1;
        
            $topic_arr[$key] = $val;
        }
        
        return $topic_arr;
    }
}