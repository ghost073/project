<?php
/**
 * 安全验证码 
 * 安全的验证码要：验证码文字扭曲、旋转，使用不同字体，添加干扰码
 */
class captcha {
	/**
	 * 验证码的session的下标
	 * 
	 * @var string
	 */
	public static $seKey = 'captcha';
	public static $expire = 300;     // 验证码过期时间（s）
	/**
	 * 验证码中使用的字符，01IO容易混淆，建议不用
	 *
	 * @var string
	 */
	public static $codeSet = '的一是在了不和有大这主中人上为们地个用工时要动国产以我到他会作来分生对于学下级就年阶义发成部民可出能方进同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批如应形想制心样干都向变关点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫康遵牧遭幅园腔订香肉弟屋敏恢忘衣孙龄岭骗休借丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩';
	public static $fontSize = 30;     // 验证码字体大小(px)
	public static $useCurve = true;   // 是否画混淆曲线
	public static $useNoise = false;   // 是否添加杂点	
	public static $imageH = 0;        // 验证码图片宽
	public static $imageL = 0;        // 验证码图片长
	public static $length = 2;        // 验证码位数
	public static $bg = array(243, 251, 254);  // 背景
	
	protected static $_image = null;     // 验证码图片实例
	protected static $_color = null;     // 验证码字体颜色
	
	/**
	 * 输出验证码并把验证码的值保存的session中
	 * 验证码保存到session的格式为： $_SESSION[self::$seKey] = array('code' => '验证码值', 'time' => '验证码创建时间');
	 */
	public static function entry() {
		// 图片宽(px)
		self::$imageL || self::$imageL = self::$length * self::$fontSize * 1.5 + self::$fontSize*1.5; 
		// 图片高(px)
		self::$imageH || self::$imageH = self::$fontSize * 2;
		// 建立一幅 self::$imageL x self::$imageH 的图像
		self::$_image = imagecreate(self::$imageL, self::$imageH); 
		// 设置背景      
		imagecolorallocate(self::$_image, self::$bg[0], self::$bg[1], self::$bg[2]); 
		// 验证码字体随机颜色
		self::$_color = imagecolorallocate(self::$_image, mt_rand(1,120), mt_rand(1,120), mt_rand(1,120));
		// 验证码使用随机字体
		//$ttf = 'http://static.ui.07073.com/_img/ttfs/' . 4 . '.ttf'; 
		/*
        $ttf_arr = array(2);
		$ttf_key = array_rand($ttf_arr);
		$rand_ttf = mt_rand(1, 20);
		$rand_ttf = $ttf_arr[$ttf_key];*/
		$ttf = dirname(__FILE__) . '/ttfs/simhei.ttf';  
		
		if (self::$useNoise) {
			// 绘杂点
			self::_writeNoise();
		} 
		if (self::$useCurve) {
			// 绘干扰线
			self::_writeCurve();
		}
		
		// 绘验证码
		$code = array(); // 验证码
		$codeNX = 0; // 验证码第N个字符的左边距
        $code_arr = str_split(self::$codeSet, 2);
        $codeLen = count($code_arr)-1;
        
		for ($i = 0; $i<self::$length; $i++) {
			$code[$i] = $code_arr[mt_rand(0, $codeLen)];
            // 中文验证码显示
            $code_tmp = mb_convert_encoding($code[$i], "html-entities", "gbk");
			$codeNX += mt_rand(self::$fontSize*1.2, self::$fontSize*1.5);
			// 写一个验证码字符
			imagettftext(self::$_image, self::$fontSize, mt_rand(-10, 40), $codeNX, self::$fontSize*1.5, self::$_color, $ttf, $code_tmp);
		}
		
		// 保存验证码
		isset($_SESSION) || session_start();
		$_SESSION[self::$seKey]['code'] = implode('', $code); // 把校验码保存到session
		$_SESSION[self::$seKey]['time'] = time();  // 验证码创建时间

		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');		
		header("content-type: image/png");
	
		// 输出图像
		imagepng(self::$_image); 
		imagedestroy(self::$_image);
	}
	
	/** 
	 * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数) 
     *      
     *      高中的数学公式咋都忘了涅，写出来
	 *		正弦型函数解析式：y=Asin(ωx+φ)+b
	 *      各常数值对函数图像的影响：
	 *        A：决定峰值（即纵向拉伸压缩的倍数）
	 *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
	 *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
	 *        ω：决定周期（最小正周期T=2π/∣ω∣）
	 *
	 */
    protected static function _writeCurve() {
		$A = mt_rand(1, self::$imageH/2);                  // 振幅
		$b = mt_rand(-self::$imageH/4, self::$imageH/4);   // Y轴方向偏移量
		$f = mt_rand(-self::$imageH/4, self::$imageH/4);   // X轴方向偏移量
		$T = mt_rand(self::$imageH*1.5, self::$imageL*2);  // 周期
		$w = (2* M_PI)/$T;
						
		$px1 = 0;  // 曲线横坐标起始位置
		$px2 = mt_rand(self::$imageL/2, self::$imageL * 0.667);  // 曲线横坐标结束位置 	    	
		for ($px=$px1; $px<=$px2; $px=$px+ 0.9) {
			if ($w!=0) {
				$py = $A * sin($w*$px + $f)+ $b + self::$imageH/2;  // y = Asin(ωx+φ) + b
				$i = (int) ((self::$fontSize - 6)/4);
				while ($i > 0) {	
				    imagesetpixel(self::$_image, $px + $i, $py + $i, self::$_color);  // 这里画像素点比imagettftext和imagestring性能要好很多				    
				    $i--;
				}
			}
		}
		
		$A = mt_rand(1, self::$imageH/2);                  // 振幅		
		$f = mt_rand(-self::$imageH/4, self::$imageH/4);   // X轴方向偏移量
		$T = mt_rand(self::$imageH*1.5, self::$imageL*2);  // 周期
		$w = (2* M_PI)/$T;		
		$b = $py - $A * sin($w*$px + $f) - self::$imageH/2;
		$px1 = $px2;
		$px2 = self::$imageL;
		for ($px=$px1; $px<=$px2; $px=$px+ 0.9) {
			if ($w!=0) {
				$py = $A * sin($w*$px + $f)+ $b + self::$imageH/2;  // y = Asin(ωx+φ) + b
				$i = (int) ((self::$fontSize - 8)/4);
				while ($i > 0) {			
				    imagesetpixel(self::$_image, $px + $i, $py + $i, self::$_color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多	
				    $i--;
				}
			}
		}
	}
	
	/**
	 * 画杂点
	 * 往图片上写不同颜色的字母或数字
	 */
	protected static function _writeNoise() {
		for($i = 0; $i < 10; $i++){
			//杂点颜色
		    $noiseColor = imagecolorallocate(
		                      self::$_image, 
		                      mt_rand(150,225), 
		                      mt_rand(150,225), 
		                      mt_rand(150,225)
		                  );
			for($j = 0; $j < 5; $j++) {
				// 绘杂点
			    imagestring(
			        self::$_image,
			        5, 
			        mt_rand(-10, self::$imageL), 
			        mt_rand(-10, self::$imageH), 
			        self::$codeSet[mt_rand(0, 27)], // 杂点文本为随机的字母或数字
			        $noiseColor
			    );
			}
		}
	}
	
	/**
	 * 验证验证码是否正确
	 *
	 * @param string 	$code 		用户验证码
	 * @param boo  	 	$del_sess	验证完是否删除session 
	 *                          	true:是 
	 *                          	false:否 ajax验证时使用false,提交以后验证，使用true
	 * 
	 * @param bool 用户验证码是否正确
	 */
	public static function check($code) {	
		//$code = strtoupper($code);//都转为大写
		isset($_SESSION) || session_start();
        $captcha_arr = array();
        
        if (!isset($_SESSION[self::$seKey]) || empty($_SESSION[self::$seKey])) {
            return false;
        }
        
        // 清除session,避免sesion重放攻击
        $captcha_arr = $_SESSION[self::$seKey];
        unset($_SESSION[self::$seKey]);
        
		// 验证码不能为空
		if(empty($code)) {
			return false;
		}
		
		// session 过期
		if(time() - $captcha_arr['time'] > self::$expire) {
			return false;
		}

		if($code == $captcha_arr['code']) {
			return true;
		}

		return false;
	}
}


// useage
/*
YL_Security_Secoder::$useNoise = false;  // 要更安全的话改成true
YL_Security_Secoder::$useCurve = true;
YL_Security_Secoder::entry();
*/

/*
// 验证验证码
if (!YL_Security_Secoder::check(@$_POST['secode'])) {
	print 'error secode';
}
*/