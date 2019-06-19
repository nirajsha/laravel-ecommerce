<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session; //for logout
use App\User;
use App\Admin;
use Illuminate\Support\Facades\Hash;//for hash password
class AdminController extends Controller
{
  public function login(Request $request){
  	if($request->isMethod('post')){
  	$data =$request->input();
    $adminCount = Admin::where(['username' => $data['username'], 'password' => md5($data['password']), 'status'=>1])->count(); 
    if($adminCount > 0){

  		//echo"success"; die;
      Session::put('adminSession', $data['username']);
  		return redirect('admin/dashboard') 	;
  	}
  else {
  	// echo "failed"; die;
                return redirect('/admin')->with('flash_message_error', 'Invalid Username or Password');
            }
  }
  	  
  	return view('admin.admin_login');
  }  
  public function dashboard(){
  	return view('admin.dashboard');
  }
   public function settings(){
   	// dd('here');
    $adminDetails = Admin::where(['username'=>Session::get('adminSession')])->first();//getting username from adminSession which is defined above.
  	return view('admin.settings')->with(compact('adminDetails'));
  }
      public function chkPassword(Request $request){
        $data = $request->all();
        // $current_password = $data['current_pwd'];
        // $check_password = Admin::where(['username'=>Session::get('adminSession')])->first();
          $adminCount = Admin::where(['username' =>Session::get('adminSession'), 'password' => md5($data['current_pwd'])])->count(); 
          // if(Hash::check($current_password,$check_password->password)){
        if($adminCount == 1){
            echo "true"; die;
        } else {
            echo "false"; die;
        }
    }
        public function updatePassword(Request $request){
        if($request->isMethod('post'))
        { $data = $request->all();
            // $user = Auth::user()->email;
            // $check_password = User::where(['email' => $user])->first();
            // $current_password = $data['current_pwd'];
             $adminCount = Admin::where(['username' =>Session::get('adminSession'), 'password' => md5($data['current_pwd'])])->count(); 
            // if(Hash::check($current_password,$check_password->password)){
                // $password = bcrypt($data['new_pwd']);
             $password = md5($data['new_pwd']);
                Admin::where('username', Session::get('adminSession'))->update(['password'=>$password]);
                return redirect('/admin/settings')->with('flash_message_success','Password updated Successfully!');
            }else {
                return redirect('/admin/settings')->with('flash_message_error','Incorrect Current Password!');
            }
        }
    


   public function logout(){
  Session::flush();//clear all sessions
  return redirect('/admin')->with('flash_message_success', 'Logged Out Successfully');
  }

}
