<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CmsPage;
use App\Category;
use Illuminate\Support\Facades\Mail;
use Validator;

class CmsController extends Controller
{
   public function addCmsPage(Request $request){
   	if($request->isMethod('post')){
   		$data =$request->all();
   		// dd($data);
   		if(empty($data['meta_title'])){
   			$data['meta_title'] = "";
   		}
   		if(empty($data['meta_description'])){
   			$data['meta_description'] = "";
   		}
   		if(empty($data['meta_keywords'])){
   			$data['meta_keywords'] = "";
   		}
   		$cmspage = new CmsPage;
   		$cmspage->title =$data['title'];
   		$cmspage->url =$data['url'];
   		$cmspage->description =$data['description'];
   		$cmspage->meta_title =$data['meta_title'];
   		$cmspage->meta_description =$data['meta_description'];
   		$cmspage->meta_keywords =$data['meta_keywords'];
   		if(empty($data['status'])){
   			$status = 0;
   		}else{
   			$status =1;
   		}
   		$cmspage->status = $status;
   		$cmspage->save();
   		return redirect()->back()->with('flash_message_success', 'CMS Page has been added successfully');
   	}
   	return view('admin.pages.add_cms_page');
   } 

    public function editCmsPage(Request $request, $id){
    	if($request->isMethod('post')){
    		$data =$request->all();
    		// dd($data);
    		if(empty($data['status'])){
   			$status = 0;
   		}else{
   			$status =1;
   		}
   		if(empty($data['meta_title'])){
   			$data['meta_title'] = "";
   		}
   		if(empty($data['meta_description'])){
   			$data['meta_description'] = "";
   		}
   		if(empty($data['meta_keywords'])){
   			$data['meta_keywords'] = "";
   		}
    		CmsPage::where('id', $id)->update(['title'=>$data['title'], 'url'=>$data['url'],'description'=>$data['description'],'meta_title'=>$data['meta_title'],'meta_description'=>$data['meta_description'],'meta_keywords'=>$data['meta_keywords'], 'status'=>$status]);
    		return redirect()->back()->with('flash_message_success', 'CMS page has been updated successfully');
    	}
    	$cmsPage =CmsPage::where('id',$id)->first();
    	$cmsPage = json_decode(json_encode($cmsPage));
    	// dd($cmsPage);
    	return view('admin.pages.edit_cms_page')->with(compact('cmsPage'));

    }
   public function viewCmsPages(){
   	$cmsPages = CmsPage::get();
   	$cmsPages =json_decode(json_encode($cmsPages));
   	// dd($cmsPages);
   	return view('admin.pages.view_cms_pages')->with(compact('cmsPages'));
   }
   public function deleteCmsPage($id){
CmsPage::where('id',$id)->delete();
return redirect('/admin/view_cms_pages')->with('flash_message_success', 'CMS Page has been deleted successfully!');
   }
   public function cmsPage($url){
   	//Redirect to 404 if CMS Page is disabled or doesnot exists
   	$cmsPageCount = CmsPage::where(['url'=>$url, 'status'=>1])->count();
   	if($cmsPageCount>0){
   			//Get Cms Page Details
   	$cmsPageDetails= CmsPage::where('url',$url)->first();
   	// dd($cmsPageDetails);
   	$meta_title = $cmsPageDetails->meta_title;
   	$meta_description = $cmsPageDetails->meta_description;
   	$meta_keywords = $cmsPageDetails->meta_keywords;

   }else{
   	abort(404);
   }
   
   	//Get all categories and subcategories
   	 $categories_menu= "";
   	 $categories = Category::with('categories')->where(['parent_id'=>0])->get();
   	 $categories = json_decode(json_encode($categories));
          foreach($categories as $cat){
    					$categories_menu .="<div class='panel-heading'>
				<h4 class='panel-title'>
					<a data-toggle='collapse' data-parent='#accordian".$cat->id."' 
						href='#".$cat->url."'>
						<span class='badge pull-right'><i class='fa fa-plus'></i></span>
						".$cat->name."
					</a>
				</h4>
			</div>
			<div id='".$cat->id."'  class='panel-collapse collapse'>
				<div class='panel-body'>
					<ul>";

						$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
							foreach($sub_categories as $subcat){
								$categories_menu .= "<li><a href='#'>".$subcat->url."'>".
								$subcat->name."</a></li>";
							}
							$categories_menu .= 
					"</ul>
									</div>
								</div>
								";
         
    		
    	}
   	return view('pages.cms_page')->with(compact('cmsPageDetails','categories_menu','categories','meta_title', 'meta_keywords', 'meta_description'));
   }
   public function contact(Request $request){
   	if($request->isMethod('post')){
   		$data = $request->all();
   		//dd($data);

       //Laravel Validator
        $validator = Validator::make($request->all(),[
        	'name'=>'required|regex:/^[\pL\s\-]+$/u|max:255',//Search regex (using laravel validation for alphabetic characters and spaces) in google
        	'email'=>'required|email',
        	'subject'=>'required',

        ]);
        //(search laravel validator fails in laravel.com)
        if($validator->fails()){
        	return redirect()->back()->withErrors($validator)->withInput();
        }
   		//Send Contact Email
   		$email = "nirssh1@yopmail.com";
   		$messageData = [
   			'name'=>$data['name'],
   			'email'=>$data['email'],
   			'subject'=>$data['subject'],
   			'comment'=>$data['message']
   		];
   	
   		//sending message to email with subject
   		Mail::send('emails.enquiry', $messageData,function($message)use($email){
   			$message->to($email)->subject('Enquiry from E-com WEbsite');
   		});
   		//echo "test";
   		//die;
   		return redirect()->back()->with('flash_message_success', 'Thanks for your enquiry. We will get back to you soon.');
   	}
   	//Get all categories and subcategories
   	//We are showing side menu in contact page so using this code here.
   	 $categories_menu= "";
   	 $categories = Category::with('categories')->where(['parent_id'=>0])->get();
   	 $categories = json_decode(json_encode($categories));
          foreach($categories as $cat){
    					$categories_menu .="<div class='panel-heading'>
				<h4 class='panel-title'>
					<a data-toggle='collapse' data-parent='#accordian".$cat->id."' 
						href='#".$cat->url."'>
						<span class='badge pull-right'><i class='fa fa-plus'></i></span>
						".$cat->name."
					</a>
				</h4>
			</div>
			<div id='".$cat->id."'  class='panel-collapse collapse'>
				<div class='panel-body'>
					<ul>";

						$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
							foreach($sub_categories as $subcat){
								$categories_menu .= "<li><a href='#'>".$subcat->url."'>".
								$subcat->name."</a></li>";
							}
							$categories_menu .= 
					"</ul>
									</div>
								</div>
								";
         
    		
    	}
    	//Meta Tags(for Seo)
        $meta_title = "Contact Us E-shop sample website";
        $meta_description = "Contact Us for any queries related to our products.";
        $meta_keywords = "contact us, queries";
   	return view('pages.contact')->with(compact('categories_menu','categories','meta_title','meta_description','meta_keywords'));
   }
}
