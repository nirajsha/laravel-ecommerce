{{-- @extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Order; ?> //use model Order same as in Controller(we have used model Order below) --}}
{{-- <section id="cart_items">
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
			  <li><a href="#">Home</a></li>
			  <li class="active">Thanks</li>
			</ol>
		</div>
	</div>
</section>

<section id="do_action">
	<div class="container">
		<div class="heading" align="center">
			<h3>YOUR ORDER HAS BEEN PLACED</h3>
			<p>Your order number is {{ Session::get('order_id') }} and total payable about is INR {{ Session::get('grand_total') }}</p>
			<p>Please make payment by clicking on below Payment Button</p>
			<?php
			$orderDetails = Order::getOrderDetails(Session::get('order_id'));//getting getOrderDetails from Order.php(we are getting order id from session and send that order_id in function which is defined in order model)
			$orderDetails = json_decode(json_encode($orderDetails));
			/*echo "<pre>"; print_r($orderDetails); die;*/
			//$nameArr = explode(' ',$orderDetails->name);//In 0 array first name will come and in 1 array last name will come.(we are breaking full name into two parts)
			//$getCountryCode = Order::getCountryCode($orderDetails->country);
			?>
			 <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post"> --}}
				{{-- <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="nirssh1-facilitator@gmail.com">
				<input type="text" name="item_name" value="{{ Session::get('order_id') }}">
				<input type="text" name="currency_code" value="INR">
				<input type="text" name="amount" value="{{ Session::get('grand_total') }}">
				<input type="text" name="first_name" value="John">
				<input type="text" name="last_name"  value="Doe">
				<input type="text" name="address1" value="{{ $orderDetails->address }}">--}}
				{{-- <input type="text" name="address2" value="">
				<input type="text" name="city" value="{{ $orderDetails->city }}">
				<input type="text" name="state" value="{{ $orderDetails->state }}">
				<input type="text" name="zip" value="{{ $orderDetails->pincode }}">
				<input type="text" name="email" value="{{ $orderDetails->user_email }}">
				 <input type="text" name="country" value="{{ $getCountryCode->country_code }}"> --}}
				{{-- <input type="text" name="return" value="{{ url('paypal/thanks') }}">
				<input type="text" name="cancel_return" value="{{ url('paypal/cancel') }}">
				<input type="image"
				    src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_paynow_107x26.png" alt="Pay Now">
				  <img alt="" src="https://paypalobjects.com/en_US/i/scr/pixel.gif"
				    width="1" height="1">
			</form>
		</div>
	</div>
</section>

@endsection

<?php
Session::forget('grand_total');
Session::forget('order_id');
?> --}} 


@extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Order; ?>
<section id="cart_items">
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
			  <li><a href="#">Home</a></li>
			  <li class="active">Thanks</li>
			</ol>
		</div>
	</div>
</section>

<section id="do_action">
	<div class="container">
		<div class="heading" align="center">
			<h3>YOUR ORDER HAS BEEN PLACED</h3>
			<p>Your order number is {{ Session::get('order_id') }} and total payable about is INR {{ Session::get('grand_total') }}</p>
			<p>Please make payment by clicking on below Payment Button</p>
			<?php
			$orderDetails = Order::getOrderDetails(Session::get('order_id'));
			$orderDetails = json_decode(json_encode($orderDetails));
			/*echo "<pre>"; print_r($orderDetails); die;*/
			//$nameArr = explode(' ',$orderDetails->name);
			$getCountryCode = Order::getCountryCode($orderDetails->country);
			?>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="nirssh1-facilitator@gmail.com">
				<input type="hidden" name="item_name" value="{{ Session::get('order_id') }}">
				<input type="hidden" name="currency_code" value="INR">
				<input type="hidden" name="amount" value="{{ Session::get('grand_total') }}">
				{{-- <input type="hidden" name="first_name" value="{{ $nameArr[0] }}">
				<input type="hidden" name="last_name" value="{{ $nameArr[1] }}"> --}}
				<input type="hidden" name="address1" value="{{ $orderDetails->address }}">
				<input type="hidden" name="address2" value="">
				<input type="hidden" name="city" value="{{ $orderDetails->city }}">
				<input type="hidden" name="state" value="{{ $orderDetails->state }}">
				<input type="hidden" name="zip" value="{{ $orderDetails->pincode }}">
				<input type="hidden" name="email" value="{{ $orderDetails->user_email }}">
				<input type="hidden" name="country" value="{{ $getCountryCode->country_code }}">
				<input type="hidden" name="return" value="{{ url('paypal/thanks') }}">
				<input type="hidden" name="cancel_return" value="{{ url('paypal/cancel') }}">
				<input type="image"
				    src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_paynow_107x26.png" alt="Pay Now">
				  <img alt="" src="https://paypalobjects.com/en_US/i/scr/pixel.gif"
				    width="1" height="1">
			</form>
		</div>
	</div>
</section>

@endsection

<?php
Session::forget('grand_total');
Session::forget('order_id');
?>
© 2019 GitHub, Inc.