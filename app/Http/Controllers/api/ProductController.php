<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\attributesValuesResource;
use App\Http\Resources\TagsResource;
use App\Http\Resources\MainCategoryResource;
use App\Http\Resources\AuthCategoriesResource;
use App\Http\Resources\attributesResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Tag;
use App\Models\ProductPrice;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Scoping\Scopes\CategoryScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->only('store');
    }

    public function offers()
    {
        
        return response()->json([
    'categories' => MainCategoryResource::collection(Category::with('children')->whereHas('children.products', function ($query)  { 
    $query->where('sale_price', '!=', null); 
})->get()
),
    'top-suppliers' => [['id' => 1 , 'name'=> 'Dell','logo' => 'https://i.imgur.com/6Li4KwM.png' , 'stars' => 4],['id' => 2 ,'name' => 'Nike','logo'=> 'https://i.imgur.com/FAlNOQ3.jpg','stars' => 3]],
    'slider' => [
        'https://i.imgur.com/lb2nq43.jpg',
        'https://i.imgur.com/lb2nq43.jpg',
        ]
]);
    
    }
    public function used()
    {
        
        return response()->json([
    'categories' => MainCategoryResource::collection(Category::where('id',1)->get()
),
    'top-suppliers' => [['id' => 1 , 'name'=> 'Dell','logo' => 'https://i.imgur.com/6Li4KwM.png' , 'stars' => 4],['id' => 2 ,'name' => 'Nike','logo'=> 'https://i.imgur.com/FAlNOQ3.jpg','stars' => 3]],
    'slider' => [
        'https://i.imgur.com/lb2nq43.jpg',
        'https://i.imgur.com/lb2nq43.jpg',
        ]
]);
    
    }
    public function category_info($id)
    {
        
        return response()->json([

    'top-suppliers' => [['id' => 1 , 'name'=> 'Dell','logo' => 'https://i.imgur.com/6Li4KwM.png' , 'stars' => 4],['id' => 2 ,'name' => 'Nike','logo'=> 'https://i.imgur.com/FAlNOQ3.jpg','stars' => 3]],
    'slider' => [
        'https://i.imgur.com/lb2nq43.jpg',
        'https://i.imgur.com/lb2nq43.jpg',
        ]
]);
    
    }

    public function index()
    {
        $products = Product::withScopes($this->scopes())->paginate(10);

        return ProductIndexResource::collection(
            $products
        );
    }
    public function store_products()
    {
        $products = Product::where('user_id',Request()->user()->id)->paginate(10);

        return ProductIndexResource::collection(
            $products
        );
    }


   public function suggested_supplier()
    {
        $products = Product::limit(10)->get();

        return ProductIndexResource::collection(
            $products
        );
    }
   public function suggested_category()
    {
        $products = Product::limit(10)->get();

        return ProductIndexResource::collection(
            $products
        );
    }


    public function show($id)
    {

        return new ProductResource(
            Product::find($id)
        );
    }

    public function factory_tags($id)
    {
        return TagsResource::collection(Tag::with(["products" => function($q) use($id){
            $q->where('user_id', $id);
        }])
        ->get());

    }
    public function factory_tag($id,$tag_id)
    {
        return ProductIndexResource::collection(Product::where('tag_id',$tag_id)->paginate(10));

    }
    public function factory_used($id)
    {
        return ProductIndexResource::collection(Product::get());

    }
    
    public function factory_categories($id)
    {
        return AuthCategoriesResource::collection(Category::whereIn('id',[14,15])->get());
    }
    
    public function factory_categories_products($factory_id,$category_id)
    {
        return ProductIndexResource::collection(Product::where('category_id',$category_id)->paginate(10));
    }
    
    public function available_languages(){
         $lang = \Config::get('voyager.multilingual')['locales'];
        return $lang = array_values(array_diff($lang, array(\Config::get('voyager.multilingual')['default'])));

    }

    public function store(Request $request)
    {
        // return request()->all();
         $lang = \Config::get('voyager.multilingual')['locales'];
         $lang = array_values(array_diff($lang, array(\Config::get('voyager.multilingual')['default'])));

        $langs = [];
        

         $product = new Product; 
         
         $product->name = $request->name;
         $product->user_id = Request()->user()->id;
         $product->description = $request->description;
         $product->price = $request->price;
         $product->sale_price = $request->sale_price;
         $product->tag_id = $request->tag_id;
         $product->category_id = $request->category_id;
         $product->used = $request->used;
         
          		$images = array();
		/**
		 * check if images come as array then loop the images to save them
		 */

			if (is_array($request->file('images'))) {

				foreach ($request->file('images') as $image) {
                    $file_name     = 'product_image'.   rand(1, 15). rand(155, 200) . rand(25, 55). '.png';
                    $image->storeAs('public/products',$file_name);
                    $img_url = 'products/'. $file_name;
					array_push($images, $img_url);
				}

			}


        $product->images = json_encode($images);
   
   

			if (($request->file('image'))) {

                    $file_name     = 'product_image'.   rand(1, 15). rand(155, 200) . rand(25, 55). '.png';
                    $image->storeAs('public/products',$file_name);
                    $product->image = 'products/'. $file_name;
			}



        $product->save();
    
     $product->categories()->attach(Request()->category_ids);
     $price_list = [];
     $price_list = $request->prices;
   
     foreach($price_list as $price){
         $price_option = new ProductPrice;
         $price_option->product_id = $product->id;
         $price_option->price = $price['price'];
         $price_option->quantity_from = $price['quantity_from'];
         $price_option->quantity_to = $price['quantity_to'];
         $price_option->save();
     }
     
          $attributes = [];
     $attributes = $request->options;

     foreach($attributes as $attribute){
         $attribute_id =   $attribute['attribute_id'];
         $values_id =   $attribute['values_id'];
             $attribute = new \App\Models\ProductAttribute;
             $attribute->product_id = $product->id;
             $attribute->attribute_id = $attribute_id;
             $attribute->value_id = $values_id;
             $attribute->save();
        
     }
     
     
            foreach($lang as $lang_option)  {
          $name_lang_option = "name_" . $lang_option;
          $description_lang_option = "description_" . $lang_option;
           if(request()->$name_lang_option){
              DB::table('translations')->updateOrInsert(
    ['table_name' => 'products', 'column_name' => "name" , "foreign_key"=> $product->id , "locale" => $lang_option ],[ "value" => request()->$name_lang_option]
);
   
           }
           if( request()->$description_lang_option){
              DB::table('translations')->updateOrInsert(
    ['table_name' => 'products', 'column_name' => "description" , "foreign_key"=> $product->id , "locale" => $lang_option ],
    ["value" => request()->$description_lang_option]
    
);
           }
       }
return response()->json([
    "status" => "success"
    ]);
    }

    protected function attributes()
    {
                return  attributesResource::collection(
            Attribute::get()
        );
    }
    protected function attributes_by_category($id)
    {
                return  attributesResource::collection(
            Attribute::whereHas('categories', function ($query) use($id) {
    $query->where('id', $id);
})->get()
        );
    }
    protected function attribute_values($id)
    {
                return  attributesValuesResource::collection(
            AttributeValue::where('attribute_id',$id)->get()
        );
    }
    
    
    
    
        public function destroy($id){
                 $product =  Product::find($id) ;

                 if(!$product)
                        return response()->json(['status' => 'failed', 'message' => 'not fond']);
            $product->delete();

        return response()->json([
            'status' => "success",
             "message" => "product Deleted successfully"

            ]) ;
        
    }

}
