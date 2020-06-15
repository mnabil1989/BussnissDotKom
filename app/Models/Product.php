<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductVariation;
use App\Models\Traits\CanBeScoped;
use App\Models\Traits\HasPrice;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
       use Favoritable,Reviewable, CanBeScoped, Translatable;

    protected $translatable = ['name', 'description'];
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'price'
    ];
    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function owner()
    {
        return $this->belongsTo(User::class,'user_id');
    }

	public function category() {
		return $this->belongsTo('App\Models\Category','category_id');
	}
    public static function boot()
    {
        parent::boot();

        static::creating(function ($product){
            $product->user_id = Request()->user()->id;
        });
    }



    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
    
    public function prices()
    {
        return $this->hasMany(ProductPrice::class)->select(['id', 'price', 'quantity']);
    }
    
    public function options()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    
    public function like($user = null)
    {
        $user = $user ?:Request()->user();
        return $this->likes()->attach($user);
    }
    
        
    public function likes()
    {
        return $this->morphTo(User::class, 'favouriteable');
    }
    
	public static function categoryIdRelationship($id) {

		// return
		// 	self::where('products.id', '=', $id)
		// 		->select('products.category_id', 'sub_categories.id as sub_category_id', 'main_categories.id as main_category_id')
		// 			->join('categories', 'products.category_id', '=', 'categories.id')
		// 				->join('sub_categories', 'categories.sub_category_id', '=', 'sub_categories.id')
		// 					->join('main_categories', 'sub_categories.main_category_id', '=', 'main_categories.id')
		// 						->first();
	}

}
