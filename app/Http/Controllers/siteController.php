<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class siteController extends Controller
{
    public function home(){
        $settings = \App\Models\setting::all();
        return view('home',[
            'settings' => $settings
        ]);
    }
    public function kurumsal(){
        return view('kurumsal');
    }
    public function aktiviteler(){
        return view('aktiviteler');
    }
    public function galeri(){
        return view('galeri');
    }
    public function iletisim(){
        return view('iletisim');
    }
    public function sendRegistrationForm(){
        $data = request()->validate([
            'studentName' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'parentName' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        try {
            Mail::to('0401cf@gmail.com')->send(new \App\Mail\RegisterForm($data)); // mail adresi değiştir
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('anasayfa')->with('status_bad','Form gönderilirken bir hata ile karşılaşıldı.');
        }
        return redirect()->route('anasayfa')->with('status_good', 'Form başarı ile gönderildi.');
    }
    public function sendContactForm(){
        $data = request()->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        try {
            Mail::to('0401cf@gmail.com')->send(new \App\Mail\ContactForm($data)); // mail adresi değiştir
            $msg = "İletişim formunuz başarıyla elimize ulaştı. Size en kısa sürede dönüş sağlayacağız. ";
            $title = "Başarılı";
            $type = "success";
        } catch (\Throwable $th) {
            $msg = "Mesajınızı iletirken bir sorunla karşılaştık. Lütfen iletişim sayfasındaki bilgilerle bizimle iletişime geçiniz.";
            $title = "Üzgünüz!";  
            $type = "danger";
        }
        $response = array(
            'message' => $msg,
            'title' => $title,
            'type' => $type
        );
        return json_encode($response);
    }

}
