<?php
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
//Route pour voir les personnes qui nous suivent
$router->addRoute('Followee',
                  '/followee/',
                  '\tweeterapp\control\TweeterController',
                  'followee',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
//route pour voir les personnes que l'on suit
$router->addRoute('Follower',
                  '/follower/',
                  '\tweeterapp\control\TweeterController',
                  'follower',
                  \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->setDefaultRoute('/home/');

$router->run();
