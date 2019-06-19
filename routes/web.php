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


//Index Page
Route::get('/', 'IndexController@index'); 

//Home Page
Route::get('/home', 'HomeController@index')->name('home'); 

Auth::routes();

Route::match(['get', 'post'], '/admin','AdminController@login');

//Category Listing Page
Route::get('products/{url}','ProductsController@products');//we will be displaying url of products from category table

//Product Detail Page
Route::get('product/{id}','ProductsController@product');


//Cart Page
Route::match(['get', 'post'], '/cart','ProductsController@cart');


//Add to Cart Route
Route::match(['get', 'post'], '/add-cart','ProductsController@addtocart');


//Delete Product from Cart Page
Route::get('/cart/delete-product/{id}','ProductsController@deleteCartProduct');

//Update Product Quantity in Cart
Route::get('/cart/update-quantity/{id}/{quantity}','ProductsController@updateCartQuantity');

//Get Product Attributes Price
Route::get('get-product-price','ProductsController@getProductPrice');

// Users Login/Register Page
Route::match(['get', 'post'],'/login-register','UsersController@userLoginRegister');

Route::match(['get', 'post'],'/forgot-password','UsersController@forgotPassword');


//User Register Form Submit
Route::match(['get', 'post'],'/user-register','UsersController@register');

//Confirm Account
Route::get('confirm/{code}', 'UsersController@confirmAccount');

//Users Login
Route::post('user-login', 'UsersController@login');
 
//Users Logout
Route::get('/user-logout', 'UsersController@logouts'); 

//Search Products
Route::post('/search-products', 'ProductsController@searchProducts');

//Check If User Already Exists while filling the register form
Route::match(['get', 'post'],'/check-email','UsersController@checkEmail');//check-email is used in main.js in rules of email.  

//Check Pincode
Route::post('/check-pincode', 'ProductsController@checkPincode');



//All Routes after Login
//Users Account Page
Route::group(['middleware'=>['frontlogin']],function(){
Route::match(['get', 'post'],'/account', 'UsersController@account');
//Check User Current Password
Route::post('check-user-pwd', 'UsersController@chkUserPassword');
//Update User Password
Route::post('update-user-pwd', 'UsersController@updatePassword');
//Checkout Page
Route::match(['get', 'post'],'/checkout', 'ProductsController@checkouts');
//Order Review Page
Route::match(['get', 'post'],'/order-review', 'ProductsController@orderReview');
//Place Order
Route::match(['get', 'post'],'/place-order', 'ProductsController@placeOrder');
//Thanks Page
Route::get('/thanks', 'ProductsController@thanks');

//Paypal Page
Route::get('/paypal', 'ProductsController@paypal');
//Users Orders Page
Route::get('/orders', 'ProductsController@userOrders');
//User Ordered Products Page
Route::get('/orders/{id}', 'ProductsController@userOrderDetails');
//Paypal Thanks Page
Route::get('/payapl/thanks', 'ProductsController@thanksPaypal');
//Paypal Cancel Page
Route::get('/payapl/cancel', 'ProductsController@cancelPaypal');
});




//Admin Routes
Route::group(['middleware'=>['adminlogin']],function(){
Route::get('admin/dashboard' ,'AdminController@dashboard');
Route::get('admin/settings' ,'AdminController@settings');
Route::get('admin/check-pwd' ,'AdminController@chkPassword');
Route::match(['get', 'post'], '/admin/update-pwd','AdminController@updatePassword');

//Apply Coupon
Route::post('/cart/apply-coupon','ProductsController@applyCoupon');

//Admin categories routes
Route::match(['get','post'],'/admin/add-category','CategoryController@addCategory');
Route::match(['get','post'],'/admin/edit-category/{id}','CategoryController@editCategory');
Route::match(['get','post'],'/admin/delete-category/{id}','CategoryController@deleteCategory');
Route::get('/admin/view-categories','CategoryController@viewCategories');

//Admin Product Routes
Route::match(['get','post'],'/admin/add-product','ProductsController@addProduct');
Route::match(['get','post'],'/admin/edit-product/{id}','ProductsController@editProduct');
Route::get('/admin/view-products','ProductsController@viewProducts');
// Route::get('/admin/delete-product/{id}','ProductsController@deleteProduct');
Route::get('/admin/delete-product/{id}','ProductsController@deleteProduct');
Route::get('/admin/delete-product-image/{id}','ProductsController@deleteProductImage');
Route::get('/admin/delete-product-video/{id}','ProductsController@deleteProductVideo');

Route::get('/admin/delete-alt-image/{id}','ProductsController@deleteAltImage');


//Admin Product Attribute Routes
Route::match(['get','post'],'/admin/add-attribute/{id}','ProductsController@addAttributes');
Route::match(['get','post'],'/admin/edit-attributes/{id}','ProductsController@editAttributes');
Route::match(['get','post'],'/admin/add-images/{id}','ProductsController@addImages');

Route::get('/admin/delete-attribute/{id}','ProductsController@deleteAttribute');

//Admin Coupon Routes
Route::match(['get','post'],'/admin/add-coupon','CouponsController@addCoupon');
Route::match(['get','post'],'/admin/edit-coupon/{id}','CouponsController@editCoupon');
Route::get('/admin/view-coupons','CouponsController@viewCoupons');
Route::get('/admin/delete-coupon/{id}','CouponsController@deleteCoupon');


//Admin Banners Routes
Route::match(['get','post'],'/admin/add-banner','BannersController@addBanner');
Route::match(['get','post'],'/admin/edit-banner/{id}','BannersController@editBanner');
Route::get('/admin/view-banners','BannersController@viewBanners');
Route::get('/admin/delete-banner/{id}',' BannersController@delete Banner');

//Admin Orders Routes
Route::get('/admin/view-orders','ProductsController@viewOrders');

//Admin Order Details Routes
Route::get('/admin/view-order/{id}','ProductsController@viewOrderDetails');

//Order Invoice
Route::get('/admin/view-order-invoice/{id}','ProductsController@viewOrderInvoice');



//Update Order Status
Route::post('/admin/update-order-status','ProductsController@updateOrderStatus');

//Admin User Routes
Route::get('/admin/view-users', 'UsersController@viewUsers');

//Add CMS Route
Route::match(['get','post'],'/admin/add-cms-page','CmsController@addCmsPage');

//Edit CMS Route
Route::match(['get','post'],'/admin/edit-cms-page/{id}','CmsController@editCmsPage');

//View CMS Pages Route
Route::get('/admin/view-cms-pages', 'CmsController@viewCmsPages');

//Delete CMS Route
Route::get('/admin/delete-cms-page/{id}', 'CmsController@deleteCmsPage');

//Currencies Routes
//Add Currency Route
Route::match(['get','post'],'admin/add-currency','CurrencyController@addCurrency');
//Edit Currency Route
Route::match(['get','post'],'admin/edit-currency/{id}','CurrencyController@editCurrency');
//Delete Currency Route
Route::match(['get','post'],'admin/delete-currency/{id}','CurrencyController@deleteCurrency');
//View Currencies Route
Route::get('/admin/view-currencies','CurrencyController@viewCurrencies');

});
Route::get('logout','AdminController@logout');

//Display Contact Page 
Route::match(['get','post'],'/page/contact','CmsController@contact');//(This is a form we don't want to add it from admin panel)This is static contact page
//if we use Display Cms Page route at first then contact page will never come so use contact page above the cms page route.


//Display Cms Page
Route::match(['get','post'],'/page/{url}','CmsController@cmsPage');//(we are adding it from admin panel like about us, privacy policy, terms and condition page)
//This is dynamic cms page


