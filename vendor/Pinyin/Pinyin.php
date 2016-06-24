<?php
class Pinyin  {
	//返回第一个汉字的拼音首字母
	public function get_first_letter($string){
		return $this->pinyin_query($string,1,'L');
	}
	
	//返回第一个汉字的拼音
	public function get_first_pinyin($string){
		return $this->pinyin_query($string,1,'P');
	}
	
	//返回所有汉字的拼音首字母
	public function get_all_letter($string){
		return $this->pinyin_query($string,0,'L');
	}
	
	//返回所有汉字的拼音
	public function get_all_pinyin($string){
		return $this->pinyin_query($string,0,'P');
	}
	
	private function pinyin_query($string,$len,$style){
		if($len==1)$string=$this->msubstr($string,0,1);
		$field=$style=='L'?'f':'py';
		$str_arr=$this->msstr_split($string);
		$pinyin=array();
		$pinyin_data=include 'pinyin_data.php';
		foreach ($str_arr as $k=>$v){
			$res=list_search($pinyin_data,array('z'=>$v));
			$pinyin[$k]=$res?$style=='P'?ucfirst($res[0][$field]):$res[0][$field]:$v;
		}
		return implode(' ',$pinyin);
	}
	
	private function msstr_split($string){
		$flag=true;
		$i=0;
		$str_arr=array();
		do{
			if($a=$this->msubstr($string,$i,1)){
				$str_arr[]=$a;
				$i++;
			}else{
				$flag=false;
			}
		}while ($flag);
		return $str_arr;
	}
	
	/**
	 * 字符串截取，支持中文和其他编码
	 * @static
	 * @access public
	 * @param string $str 需要转换的字符串
	 * @param string $start 开始位置
	 * @param string $length 截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix 截断显示字符
	 * @return string
	 */
	private function msubstr($str, $start=0, $length, $charset="utf-8", $suffix='') {
	    if(function_exists("mb_substr"))
	        $slice = mb_substr($str, $start, $length, $charset);
	    elseif(function_exists('iconv_substr')) {
	        $slice = iconv_substr($str,$start,$length,$charset);
	        if(false === $slice) {
	            $slice = '';
	        }
	    }else{
	        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	        preg_match_all($re[$charset], $str, $match);
	        $slice = join("",array_slice($match[0], $start, $length));
	    }
	    return $suffix ? $slice.$suffix : $slice;
	}
}