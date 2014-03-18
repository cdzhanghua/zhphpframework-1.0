<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class router{
    private $module; #模块
    private $controller;#控制器
    private $action;#动作
    private $params;#参数
    /**
     * 构造函数
     */
    public function __construct(){
      $this->parseUrl();#分析路由并url安全验证
      $this->filterControl();#控制器安全验证
      $this->parseStatic();#处理伪静态
    }
    /**
     * 解析路由并url安全验证
     */
    private function parseUrl(){
        $parse=parse_url(APP_URI);#伪静态自动过滤
        #初始化处理
           if($parse['path'] == '/' || $parse['path'] == '/index.php' || $parse['path'] == PROJECT_NAME.'/' || $parse['path'] === PROJECT_NAME.'/index.php' ){ #为空处理
              if(!isset($parse['query'])){
                 $this->_empty();#为空的时候不需要安全验证
                  $this->params[]='error_0';#无错误
                  return;
               }
            }
      #get路由处理 与pathinfo路由处理 
        if(isset($parse['query'])){#get
              #关键词过滤
            if(filterKword() == false){#url非法过滤,如果存在非法字符就设置默认页面
                $this->_empty();
                $this->params[]='error_1';#url包含非法字符错误
            }
            $httpRequest=$_GET;
            $keys= array_keys($httpRequest);
           if($keys[0] === 'm'){#表示访问modules/controller/action
                $this->module=$httpRequest[$keys[0]];
                $this->controller=$httpRequest[$keys[1]];
                $this->action=$httpRequest[$keys[2]];
                array_shift($httpRequest);array_shift($httpRequest);array_shift($httpRequest);
                $this->params=$httpRequest;
            }
            if($keys[0] === 'c'){#表示访问controller/action
                $this->controller=$httpRequest[$keys[0]];
                $this->action=$httpRequest[$keys[1]];
                array_shift($httpRequest);array_shift($httpRequest);
                $this->params=$httpRequest;
            }
             unset($isfilterUrl,$httpRequest,$keys);
          }else{#pathInfo格式
             if(strpos($parse['path'], 'index.php')){#有index.php的情况下
                 $httpRequest=$this->parseDelimiter($parse);#通过url分隔符
                 if(empty($httpRequest[0]) || $httpRequest[0]=='index.php'){
                     array_shift($httpRequest);
                 }
                 if(empty($httpRequest[0]) || $httpRequest[0]=='index.php' ){
                     array_shift($httpRequest);
                 }
                 if(count($httpRequest) == 1 ){
                    $this->_empty(); 
                    return;
                 }
                }else{#无index.php的情况下
                 $httpRequest=$this->parseDelimiter($parse);
                   if(empty($httpRequest[0])){
                      array_shift($httpRequest);
                   }
                 if(count($httpRequest) == 1 ){
                    $this->_empty(); 
                    return;
                 }
              }
             $modules=config::readConfig('modules');
             $boolean=in_array($httpRequest[0],$modules);
               if($boolean){ #表示访问modules/controller/action
                   $this->module=$httpRequest[0];
                   $this->controller=$httpRequest[1];
                   $this->action=$httpRequest[2];
                   array_shift($httpRequest);array_shift($httpRequest);array_shift($httpRequest);
                   $this->params=$httpRequest;
               }else{#表示访问controller/action
                   $this->controller=$httpRequest[0];
                   $this->action=$httpRequest[1];
                   array_shift($httpRequest);array_shift($httpRequest);
                   $this->params=$httpRequest;
               }
               unset($modules,$boolean,$httpRequest,$parse);
             }
          }
       /**
        * 安全验证controller
        * 支持redis
        */
       private function filterControl(){
           $controllers=config::readConfig('controller');#获取已经注册的控制器
           $develop=config::readConfig('develop');#获取开发模式
           $checkControll=isset($develop['checkControll'])?$develop['checkControll']:false;#是否需要严格验证控制器
           if($checkControll){
               if(is_array($controllers)){
                   if(in_array($this->controller, $controllers) == false){#判断是否注册控制器,返回false就是没有注册
                       $this->_empty();#重新赋值
                       $this->params[]='error_2';#控制器未注册,非法访问文件错误
                   }else{
                       $key=array_search($this->controller, $controllers);#返回键
                       if(is_string($key)){;#重新解析控制器,通过值获取key判断是否是数字或者是关联
                           $this->controller=$key;
                       }
                   }
               }
           }else{
               if(is_array($controllers)){
                   if(in_array($this->controller, $controllers)){#判断是注册控制器
                       $key=array_search($this->controller, $controllers);#返回键
                       if(is_string($key)){;#重新解析控制器,通过值获取key判断是否是数字或者是关联
                           $this->controller=$key;
                       }
                   }
               }
            }
           unset($controllers,$develop,$checkControll);
         }
         /**
          *处理伪静态
          */
         private function parseStatic(){
              #分析完后获取到 action 或者参数 然后在处理伪静态
              $Static_suffix=config::readConfig('parse','Static_suffix');#得到伪静态
              $end=count($this->params) > 0?end($this->params):null;#判断是否有参数
              $length2=strlen($Static_suffix);#得到伪静态长度
              if(is_null($end)){#如果参数,
                   $pos=strpos($this->action, $Static_suffix);
                   if($pos !== false){
                      $length=strlen($this->action);
                      $this->action=substr($this->action, 0,$length-$length2);
                   }
                  unset($pos);
              }else{#有参数
                   $pos=strpos($end, $Static_suffix);#判断最后一个元素中是否存在伪静态
                   if($pos !== false){ #如果不等于 false 就说明有
                       array_pop($this->params);#如果存在就删除最后一个
                       $end=str_ireplace($Static_suffix, '', $end);#替换去掉 .html 伪静态
                       array_push($this->params, $end);#将新数据重新添加到最后一个元素中
                    }
                  unset($pos);
              }
             unset($Static_suffix,$end,$length2);
         }
         
         /**
          * uri初始化或者为空的情况
          */
         private  function _empty(){
               $parse=config::readConfig('parse');
                $this->module=$parse['m'];
                $this->controller=$parse['c'];
                $this->action=$parse['a'];
                unset($parse);
         }
         /**
          * 解析路由分割符号
          * @param type $parse
          * @return type
          */
         private function parseDelimiter($parse){
                 $parseconfig=config::readConfig('parse');
                 $delimiter=isset($parseconfig['url_Delimiter'])?$parseconfig['url_Delimiter']:'/';
                 $parse['path']=  str_ireplace(PROJECT_NAME, '', $parse['path']);
                 $httpRequest=explode($delimiter, $parse['path']);
                 if($delimiter != '/'){
                    $httpRequest[0]=  str_ireplace('/', '', $httpRequest[0]);
                 }else{
                   array_shift($httpRequest);  
                 }
                  unset($parseconfig,$delimiter,$parse);
                 return  $httpRequest;
         }
          /**
          * 
          * @return type
          */
         public function getModule(){
           return empty($this->module)?'':$this->module;
       }
       /**
        * 
        * @return type
        */
       public function getControl(){
            return empty($this->controller)?'index':$this->controller;
       }
       /**
        * 
        * @return type
        */
       public function getAction(){
           return empty($this->action)?'index':$this->action;
       }
       /**
        * 
        * @return type
        */
       public function getParams(){
           return $this->params;
       }
      
 
}