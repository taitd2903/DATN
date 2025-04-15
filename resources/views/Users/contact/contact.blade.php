@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div id="alert-success" class="alert alert-success position-fixed fade show"
                style="top: 20px; right: 20px; z-index: 1050; max-width: 300px;">
                {{ session('success') }}
            </div>
        @endif
    </div>
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Thông tin liên hệ</span>
                            <h2>Liên hệ với Oceansport</h2>
                            <p>
                                Oceansport luôn sẵn sàng hỗ trợ bạn trong hành trình chinh phục đam mê thể thao.
                                Đừng ngần ngại liên hệ với chúng tôi nếu bạn có bất kỳ thắc mắc nào về sản phẩm, đơn hàng
                                hoặc chương trình khuyến mãi.
                            </p>
                        </div>
                        <h5 class="fw-semibold mt-4">Chi nhánh Hà Nội</h5>
                        <p>13 Trịnh Văn Bô, Phường Canh, Hà Nội<br>
                            Hotline: <strong>0866043950</strong></p>

                        <h5 class="fw-semibold mt-4">Email & Hỗ trợ</h5>
                        <p>
                            Email: <a href="mailto:oceansport@oceansport.vn">oceansport@oceansport.vn</a><br>
                            Thời gian làm việc: 8:00 - 22:00 (T2 - CN)
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="contact__form">
                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Tên">
                                    @error('name')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email">
                                    @error('email')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-lg-12">
                                    <textarea name="message" class="form-control" value="{{ old('message') }}"
                                        placeholder="Nội dung"></textarea>
                                    @error('message')
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="btn btn-primary">Gửi liên hệ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Liên hệ</h4>
                        <div class="breadcrumb__links">
                            <a href={{ url('/') }}>Trang Chủ</a>
                            <span>Liên hệ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <div class="map ">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d475751.27447118296!2d105.31016925896422!3d21.318647831431896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2s!4v1744562942644!5m2!1svi!2s"
            height="500" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
    <!-- Contact Section Begin -->
   
    <!-- Contact Section End -->
@endsection
<script>
    setTimeout(() => {
        const alert = document.getElementById('alert-success');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500); // Xóa hẳn khỏi DOM sau animation
        }
    }, 3000); // 3 giây
</script>