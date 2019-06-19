@extends('layouts.adminlayout.admin_design')
@section('content')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Go to Home" class="tip-bottom"><i class="icon-home"></i>Home</a> <a href="#" class="current">View Currencies</a> </div>
    <h1>View Currencies</h1>
     @if(Session::has('flash_message_error'))
                <div class="alert alert-error alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{!! session('flash_message_error') !!}</strong>
                </div>
            @endif
            @if(Session::has('flash_message_success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{!! session('flash_message_success') !!}</strong>
                </div>
            @endif
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
            <h5>View Currencies</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th style="text-align:left;">Id</th>
                  <th style="text-align:left;">Currency Code</th>
                  <th style="text-align:left;">Exchange Rate</th>
                  <th style="text-align:left;">Updated at</th>
                  <th style="text-align:left;">Actions</th>
                  
                </tr>
              </thead>
              <tbody>
                @foreach($currencies as $currency)
                <tr class="gradeX">
                  <td>{{$currency->id}}</td>
                  <td>{{$currency->currency_code}}</td>
                  <td>{{$currency->exchange_rate}}</td>
                  <td>{{$currency->updated_at}}</td>
                  <td class="center">
                    <a href="{{url ('/admin/edit-currency',$currency->id) }}" class="btn btn-primary btn-mini">Edit</a> 
                     <a rel="{{$currency->id}}" rel1="/delete-currency" class="btn btn-danger btn-mini deletecurrency" href="javascript:" >Delete</a>
                  </td>
                </tr>
                @endforeach
                
                
              </tbody>
            </table>
          </div>
        </div>
       
      </div>
    </div>
  </div>
</div>
@endsection
