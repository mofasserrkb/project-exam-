<?php

use Illuminate\Support\Facades\Route;
use App\Models\Variant;
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
    return redirect()->to('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('product-variant', 'VariantController');
    Route::resource('product', 'ProductController')->except('update');
    Route::post('/product/{product}','ProductController@update');
    Route::resource('blog', 'BlogController');
    Route::resource('blog-category', 'BlogCategoryController');
});
Route::get('/addproduct',function(){
    $variants = Variant::all();
    return view('products.create', compact('variants'));
    // return view('products.variant.create');
   // return view('products.add',$variants);
});
