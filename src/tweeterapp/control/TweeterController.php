<?php

namespace tweeterapp\control;
use \tweeterapp\model\Tweet as Tweet;
use \tweeterapp\model\User as User;
use \tweeterapp\model\Like as Like;
use \tweeterapp\model\Follow as Follow;
use \tweeterapp\View\TweeterView as TweeterView;

/* Classe TweeterController :
 *
 * Réalise les algorithmes des fonctionnalités suivantes:
 *
 *  - afficher la liste des Tweets
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis
 *  - évaluer un Tweet
 *  - suivre un utilisateur
 *
 */

class TweeterController extends \mf\control\AbstractController {


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


    /* Méthode viewHome :
     *
     * Réalise la fonctionnalité : afficher la liste de Tweet
     *
     */

    public function viewHome(){

        /* Algorithme :
         *
         *  1 Récupérer tout les tweet en utilisant le modèle Tweet
         *  2 Parcourir le résultat
         *      afficher le text du tweet, l'auteur et la date de création
         *  3 Retourner un block HTML qui met en forme la liste
         *
         */
         $tweets = Tweet::orderBy('id', 'DESC')->get();
         $vue = new TweeterView($tweets);
         $vue->render("home");
    }


    /* Méthode viewTweet :
     *
     * Réalise la fonctionnalité afficher un Tweet
     *
     */

    public function viewTweet(){

        /* Algorithme :
         *
         *  1 L'identifiant du Tweet en question est passé en paramètre (id)
         *      d'une requête GET
         *  2 Récupérer le Tweet depuis le modèle Tweet
         *  3 Afficher toutes les informations du tweet
         *      (text, auteur, date, score)
         *  4 Retourner un block HTML qui met en forme le Tweet
         *
         *  Erreurs possibles : (*** à implanter ultérieurement ***)
         *    - pas de paramètre dans la requête
         *    - le paramètre passé ne correspond pas a un identifiant existant
         *    - le paramètre passé n'est pas un entier
         *
         */
        if(isset($_GET["id"])){
            $tweet = Tweet::select()
                            ->where('id',$_GET['id'])
                            ->first();
            if(!empty($tweet)){ //On test s'il y a bien un tweet a afficher
              $vue = new TweeterView($tweet);
              $vue->render(1);
            }
        }
    }


    /* Méthode viewUserTweets :
     *
     * Réalise la fonctionnalité afficher les tweet d'un utilisateur
     *
     */

    public function viewUserTweets(){
      /*
        *
        *  1 L'identifiant de l'utilisateur en question est passé en
        *      paramètre (id) d'une requête GET
        *  2 Récupérer l'utilisateur et ses Tweets depuis le modèle
        *      Tweet et User
        *  3 Afficher les informations de l'utilisateur
        *      (non, login, nombre de suiveurs)
        *  4 Afficher ses Tweets (text, auteur, date)
        *  5 Retourner un block HTML qui met en forme la liste
        *
        *  Erreurs possibles : (*** à implanter ultérieurement ***)
        *    - pas de paramètre dans la requête
        *    - le paramètre passé ne correspond pas a un identifiant existant
        *    - le paramètre passé n'est pas un entier
        *
        */
      if(isset($_GET['id'])){
          $user = User::where('id',$_GET['id'])->first();
            $vue = new TweeterView($user);
            $vue->render(2);
      }
    }
    /*Méthode viewUserTweets :
    *
    * Réalise la fonctionnalité afficher le formulaire de tweet
    *
    */
    public function viewForm(){
      $vue = new TweeterView("");
      $vue->render(3);
    }

    public function following(){
      $user = User::where('username',$_SESSION['user_login'])->first();
      $vue = new TweeterView($user);
      $vue->render("Following");
    }

    public function like(){ 
      $user = User::where('username','=',$_SESSION['user_login'])->first();
      if(Like::where('user_id', '=', $user->id)->where('tweet_id', '=', $_GET["idtweet"])->first()!=null){
        $tweet = Tweet::where("id",'=',$_GET['idtweet'])->first();
      }else{
        $like = new Like();
        $like->user_id= $user->id;
        $like->tweet_id=$_GET['idtweet'];
        $like->save();
        $tweet = Tweet::where("id",'=',$_GET['idtweet'])->first();
        $likes = Like::where("tweet_id",'=',$_GET['idtweet'])->count();
        $tweet->score = $likes;
        $tweet->save();
      }
      $vue = new TweeterView($tweet);
      $vue->render(1);
    }

    public function dislike(){
      $user = User::where('username','=',$_SESSION['user_login'])->first();
      $tweet = Tweet::where("id",'=',$_GET['idtweet'])->first();
      if(Like::where('user_id', '=',  $user->id)->where('tweet_id', '=', $_GET["idtweet"])->first()!=null){
        $like  = Like::where('user_id', '=',  $user->id)->where('tweet_id', '=', $_GET["idtweet"])->first();
        $like->delete();
        $likes = Like::where("tweet_id",'=',$_GET['idtweet'])->count();
        $tweet->score = $likes;
        $tweet->save();
      }
      $vue = new TweeterView($tweet);
      $vue->render(1);
    }

    public function follow(){
      $user = User::where('username',$_SESSION['user_login'])->first();
      if(isset($_GET['idtweet'])){
        $tweet = Tweet::where("id",'=',$_GET['idtweet'])->first();
      }
      if($user->alreadyFollow($user->id, $_GET['iduser']) == false){
        if($_GET['iduser'] != $user->id){
          $follow = new Follow();
          $follow->follower = User::where('username','=',$_SESSION["user_login"])->first()->id;
          $follow->followee = $_GET['iduser'];
          $follow->save();
          $nbfollow = Follow::where('followee','=',$user->id)->count();
          $user->followers = $nbfollow; 
          $user->save();
        }
      }
      if(isset($_GET['idtweet'])){
        $vue = new TweeterView($tweet);
        $vue->render(1);
      }else{
        $vue = new TweeterView($user);
        $vue->render("Followee");
      }
    }
    public function followee(){
      $user = User::where('username',$_SESSION['user_login'])->first();
      $vue = new TweeterView($user);
      $vue->render("Followee");
    }
    public function follower(){
      $user = User::where('username',$_SESSION['user_login'])->first();
      $vue = new TweeterView($user);
      $vue->render("Follower");
    }
    public function Envoie(){
      $user = User::where("username",'=',$_SESSION['user_login'])->first();
      $t = new Tweet();
      $t->text = $_POST['text'];
      $t->author = $user->id;
      $t->save();
      $router = new \mf\router\Router();
      $urlEnvoie = $router->executeRoute('default');
    }
}
