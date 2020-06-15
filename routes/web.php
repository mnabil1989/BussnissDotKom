<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});


Route::group(['prefix' => 'admin'], function () {
    Route::get('categories/sub'   , 'VoyagerCategoriesController@sub_categories');
    Route::get('categories/sub/sub'   , 'VoyagerCategoriesController@sub_sub_categories');
    Voyager::routes();


});
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});
Route::get('cat/{id}', function($id) {

        // return

        // \App\Models\Category::where('categories.id', '=', $id)
        //     ->select('categories.parent_id', 'cate2.id as parent_id')
        //         ->join('categories as cate1', 'categories.parent_id', '=', 'cate1.id')
        //             ->join('categories as cate2', 'cate1.parent_id', '=', 'cate2.id')
        //                 ->first();
                        
        $cat = \App\Models\Category::find($id);
        return [
            'parent_id' => (string) $cat->parent->id,
            'category' => (string) $cat->parent->parent->id,
            ];
	
});