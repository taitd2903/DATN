{{-- @extends('layouts.app')

@section('content') --}}
<div class="container">
    <div class="header clearfix">
        <h3 class="text-muted">VNPAY DEMO</h3>
    </div>

    <div class="mt-3">
        <h3>Tra cứu giao dịch</h3>
    </div>

    <div class="border-bottom pb-3">
        <form action="{{ route('vnpay.query') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="txnRef">Mã giao dịch thanh toán (vnp_TxnRef):</label>
                <input id="txnRef" class="form-control" name="txnRef" type="text" required />
            </div>

            <div class="form-group">
                <label for="transactionDate">Thời gian khởi tạo giao dịch (vnp_TransactionDate):</label>
                <input id="transactionDate" class="form-control" name="transactionDate" type="datetime-local" required />
            </div>

            <button type="submit" class="btn btn-primary">Tra cứu giao dịch</button>
        </form>
    </div>

    @if(session('response'))
    <div class="alert alert-info mt-3">
        <strong>Phản hồi từ API:</strong>
        <pre>{{ session('response') }}</pre>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
{{-- @endsection --}}
