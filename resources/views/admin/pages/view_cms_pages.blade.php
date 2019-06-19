@extends('layouts.adminlayout.admin_design')
@section('content')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="#" title="Go to Home" class="tip-bottom"><i class="icon-home"></i>Home</a><a href="#">CMS Pages</a>  <a href="#" class="current">View CMS Pages</a> </div>
    <h1>View CMS Pages</h1>
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
            <h5>View CMS Pages</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Page Id</th>
                  <th>Title</th>
                  <th>URL</th>
                  <th>Status</th>
                  <th>Created on</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach( $cmsPages  as $page)
                <tr class="gradeX">
                  <td class="center">{{$page->id}}</td>
                  <td class="center">{{$page->title}}</td>
                  <td class="center">{{$page->url}}</td>
                  <td class="center">@if($page->status==1)Active @else Inactive @endif</td>
                  <td class="center">{{$page->created_at}}</td>
                  
                  <td class="center"><a  href="#myModal{{$page->id}}" data-toggle="modal" class="btn btn-success btn-mini" title="View page">View</a>
                    <a href="{{url ('/admin/edit-cms-page',$page->id) }}" class="btn btn-primary btn-mini" title="Edit page">Edit</a> 
                    <a  href="{{url ('/admin/delete-cms-page',$page->id) }}" class="btn btn-danger btn-mini" >Delete</a>
                  </td> 
                </tr>

               
                 <div id="myModal{{$page->id}}" class="modal hide">
                    <div class="modal-header">
                      <button data-dismiss="modal" class="close" type="button">×</button>
                      <h3>{{ $page->title }} Page Details</h3>
                      {{-- <h3> Full Details</h3> --}}
                    </div>
                    <div class="modal-body">
                      <p><strong>Title:</strong> {{$page->title}}</p>
                      <p><strong>Url:</strong>{{$page->url}}</p>
                      <p><strong>Status:</strong>@if($page->status==1) Active @else Inactive @endif </p>
                      <p><strong>Created On:</strong>{{$page->created_at}} </p>
                      <p><strong>Description:</strong> {{$page->description}} </p>
                    </div>
                  </div>
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










