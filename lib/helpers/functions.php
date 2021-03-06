<?php
/**
 * 定义全局方法
 * @author wave
 */

/**
 * url转换
 * @param Array $params 参数
 * @param bool $urlRewrite 是否伪静态
 * @author wave
 */
function urlTo($params = array(),$urlRewrite = false){
	if($urlRewrite){
	   $diff  = array();
	   if(isset($params[G]) && isset($params[C]) && isset($params[A]) ){
		$diff =  array(G=>$params[G],C=>$params[C],A=>$params[A]);
	    }	   
	    $params = array_diff_assoc($params, $diff);
	    $urlStr = '';
	    foreach($params as $key => $value){
		if(BINDURLPARAM){
			$urlStr .= ROUTE_DS.$key.ROUTE_DS.$value;
		}else {
			$urlStr .= ROUTE_DS.$value;
		}
	    }
	    return ROUTE_DS.implode(ROUTE_DS, $diff).$urlStr;
	}
	return '?'.http_build_query($params);
}

/**
 * 转义数组
 * @param array $get 要转义的数组
 * @return array
 * @author wave
 */
function addslashesArr($get = array()){
	foreach($get as $k => $v){
		if(is_string($v)){
			$get[$k] = addslashes($v);
		}elseif(is_array($v)) {
			$get[$k] = addslashesArr($v);
		}
	}
	return $get;
}

/**
 * 加载文件
 * @param string $file 加载文件路径
 * @return bool
 * @author wave
 */
function load($file){
	return Container::get('XmlParse')->load($file);
}




