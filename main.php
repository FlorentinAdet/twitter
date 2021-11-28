<?php
ini_set("display_errors","1");
/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
require_once 'vendor/autoload.php';
require_once 'src/mf/utils/AbstractClassLoader.php';
require_once 'src/mf/utils/ClassLoader.php';
$loader = new \mf\utils\ClassLoader('src');
$loader->register();

use \tweeterapp\View\TweeterView as TweeterView;
use \mf\auth\Authentification as Authentification;


$ini_array = parse_ini_file("conf/config.ini");

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $ini_array ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */

TweeterView::addStyleSheet("html/style.css");
$ctrl = new tweeterapp\control\TweeterController();

$router = new \mf\router\Router();

$router->addRoute('maison',
                  '/home/',
                  '\tweeterapp\control\TweeterController',
                  'viewHome',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('VoirTweet',
                  '/viewtweet/',
                  '\tweeterapp\control\TweeterController',
                  'viewTweet',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('VoirUser',
                  '/viewuser/',
                  '\tweeterapp\control\TweeterController',
                  'viewUserTweets',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('VoirFormTweet',
                  '/post/',
                  '\tweeterapp\control\TweeterController',
                  'viewForm',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('EnvoyerTweet',
                  '/send/',
                  '\tweeterapp\control\TweeterController',
                  'Envoie',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

//Route de connexion 
$router->addRoute('CheckCo',
                  '/check_login/',
                  '\tweeterapp\control\TweeterAdminController',
                  'checkLogin',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('Connexion',
                  '/login/',
                  '\tweeterapp\control\TweeterAdminController',
                  'login',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
                  
//Route de déconnexion 
$router->addRoute('Deconnexion',
                  '/logout/',
                  '\tweeterapp\control\TweeterAdminController',
                  'logout',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

//Route créer un compte
$router->addRoute('CreerCompte',
                  '/signup/',
                  '\tweeterapp\control\TweeterAdminController',
                  'signup',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('CheckCreerCompte',
                  '/check_signup/',
                  '\tweeterapp\control\TweeterAdminController',
                  'CheckSignup',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('TweetFollow',
                  '/following/',
                  '\tweeterapp\control\TweeterController',
                  'following',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);


$router->addRoute('Like',
                  '/like/',
                  '\tweeterapp\control\TweeterController',
                  'like',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
                  
$router->addRoute('Dislike',
                  '/dislike/',
                  '\tweeterapp\control\TweeterController',
                  'dislike',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('Follow',
                  '/follow/',
                  '\tweeterapp\control\TweeterController',
                  'follow',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('Followee',
                  '/followee/',
                  '\tweeterapp\control\TweeterController',
                  'followee',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('Follower',
                  '/follower/',
                  '\tweeterapp\control\TweeterController',
                  'follower',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->setDefaultRoute('/home/');

$router->run();

/* Après exécution de cette instruction, l'attribut statique $routes et
   $aliases de la classe Router auront les valeurs suivantes: */
/*
print_r(\mf\router\Router::$routes);
print_r(\mf\router\Router::$aliases);





/*
// requête des utilisateur
$requete = User::select();
$lignes = $requete->get();   exécution de la requête et plusieurs lignes résultat

foreach ($lignes as $v){
echo "<br>";
       echo "Identifiant = $v->id, Nom utilisateur = $v->username\n";

}



/*
// Requête des tweets classé par ordre de création
$lignes2 = Tweet::select()
                  ->orderBy('created_at')
                  ->get();

foreach ($lignes2 as $v){
  echo "<br>";
  echo "Identifiant = $v->id, Text = $v->text\n";

}

//Requête qui récupère les tweets ayant un score positif
echo "<br> <br>Requête Tweet positif</br>";
$lignes2 = Tweet::select()
                  ->where('score', '>', 0)
                  ->get();

foreach ($lignes2 as $v){
  echo "<br>";
  echo "Identifiant = $v->id, Text = $v->text\n";
}
/*
//Ajout d'un tweet

$v = new Tweet();
$v->text = "Le nouveau tweet incroyable";
$v->id = 74;
$v->author = 2;
$v->score = 10;
$v->created_at = "2021-08-31 09:59:35";
$v->update_at = "0000-00-00 00:00:00";
$v->save();

//Nouvel utilisateur
$u = new User();
$u->id = 11;
$u->fullname = "Jean Roger";
$u->username = "Jean";
$u->password = "oui123";
$u->level = 100;
$u->followers = 3;
$u->save();

$c = Tweet::where('id' ,'=', 63)->first();
$auteur = $c->author()->first();
$like = $c->likedBy()->get();
echo "<br><br>".$auteur ;
echo "<br><br>Liked by <br><br>".$like." <br><br>";

$g = User::where('id', '=', 10)->first();
$liste_tweet = $g->tweets()->get() ;
$tweetliked = $g->liked()->get();
$followauteur = $g->follows()->get();
$followofauteur = $g->followedBy()->get();
echo "<br><br>Liste tweet user : <br><br>";
foreach ($liste_tweet as $v){
echo $g->username." : ".$v->text."<br>";
}
echo "<br><br>Liste tweet like user : <br><br>";
foreach ($tweetliked as $v){
echo $v->text."<br>";
}
echo "<br><br>Liste personne que l'auteur suit : <br><br>";
foreach ($followauteur as $v){
echo $v->username."<br>";
}
echo "<br><br>Liste personne qui suivent l'auteur : <br><br>";
foreach ($followofauteur as $v){
echo $v->username."<br>";
}
*/
