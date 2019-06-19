<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;//class for sending email
use Auth;
use Session;
use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Coupon;
use App\User;
use App\Country;
use App\DeliveryAddress;
use App\Order;
use App\OrdersProduct;
use DB;

class ProductsController extends Controller
{
    public function addProduct(Request $request){
       //dd($request);
        if($request->isMethod('post')){
            $data= $request->all();
            //echo "pre"; print_r($data); die;
            if(empty($data['category_id'])){
                 return redirect()->back()->with('flash_message_error','Under Category is missing');
            }
            $product = new Product;
           
              // if(!empty($data['category_id'])){
            $product->category_id = $data['category_id'];
            // }else{
            //     $product->category_id = '';
            // }
           
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            if(!empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
             if(!empty($data['care'])){
                $product->care = $data['care'];
             }else{
                $product->care = '';
             }
           
            $product->price = $data['price'];
            //upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){

                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                    //store image name in products table
                    $product->image = $filename;

                }
            }

            //Upload Video 
            if($request->hasFile('video')){
            	$video_tmp = Input::file('video');
                $video_name = $video_tmp->getClientOriginalName();
            	$video_path = 'videos/';   
            	$video_tmp->move($video_path,$video_name);
            	$product->video =$video_name;//saving video to product table
            }
            if(empty($data['status'])){
            	$status = 0;
            }
            else{
            	$status = 1;
            }
            if(empty($data['feature_item'])){
              $feature_item = 0;
            }
            else{
              $feature_item = 1;
            }
            $product->feature_item = $feature_item;
            $product->status = $status;

            $product->save();
            return redirect('/admin/view-products')->with('flash_message_success','product has been added successfully'); 
        }
        // Categories dropdown start
      $categories = Category::where(['parent_id'=>0])->get();
      $categories_dropdown = "<option  selected disabled>Select</option>";
      foreach($categories as $cat){
        $categories_dropdown .= "<option value='".$cat->id."'>".$cat->name."</option>";
        $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
        foreach( $sub_categories as $sub_cat){
          $categories_dropdown .= "<option value='".$sub_cat->id."'>&nbsp--&nbsp;".$sub_cat->name.
          "</option>";
        }
      } 
        // categories dropdown ends
      return view('admin.products.add_product')->with(compact('categories_dropdown'));
    }
     public function editProduct(Request $request,$id=null){
        if($request->isMethod('post')){
            $data= $request->all();
            // dd($data);
            // echo "<pre>"; print_r($data); die;
            //upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                }
            }else if(!empty($data['current_image'])){
                $filename = $data['current_image'];
            }else{
            	$filename = '';
            }
              //Upload Video
           
            if($request->hasFile('video')){
            	$video_tmp = Input::file('video');
                $video_name = $video_tmp->getClientOriginalName();
            	$video_path = 'videos/';  
            	$video_tmp->move($video_path,$video_name);
            	$videoName = $video_name;	
            }else if(!empty($data['current_video'])){
                $videoName = $data['current_video'];
            }else{
            	$videoName = '';
            }

             if(empty($data['description'])){
                $data['description'] = '';
            }
               if(empty($data['care'])){
                $data['care'] = '';
            }
              if(empty($data['status'])){
            	$status = 0;
            }
            else{
            	$status = 1;
            }
             if(empty($data['feature_item'])){
              $feature_item = 0;
            }
            else{
              $feature_item = 1;
            }

            Product::where(['id'=>$id])->update(['product_name'=>$data['product_name'],'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'description'=>$data['description'],'care'=>$data['care'],'price'=>$data['price'],'image'=>$filename, 'status'=>$status, 'feature_item'=>$feature_item,'video'=>$videoName
        ]);
            return redirect('/admin/view-products')->with('flash_message_success','product has been updated successfully');
        }

        $productDetails = Product::where(['id'=>$id])->first();
        // dd($productDetails);
        // Categories dropdown start
        $categories = Category::where(['parent_id'=>0])->get(); //shows selected categories in Category Level field that you want to edit
        $categories_dropdown = "<option  selected disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$productDetails->category_id){
                $selected = "selected";
            }else{
                $selected="";
            }
            $categories_dropdown .= "<option value='".$cat->id."'".$selected.">".$cat->name."</option>";
            //for subcategories
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();//shows selected sub categories in Category Level field that you want to edit
            foreach( $sub_categories as $sub_cat){
                 if($sub_cat->id==$productDetails->category_id){
                    $selected="selected";
                }else{
                    $selected="";
                }
                $categories_dropdown .= "<option value='".$sub_cat->id."' ".$selected.">&nbsp--&nbsp;".$sub_cat->name.
                "</option>";
            }
        }
        // categories dropdown ends
        return view('admin.products.edit_product')->with(compact('productDetails','categories_dropdown'));
    }
   
    public function viewProducts(){
        $products = Product::orderby('id','DESC')->get();//to show descending order of products in views
        //dd($products);
        $products = json_decode(json_encode($products));

        foreach($products as  $key=> $val){
            $category_name = Category::where(['id'=>$val->category_id])->first();
           // dd($category_name);
            $products[$key]->category_name = $category_name['name'];
           // dd($products);
        }
        //echo "<pre>"; print_r($products); die;
        return view('admin.products.view_products')->with(compact('products'));
    }
      public function deleteProduct($id=null){
      
         Product::where(['id'=>$id])->delete();
         return redirect()->back()->with('flash_message_success','Product has been deleted successfully');
    }
    public function deleteProductImage($id=null){

      //Get product image name
      $productImage = Product::where(['id'=>$id])->first();
      //echo $productImage->image; die;

      //GEt products image paths
      $large_image_path ='images/backend_images/products/large/';
      $medium_image_path ='images/backend_images/products/medium/';
      $small_image_path ='images/backend_images/products/small/';
    // echo $large_image_path.$productImage->image;die;

      //Delete large image if not exists in folder
      if(file_exists($large_image_path.$productImage->image)){
      // echo "test"; die;
        unlink($large_image_path.$productImage->image);//unlink deletes image from folder
      }
        //Delete medium image if not exists in folder
      if(file_exists($medium_image_path.$productImage->image)){
        unlink($medium_image_path.$productImage->image);//unlink deletes image from folder
      }
        //Delete small image if not exists in folder
      if(file_exists($small_image_path.$productImage->image)){
        unlink($small_image_path.$productImage->image);//unlink deletes image from folder
      }

        //Delete image from products folder
         Product::where(['id'=>$id])->update(['image'=>'']);
         return redirect()->back()->with('flash_message_error','Product image has been deleted successfully');
    }

     public function deleteProductVideo($id){
     	//Get Video Name
     	$productVideo = Product::select('video')->where('id',$id)->first();
     	//Get Video Path
     	$video_path ='videos/';
     	//Delete video if exists in video folder
     	if(file_exists($video_path.$productVideo->video)){
     		unlink($video_path.$productVideo->video);
     	}
     	//Delete Video from Products Table
     	Product::where('id',$id)->update(['video'=>'']);
     	return redirect()->back()->with('flash_message_success','Product Video has been deleted successfully');

     }
     //Deleting alternate images that we have added on view product blade file in add images button
        public function deleteAltImage($id=null){

      //Get product image name
      $productImage = ProductsImage::where(['id'=>$id])->first();//we are deleting image id so 'id'=>$id
      //echo $productImage->image; die;

      //GEt products image paths
      $large_image_path ='images/backend_images/products/large/';
      $medium_image_path ='images/backend_images/products/medium/';
      $small_image_path ='images/backend_images/products/small/';
    // echo $large_image_path.$productImage->image;die;

      //Delete large image if not exists in folder
      if(file_exists($large_image_path.$productImage->image)){
      // echo "test"; die;
        unlink($large_image_path.$productImage->image);//unlink deletes image from folder
      }
        //Delete medium image if not exists in folder
      if(file_exists($medium_image_path.$productImage->image)){
        unlink($medium_image_path.$productImage->image);//unlink deletes image from folder
      }
        //Delete small image if not exists in folder
      if(file_exists($small_image_path.$productImage->image)){
        unlink($small_image_path.$productImage->image);//unlink deletes image from folder
      }

        //Delete image from products folder
         ProductsImage::where(['id'=>$id])->delete();
         return redirect()->back()->with('flash_message_error','Product Alternate Image(s) has been deleted successfully');
    }
    //addAttributes button is in view products blade file
    public function addAttributes(Request $request, $id=null){
      // dd('here');
      $productDetails =Product::with('attributes')->where(['id'=>$id])->first();//attaching product with attributes ie. relationship(see attributes function in Product.php modal)
      // $productDetails = json_decode(json_encode($productDetails));
      // dd($productDetails);
       if($request->isMethod('post')){
            $data = $request->all();
        // dd($data);
             foreach($data['sku'] as $key => $val){
                if(!empty($val)){

                    //prevent duplicate SKU Check
                    $attrCountSKU = ProductsAttribute::where('sku', $val)->count();//check sku exists or not
                    if($attrCountSKU > 0){
                        return redirect('/admin/add-attribute/'.$id)->with('flash_message_error', 'SKU Already Exists');
                    }
                    // prevent duplicate Size Check
                    $attrCountSizes = ProductsAttribute::where(['product_id' => $id, 'size' => $data['size'][$key]])->count();//check product id and size exixt or not
                    if($attrCountSizes > 0){
                        return redirect('/admin/add-attribute/'.$id)->with('flash_message_error', ' "'.$data['size'][$key].' Size Already Exists');
                    }
                   $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                     //dd($attribute->size);
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }
              return redirect('/admin/add-attribute/'.$id)->with('flash_message_success', 'Attribute added successfully');
      }
      return view('admin.products.add_attributes')->with(compact('productDetails'));
    }
     public function editAttributes(Request $request, $id = null){

    	if($request->isMethod('post')){
            $data = $request->all();
           //echo "<pre>"; print_r($data); die;
           
             foreach($data['idAttr'] as $key => $attr){ //key so that we can get elements like 0,1,2's value (value may be idattribute ,price and stock)
                ProductsAttribute::where(['id' => $data['idAttr'][$key]])->update(['price' => $data['price'][$key], 'stock' => $data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success', 'Products Attributes Updated Successfully');
    	}
    }
    
   public function addImages(Request $request, $id=null){
        $productDetails = Product::where(['id' => $id])->first();//for showing product name, product code and product alt images in add_images blade file.
        $categoryDetails = Category::where(['id'=>$productDetails->category_id])->first();
        $category_name = $categoryDetails->name;
        if($request->isMethod('post')){
            $data = $request->all();
            if ($request->hasFile('image')) {
                $files = $request->file('image');
                foreach($files as $file){
                    // Upload Images after Resize
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $fileName = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large'.'/'.$fileName;
                    $medium_image_path = 'images/backend_images/products/medium'.'/'.$fileName;  
                    $small_image_path = 'images/backend_images/products/small'.'/'.$fileName;  
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600, 600)->save($medium_image_path);
                    Image::make($file)->resize(300, 300)->save($small_image_path);
                    $image->image = $fileName;  
                    $image->product_id = $data['product_id'];
                    $image->save();
                }   
            }
            return redirect('admin/add-images/'.$id)->with('flash_message_success', 'Product Images has been added successfully');
        }
        $productsImages = ProductsImage::where(['product_id' => $id])->get();//comparing and getting product_id(product_id of ProductsImage is id of product)
       // $productsImages = json_decode(json_encode($productsImages));
       // dd($productsImages);
      return view('admin.products.add_images')->with(compact('productDetails','productsImages'));
    }
    public function deleteAttribute($id = null){
        ProductsAttribute::where(['id' => $id])->delete();
        return redirect()->back()->with('flash_message_error', 'Attribute Deleted successfully');
    }
    //Working for frontend
    public function products($url = null){
        // echo $url; die;
      //show 404 page error if Category URL doesnot exist
      $countCategory = Category::where(['url'=>$url,'status'=>1])->count();
      // echo $countCategory; die;
      if($countCategory==0){
        abort(404);
      }

        $categories = Category::with('categories')->where(['parent_id'=>0])->get();    
        $categoryDetails = Category::where(['url'=> $url])->first();
        // echo $categoryDetails->id; die;        
         // $productsAll = Product::where(['category_id'=>$categoryDetails->id])->get();
        if($categoryDetails->parent_id==0){
            $subCategories =Category::where(['parent_id'=>$categoryDetails->id])->get(); //we are fetching categories of main categories in drop down menu of header    
            foreach ($subCategories as $subcat) {
                $cat_ids[] = $subcat->id;
            }
            // dd($cat_ids);
             $productsAll = Product::whereIn('category_id',$cat_ids)->where('status',1)->orderBy('id','Desc')->paginate(6);
             $productsAll =json_decode(json_encode($productsAll));
             // dd($productsAll);
        }
        else{
            //if url is sub category url
         $productsAll = Product::where(['category_id'=>$categoryDetails->id])->where('status',1)->orderBy('id','Desc')->paginate(6);
     }
         // dd($productsAll);
     $meta_title = $categoryDetails->meta_title;
     $meta_description = $categoryDetails->meta_description;
     $meta_keywords = $categoryDetails->meta_keywords;

        return view('products.listing')->with(compact('categories','categoryDetails','productsAll','meta_title','meta_description','meta_keywords'));
    }

       public function searchProducts(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //dd($data); 
             $categories = Category::with('categories')->where(['parent_id'=>0])->get(); 
             $search_product = $data['product'];
             $productsAll = Product::where('product_name','like','%'.$search_product.'%')->orwhere('product_code',$search_product)->where('status',1)->get();   
             return view('products.listing')->with(compact('categories','productsAll','search_product'));
  }
}


     public function product($id = null){
     	//Show 404 page if product is disabled
     	$productCount = Product::where(['id'=>$id, 'status'=>1])->count();
     	if($productCount == 0)
     	{
     		abort(404);
     	}

     $productDetails = Product::with('attributes')->where('id',$id)->first();//get category with category attributes(we have used relation here for detail.blade.php)
     $productDetails = json_decode(json_encode($productDetails));
     // dd($productDetails);

    //Get all categories and subcategories
       $categories = Category::with('categories')->where(['parent_id'=>0])->get(); 

       //Get Product Alternate images
       $productAltImages = ProductsImage::where('product_id',$id)->get();
       // $productAltImages = json_decode(json_encode($productAltImages));
       //dd($productAltImages);

       //For getting recommended items in front end detail.blade.php file
             $relatedProducts = Product::where('id', '!=', $id)->where(['category_id' => $productDetails->category_id])->get();
            // $relatedProducts = json_decode(json_encode($relatedProducts));
             // dd($relatedProducts);
        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');
       // dd($total_stock);
        $meta_title = $productDetails->product_name;
         $meta_description = $productDetails->description;
        $meta_keywords = $productDetails->product_name;


      return view('products.detail')->with(compact('productDetails','categories','productAltImages','total_stock','relatedProducts', 'meta_title','meta_description','meta_keywords'));

     }

   //converting INR  to different currency rate in detail blade file.
      public function getProductPrice(Request $request){
        //dd($request);
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $proArr = explode("-", $data['idSize']);
        ///echo $proArr[0]; echo $proArr[1]; die;
        $proAttr = ProductsAttribute::where(['product_id' => $proArr[0], 'size' => $proArr[1]])->first();
        echo $proAttr->price;
        //getting converted inr to any money ie. usd,usr in detailblade.php when we click on size.
        $getCurrencyRates = Product::getCurrencyRates($proAttr->price);
        echo $proAttr->price. "-".$getCurrencyRates['USD_Rate']. "-".$getCurrencyRates['GBP_Rate']. "-".$getCurrencyRates['EUR_Rate'];
        echo "#";
        echo $proAttr->stock;
    }
    public function addtocart(Request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $data = $request->all();
         if(empty($data['size'])){
                 return redirect()->back()->with('flash_message_error','Please select the missing size');
            }
         //dd($data);
        //$obj = json_decode (json_encode ($data), FALSE);
       // dd($data);         
         //Check Product stock is available or not
        $product_size = explode("-", $data['size']);//gettting product size
//dd($product_size);
       
        $getProductStock = ProductsAttribute::where(['product_id'=>$data['product_id'], 'size'=>$product_size[1]])->first();
        //dd($getProductStock);
        if($getProductStock->stock<$data['quantity']){
          return redirect()->back()->with('flash_message_error','Required Quantity is not available');
        }
        // echo $getProductStock->stock; die;

         if(empty(Auth::user()->email)){
            $data['user_email'] = '';
        }else{
          $data['user_email'] = Auth::user()->email;
        }
            $session_id = Session::get('session_id');
        if(empty($session_id)){
            $session_id = str_random(40);
            Session::put('session_id',$session_id);
        }
              
       $sizeArr = explode("-", $data['size']);
       $product_size = $sizeArr[1];
       if(empty(Auth::check())){
         $countProducts = DB::table('cart')->where(['product_id' => $data['product_id'],'product_color' => $data['product_color'],'size' =>$product_size, 'session_id' => $session_id])->count();

        if($countProducts > 0){
            return redirect()->back()->with('flash_message_error', 'Product Already Exists in the Cart');

       }
     }
       else{
         $countProducts = DB::table('cart')->where(['product_id' => $data['product_id'],'product_color' => $data['product_color'],'size' =>$product_size, 'user_email' =>$data['user_email']])->count();

        if($countProducts > 0){
            return redirect()->back()->with('flash_message_error', 'Product Already Exists in the Cart');

       }

       
       

            $getSKU = ProductsAttribute::select('sku')->where(['product_id' => $data['product_id'], 'size' => $sizeArr[1]])->first();
            DB::table('cart')->insert(['product_id' => $data['product_id'],'product_name' => $data['product_name'],'product_code' =>$getSKU->sku,'product_color' => $data['product_color'],'price'=>$data['price'],'size'=>$sizeArr[1],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],'session_id' =>$session_id]);
      
  }
      return redirect('cart')->with('flash_message_success','product has been added in cart');
    }

      public function cart(){
        if(Auth::check()){
            $user_email = Auth::user()->email;
            $usercart = DB::table('cart')->where(['user_email' => $user_email])->get();     
        }else{
            $session_id = Session::get('session_id');
            $usercart = DB::table('cart')->where(['session_id' => $session_id])->get();    
              //dd($usercart);
        }
        foreach($usercart as $key => $product){
            $productDetails = Product::where('id', $product->product_id)->first();//with this product_id we are going to get product image
            $usercart[$key]->image = $productDetails->image;
        }
        //echo "<pre>"; print_r($usercart); die;
        $meta_title = "Shopping Cart - E-com Website";
        $meta_description = "View Shopping Cart - E-com Website";
        $meta_keywords = "shopping cart, e-com website";

        return view('products.cart')->with(compact('usercart', 'meta_title','meta_description','meta_keywords'));
    }

     public function deleteCartProduct($id = null){
     	//echo($id); die;
 Session::forget('CouponAmount');
       Session::forget('CouponCode');
        DB::table('cart')->where('id', $id)->delete();
        return redirect('cart')->with('flash_message_success', 'product has been Deleted Successfully from cart');
    }
     


     public function updateCartQuantity($id = null, $quantity = null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        $getProductSKU = DB::table('cart')->select('product_code','quantity')->where('id', $id)->first();
        $getProductStock = ProductsAttribute::where('sku', $getProductSKU->product_code)->first();
        $updated_quantity = $getProductSKU->quantity + $quantity;
        // if($getAttributeStock['stock'] >= $updated_quantity){//this is the quantity that is coming from product attributes table 
        if($getProductStock->stock >= $updated_quantity){
        DB::table('cart')->where('id', $id)->increment('quantity', $quantity);//increment or decrement the product quantity by 1
            return redirect('cart')->with('flash_message_success', 'Product Quantity has been Updated');
        }else{
            return redirect('cart')->with('flash_message_success', 'Required Product Quantity is not available');
        }
    }


    public function applyCoupon(Request $request){

Session::forget('CouponAmount');
Session::forget('CouponCode');

      $data = $request->all();
     // dd($data);
      $CouponCount = Coupon::where('coupon_code', $data['coupon_code'])->count();
      if ($CouponCount == 0) {
        return redirect()->back()->with('flash_message_error', 'This Coupon doesnot exists ');
      }
      else{
        //will perform other checks like Active/Inactive, Expiry Date

        //Get Coupon Details
       $couponDetails = Coupon::where('coupon_code', $data['coupon_code'])->first();

       //If coupon is Inactive
       if($couponDetails->status==0){
                return redirect()->back()->with('flash_message_error', 'This Coupon is not active!! ');
       }
       //if coupon is expired
       $expiry_date =$couponDetails->expiry_date;   
       $current_date = date('Y-m-d');
       if($expiry_date < $current_date){ //expiry date must be greater than current date
        return redirect()->back()->with('flash_message_error', 'This Coupon is expired!! ');
       }
      //Coupon is Valid for Discount
      //Get Cart Total Amount
        $session_id =Session::get('session_id');
         if(Auth::check()){
            $user_email = Auth::user()->email;
            $usercart = DB::table('cart')->where(['user_email' => $user_email])->get();     
        }else{
            $session_id = Session::get('session_id');
            $usercart = DB::table('cart')->where(['session_id' => $session_id])->get();    
              //dd($usercart);
        }
         $total_amount = 0;
        foreach($usercart as $item){
           $total_amount = $total_amount +($item->price * $item->quantity);
        }
      //Check if Amount type is fixed or percentage
       if($couponDetails->amount_type == "Fixed"){
        $couponAmount = $couponDetails->amount;
       }
       else{
        $couponAmount = $total_amount * ($couponDetails->amount/100);
       }
      //Add Coupon Code and Amount in Session
       Session::put('CouponAmount', $couponAmount);
       Session::put('CouponCode', $data['coupon_code']);
      return redirect()->back()->with('flash_message_success', 'Coupon Code successfully applied. You are availing Discount!! ');



      }

    }
   
public function checkouts(Request $request){
  $user_id = Auth::user()->id;
  $user_email = Auth::user()->email;

  $userDetails = User::find($user_id); //get all details of authenticated id to show entered value of account.blade.php in billing of checkout.blade.php
  $countries = Country::get();

  //Check if Shipping Address exists
        $shippingCount = DeliveryAddress::where('user_id',$user_id)->count(); 
        $shippingDetails = array();//array() is used because error was coming as Undefined Variable:$shippingDetails so to resolve this we write this code.
        if($shippingCount>0){
            $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();
        }

 // Update cart table with user email
        $session_id = Session::get('session_id');
        DB::table('cart')->where(['session_id'=>$session_id])->update(['user_email'=>$user_email]);
  if($request->isMethod('post')){
    $data = $request->all();
    // dd($data);
    // Return to Checkout page if any of the field is empty
            if(empty($data['billing_name']) || empty($data['billing_address']) || empty($data['billing_city']) || empty($data['billing_state']) || empty($data['billing_country']) || empty($data['billing_pincode']) || empty($data['billing_mobile']) || empty($data['shipping_name']) || empty($data['shipping_address']) || empty($data['shipping_city']) || empty($data['shipping_state']) || empty($data['shipping_country']) || empty($data['shipping_pincode']) || empty($data['shipping_mobile'])){
                    return redirect()->back()->with('flash_message_error','Please fill all fields to Checkout!');
            }

              // Update User details
            User::where('id',$user_id)->update(['name'=>$data['billing_name'],'address'=>$data['billing_address'],'city'=>$data['billing_city'],'state'=>$data['billing_state'],'pincode'=>$data['billing_pincode'],'country'=>$data['billing_country'],'mobile'=>$data['billing_mobile']]);//save updated data in users table(after doing checkout shows table data in form automatically  )

              if($shippingCount>0){
                // Update Shipping Address
                DeliveryAddress::where('user_id',$user_id)->update(['name'=>$data['shipping_name'],'address'=>$data['shipping_address'],'city'=>$data['shipping_city'],'state'=>$data['shipping_state'],'pincode'=>$data['shipping_pincode'],'country'=>$data['shipping_country'],'mobile'=>$data['shipping_mobile']]);//save updated data in delivery_address table(after doing checkout shows table data in form automatically  )
            }else{
                // Add New Shipping Address
                $shipping = new DeliveryAddress;
                $shipping->user_id = $user_id;
                $shipping->user_email = $user_email;
                $shipping->name = $data['shipping_name'];
                $shipping->address = $data['shipping_address'];
                $shipping->city = $data['shipping_city'];
                $shipping->state = $data['shipping_state'];
                $shipping->pincode = $data['shipping_pincode'];
                $shipping->country = $data['shipping_country'];
                $shipping->mobile = $data['shipping_mobile'];
                $shipping->save();
            }

             //Checking if pincode location is available
            $pincodeCount =DB::table('pincodes')->where('pincode',$data['shipping_pincode'])->count();
            if($pincodeCount ==0){
            	return redirect()->back()->with('flash_message_error','Your location is not available for delivery. Please enter another location.');
            }
           // echo"Redirect to Order Review Page"; die;
           return redirect()->action('ProductsController@orderReview');   
  }
  $meta_title ="Checkout E-com Website";
  return view('products.checkout')->with(compact('userDetails','countries','shippingDetails','meta_title'));
}
    
    public function orderReview(){
        $user_id = Auth::user()->id;
        $user_email = Auth::user()->email;
        $userDetails = User::where('id',$user_id)->first();//get billing details 
        $shippingDetails = DeliveryAddress::where('user_id',$user_id)->first();//get shipping details
        $shippingDetails = json_decode(json_encode($shippingDetails));
         //dd($shippingDetails);
        $userCart = DB::table('cart')->where(['user_email' => $user_email])->get();
        foreach($userCart as $key => $product){
            $productDetails = Product::where('id',$product->product_id)->first();
            $userCart[$key]->image = $productDetails->image;
        }
        /*echo "<pre>"; print_r($userCart); die;*/
         $codpincodeCount =DB::table('cod_pincodes')->where('pincode',$shippingDetails->pincode)->count();
         $prepaidpincodeCount =DB::table('prepaid_pincodes')->where('pincode',$shippingDetails->pincode)->count();

         $meta_title ="Order Review E-com Website";
        return view('products.order_review')->with(compact('userDetails','shippingDetails','userCart','meta_title','codpincodeCount','prepaidpincodeCount'));
    }
    public function placeOrder(Request $request){
      if($request->isMethod('post')){
        $data = $request->all();
        $user_id = Auth::user()->id;
            $user_email = Auth::user()->email;

            // Get Shipping Address of User
            $shippingDetails = DeliveryAddress::where(['user_email' => $user_email])->first();

            //Checking if pincode location is available
            $pincodeCount =DB::table('pincodes')->where('pincode', $shippingDetails->pincode)->count();
            if($pincodeCount ==0){
            	return redirect()->back()->with('flash_message_error','Your location is not available for delivery. Please enter another location.');
            }
            /*echo "<pre>"; print_r($data); die;*/
            if(empty(Session::get('CouponCode'))){
               $coupon_code = ''; 
            }else{
               $coupon_code = Session::get('CouponCode'); 
            }
            if(empty(Session::get('CouponAmount'))){
               $coupon_amount = ''; 
            }else{
               $coupon_amount = Session::get('CouponAmount'); 
            }
            $order = new Order;
            $order->user_id = $user_id;
            $order->user_email = $user_email;
            $order->name = $shippingDetails->name;
            $order->address = $shippingDetails->address;
            $order->city = $shippingDetails->city;
            $order->state = $shippingDetails->state;
            $order->pincode = $shippingDetails->pincode;
            $order->country = $shippingDetails->country;
            $order->mobile = $shippingDetails->mobile;
            $order->coupon_code = $coupon_code;
            $order->coupon_amount = $coupon_amount;
            $order->order_status = "New";
            $order->payment_method = $data['payment_method'];
            $order->grand_total = $data['grand_total'];
            $order->save();//save in orders table
           
            $order_id = DB::getPdo()->lastInsertId();
            $cartProducts = DB::table('cart')->where(['user_email'=>$user_email])->get();
            foreach($cartProducts as $pro){
                $cartPro = new OrdersProduct;
                $cartPro->order_id = $order_id;
                $cartPro->user_id = $user_id;
                $cartPro->product_id = $pro->product_id;
                $cartPro->product_code = $pro->product_code;
                $cartPro->product_name = $pro->product_name;
                $cartPro->product_color = $pro->product_color;
                $cartPro->product_size = $pro->size;
                $cartPro->product_price = $pro->price;
                $cartPro->product_qty = $pro->quantity;
                $cartPro->save();//save in orders_products table
            }
           
            Session::put('order_id',$order_id);
            Session::put('grand_total',$data['grand_total']);

            //Showing order details in email
            if($data['payment_method']=="COD"){
                $productDetails = Order::with('orders')->where('id',$order_id)->first();//show shipping details
                $productDetails = json_decode(json_encode($productDetails),true);
                /*echo "<pre>"; print_r($productDetails);*/ /*die;*/
                $userDetails = User::where('id',$user_id)->first();//show billing details
                $userDetails = json_decode(json_encode($userDetails),true);
                /*echo "<pre>"; print_r($userDetails); die;
*/
                /* Code for Order Email Start */
                $email = $user_email;
                $messageData = [
                    'email' => $email,
                    'name' => $shippingDetails->name,
                    'order_id' => $order_id,
                    'productDetails' => $productDetails,
                    'userDetails' => $userDetails
                ];
                Mail::send('emails.order',$messageData,function($message) use($email){
                    $message->to($email)->subject('Order Placed - E-com Website');    
                });
                /* Code for Order Email Ends */
                // COD - Redirect user to thanks page after saving order
                return redirect('/thanks');
            }
            else{
              //Paypal -Redirect user to paypal page after saving order
               return redirect('/paypal');
            }
           
        }
    }
    public function thanks(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
      return view('orders.thanks');
    }

   public function thanksPaypal(){
    return view('orders.thanks_paypal');
   }

      public function paypal(Request $request){
        $user_email = Auth::user()->email;
        DB::table('cart')->where('user_email',$user_email)->delete();
      return view('orders.paypal');
    }


      public function cancelPaypal(){
        return view('orders.cancel_paypal');
      }
      public function userOrders(){
        $user_id = Auth::user()->id;
        $orders = Order::with('orders')->where('user_id',$user_id)->orderBy('id','DESC')->get();  //combining function orders with Order model so that along with order details order product details will also come.
        /*$orders = json_decode(json_encode($orders));
        echo "<pre>"; print_r($orders); die;*/
        return view('orders.user_orders')->with(compact('orders'));
    }
    //Showing products in user_order_details.blade.php
     public function userOrderDetails($order_id){
        $user_id = Auth::user()->id;
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        return view('orders.user_order_details')->with(compact('orderDetails'));
    }
    public function viewOrders(){
        $orders = Order::with('orders')->orderBy('id','Desc')->get();
        $orders = json_decode(json_encode($orders));
        //shows all details of orders table and orders_products table(see by inspection) 
        //dd($orders);
        /*echo "<pre>"; print_r($orders); die;*/
        return view('admin.orders.view_orders')->with(compact('orders'));
    }

      public function viewOrderDetails($order_id){
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        //dd($orderDetails);
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;//shows which user orderdetails is coming from
       //dd($user_id);

        $userDetails = User::where('id',$user_id)->first();
        /*$userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.order_details')->with(compact('orderDetails','userDetails'));
    }

      public function viewOrderInvoice($order_id){
        $orderDetails = Order::with('orders')->where('id',$order_id)->first();
        $orderDetails = json_decode(json_encode($orderDetails));
        /*echo "<pre>"; print_r($orderDetails); die;*/
        $user_id = $orderDetails->user_id;
        $userDetails = User::where('id',$user_id)->first();
       /* $userDetails = json_decode(json_encode($userDetails));
        echo "<pre>"; print_r($userDetails);*/
        return view('admin.orders.order_invoice')->with(compact('orderDetails','userDetails'));
    }
    public function updateOrderStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);
            return redirect()->back()->with('flash_message_success','Order Status has been updated successfully!');
        }
    }

    public function checkPincode(Request $request){
    	if($request->isMethod('post')){
    		$data = $request->all();
    		//See if pincode is available or not
    		$pincodeCount = DB::table('pincodes')->where('pincode',$data['pincode'])->count();

    		//Another way to display message
    		if($pincodeCount>0){
    			echo "This pincode is available for delivery";
    		}else{
    			echo "This pincode is not available for delivery";
    		}
    	}
    }
 
}


