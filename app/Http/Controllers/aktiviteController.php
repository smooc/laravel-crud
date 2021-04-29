<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class aktiviteController extends Controller
{

    public $validationrules = [
        'title' => 'required',
        'done' =>  'required|boolean',
        'image' => '',
        'imageDestroy' => ''
    ];
    public $validationMessages = [
        'title.required' => 'Lütfen Başlık alanını doldurunuz.',
        'done.required' =>  'Lütfen Aktiviteyi yapıldı veya planlanan olarak seçiniz.',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aktiviteler = \App\Models\aktivite::all();
        return view('panel.aktivite', [
            'aktiviteler' => $aktiviteler
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('panel.aktivite_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data = $request->validate($this->validationrules,$this->validationMessages);
       $aktivite = \App\Models\aktivite::create([
           'title' => $data['title'],
           'done' => $data['done'],
       ]);

       foreach ($data['image'] as $key => $image) {
            $imagePath = $image->store('aktiviteler','public');
           \App\Models\aktivite_image::create([
                'img_path' => $imagePath,
                'aktivite_id' => $aktivite->id
           ]);
       }

       return redirect('/panel/aktivite');
       //add things to db
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //dont use this
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $aktivite = \App\Models\aktivite::find($id);
        return view('panel.aktivite_edit', [
            'aktivite' => $aktivite
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate($this->validationrules,$this->validationMessages);
        $aktivite = \App\Models\aktivite::findOrFail($id);
        $aktivite->update([
            'title' => $data['title'],
            'done' => $data['done'],
        ]);
        if(isset($data['image'])){
            foreach ($data['image'] as $key => $image) {
                $imagePath = $image->store('aktiviteler','public');
                \App\Models\aktivite_image::create([
                        'img_path' => $imagePath,
                        'aktivite_id' => $aktivite->id
                ]);
            }
        }
        if(isset($data['imageDestroy'])){
            foreach ($data['imageDestroy'] as $key => $imageId) {
                $imageToDestroy = \App\Models\aktivite_image::findOrFail($imageId);
                try {
                    $deletePath = public_path('/storage/'.$imageToDestroy->img_path);
                    unlink($deletePath);
                    $imageToDestroy->delete();
                } catch (\Throwable $th) {
                    //throw $th;
                    return redirect()->back()->with('status' , 'Görsel Kaldırılamadı.');

                }
            }
        }

        return redirect()->back()->with('status' , 'Aktivite başarı ile güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        foreach ($request->aktivite_id as $aktivite_id) {
            $aktivite = \App\Models\aktivite::findOrFail($aktivite_id);
            foreach ($aktivite->aktivite_images as $key => $aktivite_image) {
                $imageToDestroy = $aktivite_image->img_path;
                try {
                    $deletePath = public_path('/storage/'.$imageToDestroy);
                    unlink($deletePath);
                    $aktivite_image->delete();
                } catch (\Throwable $th) {
                    //throw $th;
                    return redirect()->back()->with('status' , 'Görsel Kaldırılamadı.');

                }
            }
            $aktivite->delete();
            
        }
        return redirect()->back()->with('status' , 'Aktivite/ler başarı ile kaldırıldı.');
    }
}
