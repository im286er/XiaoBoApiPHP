<?php
/**
 * 路由加载控制器
 * @author wave
 */

class Route {

	/**
	 * 保存控制器路径
	 * @author wave
	 */
	public $controllerPath = '';

	/**
	 * 分组名字
	 */
	public $groupName = DEFAULT_PATH;

	/**
	 * 类名字
	 * @author wave
	 */
	public $className = '';

	/**
	 * 方法名字
	 * @author wave
	 */
	public $actionName = '';

	/**
	 * 保存URL参数的数组
	 * @author wave
	 */
	public $getUrlParamArr = array();

	/**
	 * 标示定义路由
	 * @author wave
	 */
	public $flag = false;


	/**
	 * 标示初始化
	 * @author wave
	 */
	public $link = 0;


	/**
	 * 初始化URL参数
	 * @author wave
	 */
	public function coustructs() {
		$this->controllerPath = $this->getPath().APP_ROOT_PATH;
		$this->getUrlParamArr =	$this->expUrlParamArr($this->getUrlParam());
		if(count($this->getUrlParamArr) == 1) {
			$this->getUrlParamArr = $this->expUrlParamArr($this->getUrlParam().$this->getDefualtUrl());
		}
		Error::init($this->getUrlParamArr);
		//$this->init();
	}


	/**
	 * 设置路由
	 * @param string $groupName  分组
	 * @param string $className  类名
	 * @param string $actionName 方法名
	 * @param Array  $params  参数
	 * @author wave
	 */
	public function setRoute($groupName, $className,$actionName,$params = array()){
			if($groupName && $className && $actionName){
				$this->getUrlParamArr[0] = $groupName;
				$this->getUrlParamArr[1] = $className;
				$this->getUrlParamArr[2] = $actionName;
				$this->getUrlParamArr = array_merge($this->getUrlParamArr,$params);
				$this->flag = true;
				$this->init();
			}

	}


	/**
	 * 拆分URL为数组
	 * @author wave
	 */
	protected function expUrlParamArr($dataStr = '' , $exp = ROUTE_DS) {
		$dataStr = str_replace(array('//',"index.php"), array($exp,"") , $dataStr);
		$getUrlParamArr = explode($exp, $dataStr);
		$getUrlParamArr = $this->filterArr($getUrlParamArr);
		return !empty($getUrlParamArr) ? $getUrlParamArr : false;
	}


	/**
	 * 判断文件路径是否存在
	 * @return boolen or String
	 * @author wave
	 */
	protected function isPath() {
		$controllerPath =  $this->controllerPath . ROUTE_DS . $this->getUrlParamArr[0];
		$controllerPath = str_replace('index.php', '', $controllerPath);
		//判断不是目录文件
		if( !file_exists($controllerPath) ) {
			return false;
		} 

		if( !is_dir($controllerPath) ) {
			return false;
		}
		$this->groupName = $this->getUrlParamArr[0];
		return $this->getUrlParamArr[0];
	}

	/**
	 * 判断控制器文件是否存在
	 * @return boolen or String
	 * @author wave
	 */
	protected function isController() {
		$defaultPath = ($this->isPath() !== false) ? $this->isPath() : DEFAULT_PATH;
		$defaultFile = ($this->isPath() !== false) ? $this->getUrlParamArr[1] : $this->getUrlParamArr[0];
		$controllerPath =  $this->controllerPath . ROUTE_DS . 
				$defaultPath . ROUTE_DS . 
				CONTROLLER . ROUTE_DS . 
				$defaultFile . 
				CON_SUFFOIX;
		$controllerPath = str_replace('index.php', '', $controllerPath);
		//判断是否是控制器文件
		if( !file_exists($controllerPath)) {
			return false;
		} 
		$this->className = $defaultFile;
		return $controllerPath;
	}



	/**
	 * 初始化类
	 * @author wave
	 */
	public function init() {
		if($this->link === 0){
			++$this->link;
			$this->coustructs();
			$actionName = ($this->isPath() !== false) ? $this->getUrlParamArr[2] : $this->getUrlParamArr[1];
			$className = $this->isClass();
			($this->isPath() !== false)  ? array_splice($this->getUrlParamArr,0,3) : array_splice($this->getUrlParamArr,0,2); 
			if ( !empty($className) ) {
				$obj = new $className();
			}

			if ( isset($obj) && is_object($obj) ) {
				if($this->isAction($obj,$actionName)){
					$this->getUrlParamArr = !empty($this->getUrlParamArr) ? $this->getUrlParamArr : $this->getParam();
					return call_user_func_array(array($obj,$actionName), $this->getUrlParamArr);
				}
			}
			throw new XiaoBoException('初始化失败');
		}
		
	}


	/**
	 * 判断类的方法是否存在
	 * @return boolen
	 * @author wave
	 */
	protected function isAction($obj,$actionName) {
		if( !method_exists($obj,$actionName) ) {
			throw new XiaoBoException('方法不存在');
		}
		$this->actionName = $actionName;
		return true;
	}

	/**
	 * 判断类是否存在
	 * @return String
	 * @author wave
	 */
	protected function isClass() {
		$controllerPath = $this->isController();
		$this->load($controllerPath);
		$controllerFile = basename($controllerPath);
		$controllerClass = rtrim($controllerFile,'.php');
		if(!class_exists($controllerClass) ) {
			throw new XiaoBoException('控制器不存在');
		} 
		return $controllerClass;
	}

	/**
	 * 获取设置默认url
	 * @return string
	 * @author wave
	 */
	protected function getDefualtUrl(){
		$url = DEFAULT_ROUTE;
		if(empty($_GET)){
			$urlArr = array_values(array_filter(explode('/', $url)));
			$_GET[C] = $urlArr[0];
			$_GET[A] = $urlArr[1];
		}
		
		return $url;
	}



	/**
	 * 获取url参数
	 * @return String
	 * @author wave
	 */
	public  function getUrlParam($flag = true) {
		//$url = $this->getDefualtUrl();
		$getParam = $this->ReturnGetParam();
		if ( !empty($_SERVER['ORIG_PATH_INFO']) ) {
			$url = $_SERVER['ORIG_PATH_INFO'];
		} else if ( !empty($_SERVER['PATH_INFO']) ) {
			$url = $_SERVER['PATH_INFO'];
		} else if ( !empty($_SERVER['REQUEST_URI']) ) {
			$url = $_SERVER['REQUEST_URI'];
		}
		if($getParam && $flag) {
			$url = $getParam;
		}
		if($url ==  strtolower(ROUTE_DS.basename($this->getPath()).ROUTE_DS) ) {
			$url = $this->getDefualtUrl();
		}
		if($this->flag && $flag){
			$url = implode('/', $this->getUrlParamArr);
		}

		return $url;
	}

	/**
	 * 返回GET参入参数
	 * @author wave
	 */
	protected function ReturnGetParam() {
		$getUrl = '';

		if( isset($_GET[C]) && isset($_GET[A]) ) {
			$getUrl = ROUTE_DS . $_GET[C] . ROUTE_DS . $_GET[A] . ROUTE_DS;
		}

		if(isset($_GET[G]) ) {
			$getUrl =  ROUTE_DS . $_GET[G] . (empty($getUrl) ? ROUTE_DS : $getUrl);
		}

		return $getUrl;
	}

	/**
	 * 获取URL参数
	 * @return Array
	 * @author wave
	 */
	protected function getParam() {
		$get = !empty($_GET) ? $_GET : array();
		if( isset($get[C]) && isset($get[A]) ) {
			unset($get[C]);
			unset($get[A]);
		}
		if( isset($get[G]) ) {
			unset($get[G]);
		}
		return $get;
	}



	/**
	 * 获取服务器相对路径目录
	 * @return String 
	 * @author wave
	 */
	protected function getPath() {
		$appPath = dirname(dirname(__FILE__));
		$currPath = basename($appPath);
		$searchArr = array('\\',$currPath);
		$replaceArr = array(ROUTE_DS,'');
		$appPath = str_replace($searchArr, $replaceArr, $appPath);
		return $appPath;
	}

	/**
	 * 引入文件
	 * @param Sting $filePath 文件相对路径
	 * @author wave
	 */
	protected function load($filePath = '') {
		$filePath  = str_replace('//', '/', $filePath);
		static $fileArr = array();
		if( empty($fileArr[$filePath]) && file_exists($filePath) ) {
			$fileArr[$filePath] = $filePath;
		}

		if( !empty($fileArr[$filePath]) ) {
			require $fileArr[$filePath];
		}	
	}

	/**
	 * 过滤空的数组
	 * @param Array $arr 要过滤的空数组
	 * @return Array 
	 * @author wave
	 */
	protected function filterArr($arr){
		if ( !empty($arr) ) {
			return	array_values(array_filter($arr));
		}
		return array();
	}


}
