<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class controller{
    protected $db;#数据库实例化对象属性
    protected  $models=array();#模型实例化对象数组
    protected  $components=array();#数据组件实例化对象数组
    protected  $layout;

    /**
     * 私有拦截器
     * 私有设置处理魔法方法
     * @param type $key
     * @param type $value
     */
    public function __set($key,$value){
         $this->$key=$value;
		 unset($key,$value);
    }
    /**
     * 私有捕获器
     * 获取私有处理魔法方法
     * @param type $key
     * @return type
     */
    public function __get($key){
        return $this->$key;
    }
    /**
     * 判断是否是get提交数据
     * @return type
     */
   protected  function isGet(){
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'get') ? true : false;
    }
    /**
     * 获取get值
     * @param type $key
     * @return type
     */
    protected function _get($key){
         if($this->isGet()){
             $getVal=isset($_GET[$key])?$_GET[$key]:null;
			 unset($key);
             $getVal=filterKword($getVal);
             return addslashes($getVal);
         }
    }
    /**
     * 判断是否是post提交数据
     * @return type
     */
    protected  function isPost(){
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? true : false;
    }
    /**
     * 获取post提交的数据
     * @param type $data
     */
    protected function _post($data=array()){
         if($this->isPost()){
             $data=empty($data)?$_POST:$data;
           foreach ( $data as $key => $value ){
                 if (get_magic_quotes_gpc()){
                     $value = htmlspecialchars( stripslashes((string)$value));
                 }else{
                     $value = htmlspecialchars( addslashes((string)$value) );
                 }
                 $data[$key]=$value;
             }
            return $data;
         }
    }

    /**
     * 判断是否是ajax
     * @return boolean
     */
   protected function isAjax(){
       if ($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==
            'xmlhttprequest'){
            return true;
        }
    }
    /**
      * 视图渲染层
      * @param type $template
      * @param type $args
      */
    protected  function render($tpl='',$vars=array(),$cacheId=''){
        $smarty=template::setTpl();#获取smarty模版对象
        $layoutName='';#layout模板名
        $parentLayout='';#父级layout模板文件
        #处理模版路径
       if(empty($tpl)){
             $control=ucfirst($GLOBALS['router']->getControl());#获取控制器
             $action=$GLOBALS['router']->getAction();#获取动作名
             $tpl=$control.'/'.$action.'';
             unset($router,$control);
         }else{
             $tpl=  ucfirst($tpl);#首字母转换成大写
             $arr=explode('/', $tpl);
             $action=end($arr);#得到模板路径中的action
             unset($arr);
         }
         #处理参数
         if( ! empty ($vars)){
              foreach ($vars as $k=>$v){
                     $smarty->assign($k,$v);
               }
           }
        #构建模板文件名
       $fileName=config::readConfig('Smarty','php_templates')== true?$tpl.'.php':$tpl.'.html';
        #最高级layout
        $layoutName=config::readConfig('app','layout');
         #如果是后台就手动继承模板
        $routerModule=$GLOBALS['router']->getModule();
        if($routerModule){
            empty($cacheId)?$smarty->display($fileName,$action): $smarty->display($fileName,$cacheId);
            unset($routerModule);
        }else if(empty($layoutName)){#不存在顶级layout,也就是说配置文件中没有配置layout
             if($this->layout){#是否存在当前控制器指定layout
                $parentLayout='layout/'.$this->layout.'.html';
                empty($cacheId)?$smarty->display('extends:'.$parentLayout.'|'.$fileName,$action): $smarty->display('extends:'.$parentLayout.'|'.$fileName,$cacheId);
            }else{
                 empty($cacheId)?$smarty->display($fileName,$action): $smarty->display($fileName,$cacheId);
            }
        }else{#存在顶级layout
            $parentparentLayout='layout/'.$layoutName.'.html';
           if($this->layout){#如果也存在当前layout
               $parentLayout='layout/'.$this->layout.'.html';
               #所以就要继承两次
               empty($cacheId)?$smarty->display('extends:'.$parentparentLayout.'|'.$parentLayout.'|'.$fileName,$action): $smarty->display('extends:'.$parentparentLayout.'|'.$parentLayout.'|'.$fileName,$cacheId);
            }else{#否则就是只有顶级没有当前级别的layout
               empty($cacheId)?$smarty->display('extends:'.$parentparentLayout.'|'.$fileName,$action): $smarty->display('extends:'.$parentparentLayout.'|'.$fileName,$cacheId);
           }
         }
		 $smarty=null;
         unset($tpl,$vars,$cacheId,$fileName,$action,$layoutName,$parentLayout,$parentparentLayout,$smarty);
      }
      /**
       *  url 重定向
       * @param type $url
       * @param type $status
       */
      protected function redirect($url = null, $status = 302){
        header('Status: ' . $status); #header头发送状态status信息为 302#
        header('Location: ' . HTTP_URL.str_replace('&amp;', '&', $url));
		unset($url,$status);
        exit();
    }
    /**
     * ajax返回
     * @param unknown_type $ajaxReturnType  返回的类型  默认为 string  可选有 json
     * @param unknown_type $ajaxReturnData  返回的数据  注意： 如果是xml 或者 json 那么数据必须是数组
     */
    protected function ajaxReturn(&$ajaxReturnData = null,$ajaxReturnType = 'string'){
        if ($ajaxReturnType == 'string'){
            return $ajaxReturnData;
        }elseif ($ajaxReturnType == 'json'){
            foreach ($ajaxReturnData as $key => $value){
                if (is_array($value)){
                    $this->ajaxReturn( $value,'json');
                }
                $json_value = urlencode($value);
                $jsonstr = json_encode($json_value);
                $json = urldecode($jsonstr);
            }
            return $json;
        }
    }
    /**
     * 手动调用数据库链接对象
     * @return type
     */
    protected function  database(){
        return database::init();
    }
    /**
     * 在什么动作之前
     */
    protected function before_(){}
    /**
     * 在什么动作之后
     */
   protected function  after_(){}
 /**
  * 控制器调用模型
  * @param type $modelName
  * @return type 
  */  
protected  function  model($modelName){
   if(is_string($modelName)){
           $UmodelName=ucfirst($modelName).'Model';
            $umodle=engine::load($UmodelName);
           unset($UmodelName,$modelName);
           return $umodle;
     }else if(is_array($modelName)){
         $objects=array();
         foreach($modelName as $model){
              $UmodelName=ucfirst($model).'Model';
               $umodle=engine::load($UmodelName);
               $objects[]=$umodle;
            }
            unset ($modelName,$model,$UmodelName);
            return $objects;
     }
}
 protected   function components(){
     if( ! empty($this->components)){
         #读取所有模块
         $modules=config::readConfig('modules');
         $incldePath=array();
         foreach($modules as $module){
             $dir=APP_PATH."modules/".$module;
             if(is_dir($dir)){
                 $incldePath[]=$dir."/controllers/components/";
             }
         }
         $incldePath[]=APP_PATH."controllers/components/";
         foreach($incldePath as $path){
             foreach($this->components as $component ){
                 $file=$path.ucfirst($component).'Component.php';
                 if(file_exists($file)){
                     include_once $file;
                 }
             }
         }
         unset($modules,$incldePath,$file);
     }
 }


public function __construct() {
    $this->db=database::init();;#初始化控制的数据库
    $this->components();#初始化加载components
}
      /**
       * 获取url段
       */
     protected  function segment($segment=''){
             $module=$GLOBALS['router']->getModule();#获取模块
             $control=ucfirst($GLOBALS['router']->getControl());#获取控制器
             $action=$GLOBALS['router']->getAction();#获取动作名
             $module=empty($module)?null:$module;
             $control=empty($control)?'index':$control;
             $action=empty($action)?'index':$action;
             switch ($segment){
                  case 'm': $ment=$module; break;
                  case 'c': $ment=$control;break;
                  case 'a': $ment=$action; break;
                  default:  $ment=array($module,$control,$action);break;
              }
              unset($module,$control,$action,$segment);
          return $ment;
     }

}