@extends('layouts.app') 

@section('content')
<div class="container">
    <h2>Liên hệ với chúng tôi</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Họ và tên</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nội dung</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi liên hệ</button>
    </form>

    <hr>

    <h4>Địa chỉ của chúng tôi</h4>
    <p>Tòa cao đăng FPT Polytechnic</p>

    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5005.76929523086!2d105.74808022113872!3d21.037996512997534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e1!3m2!1svi!2s!4v1744481136656!5m2!1svi!2s" 
    width="100%" 
    height="500" 
    style="border:0; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.15);" 
    allowfullscreen="" 
    loading="lazy" 
    referrerpolicy="no-referrer-when-downgrade">
     </iframe>
</div>
@endsection
