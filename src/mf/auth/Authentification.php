<?php

namespace mf\auth;

class  Authentification extends AbstractAuthentification {
  public function __construct(){
    if(isset($_SESSION['user_login'])){
      $this->user_login = $_SESSION['user_login'];
      $this->access_level = $_SESSION['access_level'];
      $this->logged_in = true;
    }else{
      $this->user_login = null;
      $this->access_level = self::ACCESS_LEVEL_NONE;
      $this->logged_in = false;
    }
  }
    public function updateSession($username, $level){
      $this->user_login = $username;
      $this->access_level = $level;
      $_SESSION['user_login'] = $username;
      $_SESSION['access_level'] = $level;
      $this->logged_in = true;
    }
    public function logout(){
      unset($_SESSION['user_login']);
      unset($_SESSION['access_level']);
      $this->user_login = null;
      $this->access_level = self::ACCESS_LEVEL_NONE;
      $this->logged_in = false;
    }
    public function checkAccessRight($requested){
      if($this->access_level >= $requested ){
        return true;
      }else{
        return false;
      }
    }
    public function login($username, $db_pass, $given_pass, $level){
      if(password_verify($given_pass,$db_pass)===false){
          throw new exception\AuthentificationException();
      }else{
        self::updateSession($username, $level);
      }
    }
    public function hashPassword($password){
      return password_hash($password,\PASSWORD_DEFAULT);
    }
    public function verifyPassword($password, $hash){
      return password_verify($password,$hash);
    }
  }