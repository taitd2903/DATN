<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function showForm() {
        return view('users.contact.contact');
    }

    public function submitForm(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'Vui lòng nhập tên của bạn.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.string' => 'Nội dung phải là chuỗi ký tự.',
            'message.min' => 'Nội dung phải có ít nhất 10 ký tự.',
        ]);
        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        Mail::to('nguyensang012456@gmail.com')->send(new ContactMail($request->all()));

        return back()->with('success', 'Cảm ơn bạn đã liên hệ với chúng tôi!');
    }
}
