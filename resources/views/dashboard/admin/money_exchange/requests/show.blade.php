@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $pageTitle }}</h3>
            <div class="card-toolbar">
                <a href="{{ route('admin.money_exchange.requests.index') }}" class="btn btn-sm btn-light">رجوع</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-4 border rounded">
                        <div class="text-muted">الرقم</div>
                        <div class="fw-bold">{{ $req->reference }}</div>
                        <div class="text-muted mt-3">المستخدم</div>
                        <div class="fw-bold">{{ $req->user?->email ?? '-' }}</div>
                        <div class="text-muted mt-3">الاتجاه</div>
                        <div class="fw-bold">{{ $req->direction === 'usdt_to_sar' ? 'USDT → SAR' : 'SAR → USDT' }}</div>
                        <div class="text-muted mt-3">من</div>
                        <div class="fw-bold">{{ $req->amount_from }}</div>
                        <div class="text-muted mt-3">إلى</div>
                        <div class="fw-bold text-success">{{ $req->amount_to }}</div>
                        <div class="text-muted mt-3">الحالة</div>
                        <div>
                            @if($req->status === 'completed')
                                <span class="badge badge-light-success">مكتمل</span>
                            @elseif($req->status === 'rejected')
                                <span class="badge badge-light-danger">مرفوض</span>
                            @else
                                <span class="badge badge-light-warning">معلق</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 border rounded">
                        <div class="fw-bold mb-3">بيانات الاستلام</div>
                        @if($req->direction === 'sar_to_usdt')
                            <div><b>نوع:</b> {{ $req->destination_type }}</div>
                            <div class="mt-2" style="word-break: break-all"><b>القيمة:</b> {{ $req->destination_value }}</div>
                        @else
                            <div><b>اسم البنك:</b> {{ $req->bank_name }}</div>
                            <div class="mt-2"><b>اسم صاحب الحساب:</b> {{ $req->account_name }}</div>
                            @if($req->account_number)
                                <div class="mt-2"><b>رقم الحساب:</b> {{ $req->account_number }}</div>
                            @endif
                            @if($req->iban)
                                <div class="mt-2"><b>IBAN:</b> {{ $req->iban }}</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5 p-4 border rounded">
                <form method="POST" action="{{ route('admin.money_exchange.requests.complete', $req) }}" class="d-inline-block me-2"
                      onsubmit="return confirm('تأكيد تغيير الحالة إلى مكتمل؟');">
                    @csrf
                    <input type="text" name="admin_note" class="form-control mb-2" placeholder="ملاحظة (اختياري)" value="{{ old('admin_note', $req->admin_note) }}">
                    <button class="btn btn-success btn-sm" {{ $req->status === 'completed' ? 'disabled' : '' }}>مكتمل</button>
                </form>

                <form method="POST" action="{{ route('admin.money_exchange.requests.reject', $req) }}" class="d-inline-block"
                      onsubmit="return confirm('تأكيد رفض الطلب؟');">
                    @csrf
                    <input type="text" name="admin_note" class="form-control mb-2" placeholder="سبب الرفض (مطلوب)" value="{{ old('admin_note') }}">
                    <button class="btn btn-danger btn-sm" {{ $req->status === 'rejected' ? 'disabled' : '' }}>رفض</button>
                </form>
            </div>
        </div>
    </div>
@endsection

