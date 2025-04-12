<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function showForm() {
        return view('users.contact.contact');
    }

    public function submitForm(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $data = $request->only('name', 'email', 'message');

        Mail::to('nguyensang012456@gmail.com')->send(new ContactMail($data));

        return back()->with('success', 'Cảm ơn bạn đã liên hệ với chúng tôi!');
    }
}
