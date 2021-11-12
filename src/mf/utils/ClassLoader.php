<?php

namespace mf\utils;

Class ClassLoader extends AbstractClassLoader{

  public function loadClass($classname){
    $src = $this->getFilename($classname);
    $src = $this->makePath($src);
    if(file_exists($src)==true){
      require_once($src);
    }
  }

  public function makePath(string $filename){
    $p = $this->prefix."\\".$filename;
    return str_replace("\\",DIRECTORY_SEPARATOR,$p);
  }

  public function getFilename(string $classname){
    $p = str_replace("\\",DIRECTORY_SEPARATOR,$classname);
    $p .= ".php";
    return $p;
  }
}
