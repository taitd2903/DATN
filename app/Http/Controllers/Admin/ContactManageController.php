<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactManageController extends Controller
{
    public function index(Request $request)
{
    $query = Contact::query();

    
    if ($request->filled('name')) {
        $query->where('name', 'LIKE', '%' . $request->name . '%');
    }

    
    if ($request->filled('email')) {
        $query->where('email', 'LIKE', '%' . $request->email . '%');
    }

    if ($request->has('status') && $request->status !== '') {
        $query->where('status', $request->status);
    }

    $contacts = $query->latest()->paginate(10);

    return view('admin.contact.index', compact('contacts'));
}


    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        
        return view('admin.contact.show', compact('contact'));
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return redirect()->back()->with('success', 'Đã xoá liên hệ thành công!');
    }
    public function updateStatus(Request $request, $id)
{
    $contact = Contact::findOrFail($id);
    $contact->status = $request->status;
    $contact->save();

    return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
}
public function store(Request $request)
{
    $contact = new Contact();
    $contact->name = $request->name;
    $contact->email = $request->email;
    $contact->message = $request->message;
    $contact->status = 0; 
    $contact->save();

    
    Mail::to($contact->email)->send(new ContactConfirmation($contact));

    return redirect()->back()->with('success', 'Liên hệ của bạn đã được gửi. Vui lòng kiểm tra email xác nhận.');
}
}
