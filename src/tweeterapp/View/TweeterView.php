<?php

namespace tweeterapp\view;

class TweeterView extends \mf\view\AbstractView {

    /* Constructeur
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct( $data ){
        parent::__construct($data);
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */
    private function renderHeader(){
        $router = new \mf\router\Router();        
        
        $res = "<header class='theme-backcolor1'>
                    <h1>MiniTweeTR</h1>
                    <nav id='nav-menu'>  ".$this->renderTopMenu()."</nav>
            </header>";
        return $res;
    }

    private function renderTopMenu(){
        $router = new \mf\router\Router();
        $auth = new \mf\auth\Authentification();   
        if(isset($_SESSION['user_login'])){    
            $urlfollow = $router->urlFor("TweetFollow");
            $urldeco = $router->urlFor("Deconnexion");
            $urlfollower = $router->urlFor("Follower");
            return "<a class='button' href='".$urlfollow."'><img  src='../../html/home.png' width='45' height='45'></a>
            <a class='button' href='".$urlfollower."'><img  src='../../html/followees.png' width='45' height='45'></a>
            <a class='button' href='".$urldeco."'><img  src='../../html/logout.png' width='45' height='45'></a>";
        }else{
            $urlCo = $router->urlFor("Connexion");
            $urlInscri = $router->urlFor("CreerCompte");
            $urlHome = $router->urlFor("default");
            return "<a class='button' href='".$urlHome."'><img  src='../../html/home.png' width='45' height='45'></a>
            <a class='button' href='".$urlCo."'><img  src='../../html/login.png' width='45' height='45'></a>
            <a class='button' href='".$urlInscri."'><img  src='../../html/signup.png' width='45' height='45'></a>";
        }
    }
    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     */
    private function renderFooter(){
        return "La super app créée en Licence Pro &copy;2019";
    }

    private function renderBottomMenu(){
        $router = new \mf\router\Router();
        $urlFormTweet = $router->urlFor("VoirFormTweet");
        return '<nav id="menu" class="theme-backcolor1">
                    <div id="nav-menu">
                        <div class="button theme-backcolor2">
                            <a href="'.$urlFormTweet.'">New </a>
                        </div>
                    </div>
                </nav>';
    }
    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets.
     *
     */

    private function renderHome(){

        /*
         * Retourne le fragment HTML qui affiche tous les Tweets.
         *
         * L'attribut $this->data contient un tableau d'objets tweet.
         *
         */
         $res = "";
         foreach ($this->data  as $t){
            $res .= $this->renderTweet($t);
         }
         return $res;
    }

    /* Méthode renderUeserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné.
     *
     */

    private function renderUserTweets(){

        /*
         * Retourne le fragment HTML pour afficher
         * tous les Tweets d'un utilisateur donné.
         *
         * L'attribut $this->data contient un objet User.
         *
         */
        $router = new \mf\router\Router();
        $nbfollow = $this->data->followedBy()->count();
         $tweets = $this->data;
         $res = "<h2>".$this->data->fullname."</h2>
                <p>".$this->data->username."</p>
                <p>".$nbfollow." followers </p>"; 
         foreach ($this->data->tweets()->orderBy('created_at', 'DESC')->get() as $t){
            $res .=  $this->renderTweet($t);
         }
         return $res;
    }

    /* Méthode renderViewTweet
     *
     * Rréalise la vue de la fonctionnalité affichage d'un tweet
     *
     */

    private function renderViewTweet(){

        /*
         * Retourne le fragment HTML qui réalise l'affichage d'un tweet
         * en particulié
         *
         * L'attribut $this->data contient un objet Tweet
         *
         */
        $router = new \mf\router\Router();
        $tweet = $this->data;
        $res = $this->renderTweet($tweet,1);
        return $res;
    }
    /** Méthode renderTweet
     * 
     * Renvoie la structure d'une tweet 
     * 
     */
    private function renderTweet($tweet,$check = 0){
        $router = new \mf\router\Router();
        $auteur = $tweet->author()->first();
        $tabVal['id'] = $tweet->id;
        $tabValA['id'] = $auteur->id;
        $user = \tweeterapp\model\User::where('username',$_SESSION['user_login'])->first();
        $url = $router->urlFor("VoirTweet",$tabVal);
        $urlA = $router->urlFor("VoirUser",$tabValA);
        $res = "<div class='tweet'>
                    <a href='".$url."'>
                        <li class='tweet-text' >".$tweet->text."</li>
                    </a>
                    <div class='tweet-footer'>
                        <li> ".$tweet->created_at."
                            <div class='tweet-author'>  
                                <a href='".$urlA."''>".$auteur->username." </a>
                            </div>
                        </li>
                    </div><hr>
                    <div class='tweet-footer'>
                                        <span class='tweet-score tweet-control'>".$tweet->score."</span>";
                    if($check == 1 && isset($_SESSION['user_login'])){
                        $tab['idtweet'] = $tweet->id;
                        $tab['iduser'] = $auteur->id;
                        $urlLike = $router->urlFor("Like",$tab);
                        $urlDislike = $router->urlFor("Dislike",$tab);
                        $urlFollow = $router->urlFor("Follow",$tab);
                        if(\tweeterapp\model\Like::where('user_id', '=',  $user->id)->where('tweet_id', '=', $tweet->id)->first()==null){
                            $res .= '<a class="tweet-control" href="'.$urlLike.'">
                            <img alt="Like" src="../../html/like.png"></a>';
                        }else{
                            $res .= '<a class="tweet-control" href="'.$urlLike.'">
                            <img alt="Like" src="../../html/likeG.png"></a>';
                        }

                        $res .='<a class="tweet-control" href="'.$urlDislike.'">
                            <img alt="dislike" src="../../html/dislike.png"></a>';
                        if($user->id != $auteur->id){
                            if($auteur->alreadyFollow($user->id,$auteur ->id) == false){
                                $res .= '<a class="tweet-control" href="'.$urlFollow.'"><img alt="Follow" src="../../html/follow.png"></a>';
                            }else{
                                $res .= '<a class="tweet-control" href="'.$urlFollow.'"><img alt="Followee" src="../../html/followees.png"></a>';
                            } 
                        }
                    }
                 $res .= "</div></div>";
        return $res;
    }

    /* Méthode renderPostTweet
     *
     * Realise la vue de régider un Tweet
     *
     */
    protected function renderPostTweet(){

        /* Méthode renderPostTweet
         *
         * Retourne la framgment HTML qui dessine un formulaire pour la rédaction
         * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
         *
         */
         $router = new \mf\router\Router();
         $urlEnvoie = $router->urlFor("EnvoyerTweet");
         $res = '<form action="'.$urlEnvoie.'" method="post">
                    <textarea id="tweet-form" name="text" placeholder="Entrer votre message.." ,="" maxlength="140"></textarea>
                    <div><input id="send_button" type="submit" name="send" value="Send"></div>
                  </form>';
         return $res;
    }
    /**
     * 
     */
    protected function renderLogin(){
        
        $router = new \mf\router\Router();        
        $url = $router->urlFor("CheckCo");
        $res = '<section>
                    <article class="theme-backcolor2"><form action="'.$url.'" method="post">
                    <label for="user_login">Username:</label>
                    <input type="text" name="user_login">
                    <br>
                    <label for="user_pass">Password:</label>
                    <input type="password" name="user_pass" required="">
                    <br>
                    <input type="submit" value="Connexion">
                </form></article>
                </section>';
        return $res;
    }
    /**
     * 
     */
    protected function renderFollowing(){
        $router = new \mf\router\Router();
        $follows = $this->data->follows()->get(); 
        $nbfollow = $this->data->followedBy()->count();
        $urlfollower = $router->urlFor("Followee");
        $res = '<section>
                    <article class="theme-backcolor2">
                    <h3>Tweets de vos followers :</h3>';
                    foreach ($follows as $user) {
                        $tweets = $user->tweets()->get();
                        foreach ($tweets as $t) {
                            $res .= $this->renderTweet($t);
                        }
                    }
        $res .='    </article>
                </section>';
        return $res;
    }
    /**
     * 
     */
    protected function renderSignup(){
        $router = new \mf\router\Router();        
        $url = $router->urlFor("CheckCreerCompte");
        $res = '<form method="post" class="forms" action="'.$url.'">
                    <input class="forms-text" type="text" name="fullname" placeholder="Nom complet"><br>
                    <input class="forms-text" type="text" name="user_name" placeholder="Nom utilisateur"><br>
                    <input class="forms-text" type="password" name="user_pass" placeholder="Mot de passe"><br>
                    <input class="forms-text" type="password" name="password_verify" placeholder="Retaper le mot de passe"><br>
                    <button class="forms-button" type="submit">créer</button>
                </form>';
        return $res;
    }
    /**
     * 
     */
    public function renderFollower(){
        $router = new \mf\router\Router();
        $follows = $this->data->follows()->get(); 
        $nbfollow = $this->data->followedBy()->count();
        
        $urlfollower = $router->urlFor("Followee");
        $res = '<section>
                    <article class="theme-backcolor2">
                    <h3>'.$this->data->username.' vous avez : <a href="'.$urlfollower.'"> '.$nbfollow.' Followers </a> </h3>
                    <h3>Vos follows :</h3>';
                foreach ($follows as $f) {
                    $tab['id'] = $f->id;
                    $urlUser = $router->urlFor("VoirUser",$tab);
                    $res .= '<p><a href="'.$urlUser.'">'.$f->username.'</a></p>';
                }   
        $res .= '</article>
                </section>';
        return $res;
    }
    /**
     * 
     */
    protected function renderFollowee(){
        $router = new \mf\router\Router(); 
        $follower = $this->data->followedBy()->get();
        $res = '<section>
                    <article class="theme-backcolor2">
                        <h3>Vos followers :</h3> ';
                        foreach ($follower as $user) {
                            $tab['iduser'] = $user->id;
                            $urlFollow = $router->urlFor("Follow",$tab);
                            $res .= '<div class="tweet">';
                            $res .= $user->fullname;                                
                                if($this->data->alreadyFollow($this->data->id,$user->id) == false){
                                    $res .= '<a class="tweet-control" href="'.$urlFollow.'"><img alt="Follow" src="../../html/follow.png"></a>';
                                }else{
                                    $res .= '<a class="tweet-control" href="'.$urlFollow.'"><img alt="Followee" src="../../html/followees.png"></a>';
                                }
                                    $res .= 'Nombre de followers : '.$user->followers.'
                            </div>';
                        }
        $res .='        </div> 
                    </article>
                </section>';
        return $res;
    }

    /* Méthode renderBody
     *
     * Retourne la framgment HTML de la balise <body> elle est appelée
     * par la méthode héritée render.
     *
     */
    protected function renderBody($selector){
        /*
         * voire la classe AbstractView
         *
        */     
        
        var_dump($_SESSION['user_login']);   
        $html = $this->renderHeader();
        $html .= '<section><article class="theme-backcolor2">';
        if($selector == 1){
            $html .= $this->renderViewTweet();
        }else if($selector == 2){
            $html .= $this->renderUserTweets();
        }else if($selector == 3){
            $html .= $this->renderPostTweet();
        }else if($selector == "Connexion"){
            $html .= $this->renderLogin();
        }else if($selector == "Following"){
            $html .= $this->renderFollowing();
        }else if($selector == "Signup"){
            $html .= $this->renderSignup();
        }else if($selector == "Followee"){
            $html .= $this->renderFollowee();
        }else if($selector == "Follower"){
            $html .= $this->renderFollower();
        }else{
            $html .= $this->renderHome();
        }
        $html .= '</section></article>';
        $html .=  "<footer class='theme-backcolor1'>";
        if(isset($_SESSION['user_login'])){
            $html .= $this->renderBottomMenu();
        }
        $html .= $this->renderFooter()."</footer>";
        return $html;
    }












}
