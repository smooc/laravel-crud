<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class panelController extends Controller
{
    public function login(){

    }
    public function logout(){

    }
    public function projects(){

    }
    public function settings(){
        $settings = \App\Models\setting::all();
        return view('panel.settings', ['settings' => $settings]);
    }
    
    public function updateSettings(Request $request){
        $data = $request->validate([
            'beslenmefile' => '',
            'dersfile' => '',
        ]);
        
        if($data['beslenmefile']){
            $setting = \App\Models\setting::findOrFail(2);
            $filePath = $data['beslenmefile']->store('settings','public');
            $setting->update(['value' => $filePath]);
            $setting->save();
        }
        if($data['dersfile']){
            $setting = \App\Models\setting::findOrFail(1);
            $filePath = $data['dersfile']->store('settings','public');
            $setting->update(['value' => $filePath]);
            $setting->save();
        }
        return redirect()->route('settings');
    }
    
   
    function en($en){
        $ara = array ('ı', 'İ', 'ç', 'Ç', 'Ü', 'ü', 'Ö', 'ö', 'ş', 'Ş', 'ğ', 'Ğ');
        $degis = array ('i', 'I', 'c', 'C', 'U', 'u', 'O', 'o', 's', 'S', 'g', 'G');
        $en = str_replace($ara, $degis, $en);
        return $en;
     }
    public function project_store(Request $request){
        $data = $request->validate([
            'title_tr' => '',
            'title_en' => '',
            'place_tr' => '',
            'place_en' => '',
            'content_tr' => '',
            'content_en' => '',
            'cover_image' => '',
            'cover_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg', //|max:2048
            'image' => '',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg', //|max:2048
        ]);
        $url_tr = preg_replace('/[[:space:]]+/', '-', $data['title_tr']);
        $url_tr = $this->en($url_tr);
        $url_tr = strtolower($url_tr);
        $check_projects = \App\Models\project::where('url_tr',$url_tr)->first();
        if($check_projects){
            $url_tr = $url_tr.'-2';
        }
        $url_en = preg_replace('/[[:space:]]+/', '-', $data['title_en']);
        $url_en = $this->en($url_en);
        $url_en = strtolower($url_en);
        $check_projects = \App\Models\project::where('url_en',$url_en)->first();
        if($check_projects){
            $url_en = $url_en.Str::random(7);
        }

        if(\App\Models\project::get()->sortbydesc('project_order')->first()){
            $last_added_project = \App\Models\project::get()->sortbydesc('project_order')->first()->project_order;
        }else{
            $last_added_project = 0;
        }
        if($last_added_project){
            $order = $last_added_project + 1;
        }else{
            $order = 1;
        }
        $project = \App\Models\project::create([
            'title_tr' => $data['title_tr'],
            'title_en' => $data['title_en'],
            'content_tr' => $data['content_tr'],
            'content_en' => $data['content_en'],
            'place_tr' => $data['place_tr'],
            'place_en' => $data['place_en'],
            'url_tr' => $url_tr,
            'url_en' => $url_en,
            'project_order' => $order,
        ]);
        $message = NULL;
        //check if data.image has image
        if($request->hasFile('cover_image')){
            $cover_imagePath = $data['cover_image']->store('project_images','public');
            \App\Models\project_images::create([
                'image' => $cover_imagePath,
                'image_alt' => '',
                'project_id' => $project->id,
                'cover' => true // boolean : make it false for storage
            ]);
        }
        if($request->hasFile('image')){
            foreach ($data['image'] as $image) {
                $imagePath = $image->store('project_images','public');
                \App\Models\project_images::create([
                    'image' => $imagePath,
                    'image_alt' => '',
                    'project_id' => $project->id,
                    'cover' => false // boolean : make it false for storage
                ]);
            }
        }
        return redirect("/panel/projects/$project->id/edit");

    }

    public function project_edit(){
        $id = request('id');
        $project = \App\Models\project::find($id);
       
        
        return view('/panel/edit-project',[
            'project' => $project,
        ]);
    }

    public function update(Request $request){
        $data = $request->validate([
            'title_tr' => '',
            'title_en' => '',
            'place_tr' => '',
            'place_en' => '',
            'content_tr' => '',
            'content_en' => '',
            'cover_image' => '',
            'cover_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg', //|max:2048
            'image' => '',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg', //|max:2048
            'imageDestroy' => '',
        ]);
        $id = $request->id;
        $project = \App\Models\project::find($id);
       
        if($request->hasFile('cover_image')){
            $imageToRemove = $project->project_images->where('cover', 1)->first();
            if($imageToRemove){
                $deletePath = "/project_images/". $imageToRemove->image;
                Storage::delete($deletePath);
                $imageToRemove->delete();
            }

            $cover_imagePath = $data['cover_image']->store('project_images','public');
            \App\Models\project_images::create([
                'image' => $cover_imagePath,
                'image_alt' => '',
                'project_id' => $project->id,
                'cover' => true // boolean : make it false for storage
            ]);
        }
        if($request->hasFile('image')){
            foreach ($data['image'] as $image) {
                $imagePath = $image->store('project_images','public');
                \App\Models\project_images::create([
                    'image' => $imagePath,
                    'image_alt' => '',
                    'project_id' => $project->id,
                    'cover' => false // boolean : make it false for storage
                ]);
            }
        }
        if (isset($data['imageDestroy'])) {
            foreach ($data['imageDestroy'] as $image) {
                $deleteImage = \App\Models\project_images::findOrFail($image);
                $deletePath = $deleteImage->image;
                Storage::disk('public')->delete($deletePath);
                $deleteImage->delete();
            }
        }
        $project->update(
            [
                'title_tr' => $data['title_tr'],
                'title_en' => $data['title_en'],
                'content_tr' => $data['content_tr'],
                'content_en' => $data['content_en'],
                'place_tr' => $data['place_tr'],
                'place_en' => $data['place_en'],
            ]);
        
           //send user to index page with notification
           $response = array(
            'message' => 'İçerik Başarılı bir şekilde Güncellendi.',
            'title' => 'Başarılı',
            'type' => 'success'
        );
        return redirect("/panel/projects/$project->id/edit")->with('response' , $response);
    }

   public function destroy(){
        $data = request()->validate([
            'projects_id' => ''
         ]);
         if(isset($data['projects_id'])){
            foreach ($data['projects_id'] as $project_id) {
               $project = \App\Models\project::find($project_id);
                if($project->project_images){
                    try {
                        foreach ($project->project_images as $image) {
                            $deletePath = public_path('/storage/'.$image->image);
                            unlink($deletePath);
                            $image->delete();
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
               $project->delete();
            }
        }
        //send user to index page with notification
        $response = array(
            'message' => 'İçerik başarılı bir şekilde Kaldırıldı.',
            'title' => 'Başarılı',
            'type' => 'success'
        );
        return redirect("/panel/projects")->with('response' , $response);
    }



    public function changeOrder(){
        $data = request()->validate([
            'project_id' => '',
            'project_order' => ''
         ]);
        foreach ($data['project_id'] as $i => $id) {
            $project = \App\Models\project::find($id);
              $project->update([
                'project_order' => $data['project_order'][$i]
            ]);
        }
        $response = array(
            'message' => 'project sırası başarı ile kaydedildi.',
            'title' => 'Başarılı',
            'type' => 'success'
        );
        return redirect("/panel/projects")->with('response' , $response);
    }












}
