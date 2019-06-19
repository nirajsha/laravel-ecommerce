@extends('layouts.frontlayout.front_design')
@section('content')
<?php use App\Product; ?>{{--//use for converting Inr to usd or other price in front end--}}
<section>
		<div class="container">
			<div class="row">
				 @if(Session::has('flash_message_success')) 
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{!! session('flash_message_success') !!}</strong>
                </div> 
            @endif
            @if(Session::has('flash_message_error')) 
                <div class="alert alert-error alert-block" style="background-color:#f2dfd0;">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{!! session('flash_message_error') !!}</strong>
                </div> 
            @endif
				<div class="col-sm-3">
			@include('layouts.frontlayout.front_sidebar')
				</div>

						<div class="col-sm-9 padding-right">

					<div class="product-details"><!--product-details-->
						<div class="col-sm-5">
								<div class="view-product">
                            <div class="easyzoom easyzoom--overlay easyzoom--with-thumbnails">
                            <a href="{{ asset('images/backend_images/products/large/'.$productDetails->image)}}">
							<img style="width:300px;"class="mainImage" src="{{ asset('images/backend_images/products/medium/'.$productDetails->image)}}" alt="" />
                            </a>
							<h3>ZOOM</h3>
                            </div>
						</div>
									<div id="similar-product" class="carousel slide" data-ride="carousel">
                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" >
                                    <div class="item active thumbnails">
                                        <a href="{{asset('images/backend_images/products/large/'.$productDetails->image)}}" data-standard="{{asset('images/backend_images/products/small/'.$productDetails->image)}}">
                                                <img class="changeImage" style="width:80px;"
                                                class="mainImage" src="{{asset('images/backend_images/products/small/'.$productDetails->image)}}" alt="" >
                                        </a>
                                        @foreach($productAltImages as $altImage)
                                            <a href="{{asset('images/backend_images/products/large/'.$altImage->image)}}" data-standard="{{asset('images/backend_images/products/small/'.$altImage->image)}}">
                                                <img class="changeImage" src="{{asset('images/backend_images/products/small/'.$altImage->image)}}" alt="" style="width:80px;">
                                            </a>
                                         @endforeach
                                    </div>

                                </div>
                        </div>

						</div>


						<div class="col-sm-7">
							 <form action="{{url('add-cart')}}" name="addtocartForm" method="post" id="addtoCart"> @csrf
							 <input type="hidden" name="product_id" value="{{$productDetails->id}}">
                            <input type="hidden" name="product_name" value="{{$productDetails->product_name}}">
                            <input type="hidden" name="product_code" value="{{$productDetails->product_code}}">
                            <input type="hidden" name="product_color" value="{{$productDetails->product_color}}">
                            <input type="hidden" id="price" name="price" id="price" value="{{$productDetails->price}}">
							<div class="product-information"><!--/product-information-->
								<img src="images/product-details/new.jpg" class="newarrival" alt="" />
								<h2>{{$productDetails->product_name}}</h2>
								<p>Code: {{$productDetails->product_code}}</p>
								<p>
									<select name="size" id="selSize" style="width:150px;"> 
										<option value="">Select Size</option>
										@foreach($productDetails->attributes as $sizes)
										<option value="{{$productDetails->id}}-{{$sizes->size}}">{{$sizes->size}}</option>
										{{-- //with product id and size we are going to get price(see by inspect id and sizes are coming) --}}
										@endforeach
									</select>
									
								</p>
								{{-- <img src="images/product-details/rating.png" alt="" /> --}}
								<span>
									{{--converting currencies at different currency rate--}}
									<?php $getCurrencyRates = Product::getCurrencyRates($productDetails->price); ?>
									<span id="getPrice">INR {{$productDetails->price}}
										<h2>USD{{$getCurrencyRates['USD_Rate']}}<br>GBP{{$getCurrencyRates['GBP_Rate']}}<br>EUR{{$getCurrencyRates['EUR_Rate']}}<br></h2>

									</span>
									<label>Quantity:</label>
									<input type="text" name="quantity" value="1" />
									@if($total_stock>0)
									<button type="submit" class="btn btn-default cart" id="cartButton">
										<i class="fa fa-shopping-cart"></i>
										Add to cart
									</button>
									@endif
								</span>
								<p><b>Availability:</b> <span id="Availability">@if($total_stock>0) In Stock @else Out of stock @endif </p></span>
								<p><b>Condition:</b> New</p>
								<p><b>Delivery:</b></p><input type="text" name="pincode" id="chkPincode" placeholder="Check Pincode">{{-- this function checks whether the pincode is available or not--}}
								<button type="button"  onclick="return checkPincode();">Go</button>
								<span id="pincodeResponse"></span>




								<p><b>Brand:</b> E-SHOPPER</p>
								<a href=""><img src="images/product-details/share.png" class="share img-responsive"  alt="" /></a>
							</div><!--/product-information-->
						</form>
						</div>
					</div><!--/product-details-->
					
					<div class="category-tab shop-details-tab"><!--category-tab-->
						<div class="col-sm-12">
							<ul class="nav nav-tabs">
								<li><a href="#description" data-toggle="tab">Description</a></li>
								<li><a href="#care" data-toggle="tab">Material & Care</a></li>
								<li><a href="#delivery" data-toggle="tab">Delivery Options</a></li>
								@if(!empty($productDetails->video)){{--if the product has not video then not show product video tab--}}
								<li><a href="#video" data-toggle="tab">Product Video</a></li>
								@endif

								
							</ul>
						</div>
						<div class="tab-content">
							<div class="tab-pane fade active in" id="description" >
								<div class="col-sm-12">
									<p>{{$productDetails->description}}</p>
								</div>
								
							</div>
							
							<div class="tab-pane fade" id="care" >
								<div class="col-sm-12">
									<p>{{$productDetails->care}}</p>
								</div>
							</div>
							
							<div class="tab-pane fade" id="delivery" >
								<div class="col-sm-12">
									<p>100% Original Products<br>Cash on delivery</p>
								</div>
							</div>
							@if(!empty($productDetails->video)){{--if the product has not video then not show video --}}
							<div class="tab-pane fade" id="video" >
								<div class="col-sm-12">
									
									<video width="600" height="400" controls>
									  <source src="{{url('videos/'.$productDetails->video)}}" type="video/mp4">
									  {{--<source src="movie.ogg" type="video/ogg">--}}
									
									</video>
								</div>
								@endif
							</div>
							
							<div class="tab-pane fade active in" id="reviews" >
								<div class="col-sm-12">
									<ul>
										<li><a href=""><i class="fa fa-user"></i>EUGEN</a></li>
										<li><a href=""><i class="fa fa-clock-o"></i>12:41 PM</a></li>
										<li><a href=""><i class="fa fa-calendar-o"></i>31 DEC 2014</a></li>
									</ul>
									<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
									<p><b>Write Your Review</b></p>
									
									<form action="#">
										<span>
											<input type="text" placeholder="Your Name"/>
											<input type="email" placeholder="Email Address"/>
										</span>
										<textarea name="" ></textarea>
										<b>Rating: </b> <img src="images/product-details/rating.png" alt="" />
										<button type="button" class="btn btn-default pull-right">
											Submit
										</button>
									</form>
								</div>
							</div>
							
						</div>
					</div><!--/category-tab-->
					
					<div class="recommended_items"><!--recommended_items-->
						<h2 class="title text-center">Recommended Items</h2>
						
						<div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
								<div class="carousel-inner">
								<?php $count=1; ?>
								@foreach($relatedProducts->chunk(3) as $chunk){{-- //for displaying 3 products at once --}}
								<div <?php if($count==1){ ?> class="item active" <?php } else { ?> class="item" <?php } ?>>	
									@foreach($chunk as $item)
									<div class="col-sm-4">
										<div class="product-image-wrapper">
											<div class="single-products">
												<div class="productinfo text-center">
													<img style="width:200px;" src="{{ asset('images/backend_images/products/small/'.$item->image) }}" alt="" />
													<h2>INR {{ $item->price }}</h2>
													<p>{{ $item->product_name }}</p>
													<a href="{{ url('/product/'.$item->id) }}"><button type="button" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</button></a>
												</div>
											</div>
										</div>
									</div>
									@endforeach
								</div>
								<?php $count++; ?>
								@endforeach
							</div>
							 <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
								<i class="fa fa-angle-left"></i>
							  </a>
							  <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
								<i class="fa fa-angle-right"></i>
							  </a>			
						</div>
					</div><!--/recommended_items-->
					
				</div>
			</div>
		</div>
	</section>

@endsection
