<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
	use Notifiable,Reviewable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','type','mobile','country_id','social_auth_type','social_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user){
            if(request()->password)
            $user->password = bcrypt($user->password);
            $user->verify_code = rand(1, 9).rand(11, 99).rand(33, 77).rand(6, 9);
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function branchs()
    {
        return $this->hasMany(Branch::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function country()
    {
        return $this->BelongsTo(Country::class);
    }
    public function city()
    {
        return $this->BelongsTo(City::class);
    }
    public function state()
    {
        return $this->BelongsTo(State::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    
    
    /**
     * Get all of the video's comments.
    */
    public function FavoritProducts()
    {
        return $this->morphedByMany(Product::class, 'favorite');
    }
    
    
     public function isNot($user){
        return $this->id !== $user->id;
    }

    public function isFollowing($user){
        return (bool) $this->following->where('id',$user->id)->count();
    }

    public function canFollow($user){
        if(!$this->isNot($user)){
            return false;
        }
        return !$this->isFollowing($user);
    }

    public function canUnfollow($user){
        return $this->isFollowing($user);
    }

    public function following(){
        return $this->belongsToMany('App\Models\User','follows','user_id','follower_id');
    }
    public function followers(){
        return $this->belongsToMany('App\Models\User','follows','follower_id','user_id');
    }


}
