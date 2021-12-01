<?php

namespace tweeterapp\control;


use \tweeterapp\model\User as User;
use \tweeterapp\View\TweeterView as TweeterView;
use \mf\router\Router as Router;
use \mf\auth\Authentification as Authentification;

class TweeterAdminController extends \mf\control\AbstractController {

    
    
    /* Constructeur :
     *
     * Appelle le constructeur parent
     *
     * c.f. la classe \mf\control\AbstractController
     *
     */

    public function __construct(){
        parent::__construct();
    }
    /**
     * Méthode login
     * 
     *  Réalise la fonctionnalité d'affichage de la page de login
     */
   
    public function login(){
        $vue = new TweeterView("");
        $vue->render("Connexion");
    }
     /**
     * Méthode login
     * 
     *  Réalise la fonctionnalité de connexion
     */
    public function checkLogin(){  
        $user = User::where('username',filter_var($_POST['user_login'], FILTER_SANITIZE_STRING))->first();
        if(!empty($user)){
            $auth = new Authentification();
            try{
                $auth->login($user->username, $user->password, $_POST['user_pass'], $user->level);
            }catch(\Exception $e){
                $vue = new TweeterView($user);
                $vue->render("Connexion");
                die($e->getMessage());
            }
            $vue = new TweeterView($user);
            $vue->render("Follower");
        }else{
            Router::executeRoute('Connexion');
            echo "Login ou mot de passe incorrect";
        }
    }
    /**
     * Méthode logout
     * 
     *  Réalise la fonctionnalité de déconnexion
     */
    public function logout(){ 
        $auth = new Authentification();
        $auth->logout();
        Router::executeRoute('Connexion');
    }
    /**
     * Méthode signup
     * 
     *  Réalise la fonctionnalité d'affichage d'enregistrement
     */
    public function signup(){
        $vue = new TweeterView("");
        $vue->render("Signup");
    }
    /**
     * Méthode CheckSignup
     * 
     *  Réalise la fonctionnalité de création d'un compte
     */
    public function CheckSignup(){
        if($_POST['user_pass']==$_POST['password_verify']){
            if(User::where('username','=',$_POST['user_name'])->first()){
                $e = new \mf\auth\exception\AuthentificationException("username déjà utilisé"); 
                Router::executeRoute('CreerCompte');
                echo $e->getMessage();
            }else{
                $user = new User();
                $user->fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING );
                $user->username = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING );
                $user->password = password_hash($_POST['user_pass'], \PASSWORD_DEFAULT);
                $user->level = 100;    
                $user->followers = 0;  
                $user->save(); 
                Router::executeRoute('default');
            }
        }else{
            $e = new \mf\auth\exception\AuthentificationException("Les mots de passe ne correspondent pas"); 
            Router::executeRoute('CreerCompte');
            die($e->getMessage());
        }
    }
   
}
