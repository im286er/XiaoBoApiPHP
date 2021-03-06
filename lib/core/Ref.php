<?php
/**
 * 反射类
 * @author wave
 */

class Ref{

	/**
	 * 保存反射类对象
	 * @author wave
	 */
	static  protected $classRef;

	/**
	 * 保存反射类方法对象
	 * @author wave
	 */
	static  protected $methodRef;


	/**
	 * 初始化反射类
	 * @param string $class 类名 
	 * @return object
	 * @author wave
	 */
	static public function classInstace($class = ''){
		self::$classRef = new ReflectionClass($class);
	}

	/**
	 * 初始化反射类方法
	 * @param string $class 类名 
	 * @param string $method 方法名 
	 * @return object
	 * @author wave
	 */
	static public function methodInstace($class = '',$method = ''){
		self::$methodRef = new ReflectionMethod($class,$method);
	}

	/**
	 * 判断是否公共方法
	 * @return bool
	 * @author wave
	 */
	static public function isPublic(){
		return self::$methodRef->isPublic();
	}

	/**
	 * 判断是否静态方法
	 * @return bool
	 * @author wave
	 */
	static public function isStatic(){
		return self::$methodRef->isStatic();	
	}


	/**
	 * 获取类方法的参数
	 * @return array
     * @author wave
	 */
	static public function getParams(){
		$params = array();
		foreach(self::$methodRef->getParameters() as $param){
			if($param->name){
				$params[$param->name] = $param->name;
			}
		}
		return $params;
	}


	/**
	 * 判断类方法是否存在
	 * @param string $method 方法名 
	 * @return object
	 * @author wave
	 */
	static public function hasMethod($method = ''){
		return self::$classRef->hasMethod($method); 
	}

	/**
	 * 获取类方法
	 * @param string $method 方法名 
	 * @return object
	 * @author wave
	 */
	static public function getMethod($method = ''){
	 	return self::$classRef->getMethod($method);
	}
	
	/**
	 * 类初始化
	 * @param  string $param 要实例化参数
	 * @return object
	 * @author wave
	 */
	static public function instance($param = ''){
		if($param != ''){
			return self::$classRef->newInstance($param);
		}
		return self::$classRef->newInstance();
	}
	
	/**
	 * 带参数类初始化
	 * @param Array $params
	 * @return object
	 * @author wave
	 */
	static public function instanceArgs($params = array()){
		return self::$classRef->newInstanceArgs($params);
	}

	/**
	 * 对初始类方法进行传递参数
	 * @param array $params 参数
	 * @return object
	 * @author wave
	 */
	static public function invokeArgs($params = array(),$object = ''){
		if($object){
			return self::$methodRef->invokeArgs($object,$params);
		}
		return self::$methodRef->invokeArgs(self::instance(),$params);
	}

	/**
	 * 对初始类方法进行传递参数
	 * @param array $params 参数
	 * @return object
	 * @author wave
	 */
	static public function invoke(){
			return self::$methodRef->invoke(self::instance());
	}
}

