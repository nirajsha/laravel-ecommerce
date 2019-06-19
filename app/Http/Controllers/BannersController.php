<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Banner;
use Image;

class BannersController extends Controller
{
    public function addBanner(Request $request){

    	if($request->isMethod('post')){
            $data = $request->all();
           
             if($request->isMethod('post')){
            $data= $request->all();
            }
            $banner = new Banner;
            $banner->title = $data['title'];
            $banner->link = $data['link'];
              if(empty($data['status'])){
            	$status = 0;
            }
            else{
            	$status = 1;
            }
            $banner->status = $status;
       
          
             //Upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){

                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $banner_path = 'images/frontend_images/banners/'.$filename;
                    Image::make($image_tmp)->resize(1140,340)->save($banner_path);
                  // echo "test"; die;
                    $banner->image = $filename;

                }
            }
          

            $banner->save();
            return redirect()->back()->with('flash_message_success','Banner has been added successfully'); 
        }
    
return view('admin.banners.add_banner');
    
}
 public function editBanner(Request $request, $id=null){
 	if($request->isMethod('post')){
            $data= $request->all();
            // dd($data);
             if(empty($data['status'])){
            	$status = 0;
            }
            else{
            	$status = 1;
            }
              if(empty($data['title'])){
            	$title = '';
            }
              if(empty($data['link'])){
            	$link = '';
            }

               //Upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $banner_path = 'images/frontend_images/banners/'.$filename;
                    Image::make($image_tmp)->resize(1140,340)->save($banner_path);//size of banner image
                   
                }
               }
               elseif (!empty($data['current_image'])) { //we want to pick current image not want to select new image so
                	$filename = $data['current_image'];
                	//dd($filename);
                } else{
                	$filename ='';
                }  

                Banner::where('id',$id)->update(['status' => $status, 'title'=>$data['title'],'link'=>$data['link'], 'image'=>$filename]);
                 return redirect()->back()->with('flash_message_success','Banner has been edited successfully'); 
              
        }


 	$bannerDetails = Banner::where('id', $id)->first();
 	return view('admin.banners.edit_banner')->with(compact('bannerDetails'));
 	}
public function viewBanners(){
	$banners = Banner::get();
	return view('admin.banners.view_banners')->with(compact('banners'));
}
 public function deleteBanner($id=null){
      
         Banner::where(['id'=>$id])->delete();
         return redirect()->back()->with('flash_message_success','Banner has been deleted successfully');
    }
}

