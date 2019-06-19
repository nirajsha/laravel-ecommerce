<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Banner;

class IndexController extends Controller
{

    public function index(){
    	//Shows products in ascending order by default
    	// $productsAll = Product::get(); 

    	//Shows products in descending order
    	$productsAll = Product::orderBy('id','DESC')->get(); 

    	//Shows products in random order
    	$productsAll = Product::inRandomOrder()->where('status',1)->where('feature_item',1)->paginate(3);//show product with status 1 only

    	//Get all categories and subcategories at sidebar of home page

             $categories = Category::with('categories')->where(['parent_id'=>0])->get();  
		     $banners = Banner::where('status','1')->get();
             //dd($banners);

//Meta Tags(for Seo)
        $meta_title = "E-shop sample website";
        $meta_description = "Online Shopping Site For Men, Women and Kids Clothing";
        $meta_keywords = "eshop website, online shopping,men clothing";
    	return view('index')->with(compact('productsAll', 'categories_menu','categories','banners','meta_title','meta_description','meta_keywords'));


    }
}
