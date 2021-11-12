<?php
namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'user';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */
       public function tweets() {
         return $this->hasMany('tweeterapp\model\Tweet', 'author');
       }
       // Retourne les tweets liké par l'utilisateur
       public function liked(){
         return $this->belongsToMany('tweeterapp\model\Tweet','tweeterapp\model\Like','user_id','tweet_id');
       }
       // Retourne les utilisateur qui suivent l'auteur
       public function followedBy(){
         return $this->belongsToMany('tweeterapp\model\User','tweeterapp\model\Follow','followee','follower');
       }

       //Retourne les utilisateurs que l'auteur suit
       public function follows(){
          return $this->belongsToMany('tweeterapp\model\User','tweeterapp\model\Follow','follower','followee');
       }
}
