<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\TagsResource;
use App\Http\Resources\ParentCategoryResource;
use App\Http\Resources\MainCategoryResource;
use App\Http\Resources\ServicesIndexResource;
use App\Http\Resources\CategoryProductsResource;
use App\Http\Resources\CompaniesResource;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\StoreResource;
use App\Http\Resources\AuthCategoriesResource;


class HomeController extends Controller
{
   public function services_categories(){
               		$categories =  Category::whereNull('parent_id')
               		    ->whereHas('users', function ($query)  { 
                            $query->where('type', 'service'); 
                        })
               		->select('id','name')->get();
               		return AuthCategoriesResource::collection($categories);
               		return response()->json([
               		    'data' => $categories
               		    ]);
               		
   }
   
   public function services_categories_show($id){
               return  ServicesIndexResource::collection(Service::where('category_id',$id)->with('category')->paginate(10));
       
   }

       public function home(){
        return response()->json(['data'=>TagsResource::collection(\App\Models\Tag::with('products')->get())
        //   ->concat([
        //                 'data' => [
        //                     'id' => 9999,
        //                     'name' => 'Hot Offers',
        //                     'image' => 'http://businessdotkom.com/storage/categories/January2020/1o6nDi1kjVuwje5FiFXv.png',
        //                     'products' => ProductIndexResource::collection(Product::whereNotNull('sale_price')->get()),
        //                 ]
        //             ])
          ->concat([
                        'data' => [
                             'slider' => [
                'https://i.imgur.com/lb2nq43.jpg',
                'https://i.imgur.com/lb2nq43.jpg',
                ]
                        ]
                    ])
                    
                    ])
                    ;
    }
    
    public function hot_offers_suppliers(){
        
        return  StoreResource::collection(
    
         User::with(["products" => function($q){
               $q->where('sale_price', '!=', null);
            }])
        
            ->whereHas('products', function ($query)  { 
            $query->where('sale_price', '!=', null); 
        })->paginate(10)
    );
    }
    public function hot_offers_companies(){
        
        return  CompaniesResource::collection(
    
         User::with(["products" => function($q){
               $q->where('sale_price', '!=', null);
            }])
        
            ->whereHas('products', function ($query)  { 
            $query->where('sale_price', '!=', null); 
        })->paginate(10)
    );
    }
    
    public function hot_offers_products(){
        
        return  ProductIndexResource::collection(Product::whereNotNull('sale_price')->paginate(10));
    }
    
    public function latest_slider(){
        
        return response()->json([
                      'data' => [
                             'slider' => [
                'https://i.imgur.com/lb2nq43.jpg',
                'https://i.imgur.com/lb2nq43.jpg',
                ]
    ]
            ]);
    }
    

    public function suggested_suppliers(){
          
        return  StoreResource::collection(User::with("products")->paginate(10));
    }
    public function suggested_products(){
          
            return ProductIndexResource::collection(Product::latest()->paginate(10));
    }
    public function suggested_services(){
          
        return  ServicesIndexResource::collection(Service::paginate(10));
    }
    
    
    
      public function suppliers(){
        
            return  StoreResource::collection(User::with("products")->paginate(10));

    }
    public function services(){
          
        return  ServicesIndexResource::collection(Service::paginate(10));
    }
    

}
