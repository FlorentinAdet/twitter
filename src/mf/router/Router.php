<?php

namespace mf\router;
use \mf\auth\Authentification as Authentification;

class Router extends AbstractRouter {
    static $routes = array();
    static $aliases = array();
    private $level;
    public function __construct(){
      AbstractRouter::__construct();
    }

    public function addRoute($name, $url, $ctrl, $mth,$level){
      $tab[]= $ctrl;
      $tab[]= $mth;
      $tab[]= $level;
      self::$routes[$url] = $tab;
      self::$aliases[$name]= $url;
    }

    public function setDefaultRoute($url){
      self::$aliases['default'] = $url;
    }

    public function run(){
      session_start();
      $auth = new Authentification();
      $url = self::$aliases['default'];
      $path = $this->http_req->path_info ;
      if(isset($path)){
        $url = $this->http_req->path_info;
      }
      $ctrl = self::$routes[$url][0];
      $mth = self::$routes[$url][1];
      if($auth->checkAccessRight(self::$routes[$url][2])){
        $c = new $ctrl();
        $c->$mth();
      }else{
        $this->executeRoute('default');
      }
    }

    static public function executeRoute($route){
      $url = self::$aliases[$route];
      $ctrl = self::$routes[$url][0];
      $mth = self::$routes[$url][1];
      $c = new $ctrl();
      $c->$mth();
    }

    public function urlFor($route_name, $param_list=[]){
        $url =  self::$aliases[$route_name];
        $url = $this->http_req->script_name."".$url;
        $j=1;
        if(!empty($param_list)){
          $url .= "?";
          foreach($param_list as $param => $value){
            $url .= $param."=".$value;
            if($j != count($param_list)){
              $url .="&amp;";
            }
            $j++;
          }
        }
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
