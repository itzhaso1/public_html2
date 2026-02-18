@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $pageTitle }}</h3>
            <div class="card-toolbar">
                <a href="{{ route('admin.cash_exchange.requests.index') }}" class="btn btn-sm btn-light">رجوع</a>
                <form method="POST" action="{{ route('admin.cash_exchange.requests.destroy', $req) }}" class="d-inline-block ms-2">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirm" value="DELETE">
                    <button type="button" class="btn btn-sm btn-danger"
                            onclick="const v=prompt('اكتب DELETE لتأكيد حذف هذا الطلب'); if(v==='DELETE'){ this.form.submit(); }">
                        حذف
                    </button>
                </form>
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
                        <div class="text-muted mt-3">الفئة</div>
                        <div class="fw-bold">{{ $req->offer?->name ?? '-' }}</div>
                        <div class="text-muted mt-3">الكاش</div>
                        <div class="fw-bold text-success">{{ $req->cash_value }} {{ $req->currency }}</div>
                        <div class="text-muted mt-3">الحالة</div>
                        <div>
                            @if($req->status === 'completed')
                                <span class="badge badge-light-success">مكتمل</span>
                            @elseif($req->status === 'rejected')
                                <span class="badge badge-light-danger">مرفوض</span>
                            @else
                                <span class="badge badge-light-warning">قيد المراجعة</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 border rounded">
                        <div class="text-muted">كود البطاقة</div>
                        <div class="fw-bold" style="word-break: break-all">{{ $req->card_code }}</div>

                        <hr>

                        <div class="text-muted">بيانات البنك</div>
                        <div class="mt-2"><b>اسم البنك:</b> {{ $req->bank_name }}</div>
                        <div class="mt-2"><b>اسم صاحب الحساب:</b> {{ $req->account_name }}</div>
                        @if($req->account_number)
                            <div class="mt-2"><b>رقم الحساب:</b> {{ $req->account_number }}</div>
                        @endif
                        @if($req->iban)
                            <div class="mt-2"><b>IBAN:</b> {{ $req->iban }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5 p-4 border rounded">
                <form method="POST" action="{{ route('admin.cash_exchange.requests.note', $req) }}" class="mb-3">
                    @csrf
                    <label class="form-label fw-bold">ملاحظة أدمن (اختياري)</label>
                    <textarea class="form-control" name="admin_note" rows="3" placeholder="اكتب ملاحظة داخلية...">{{ old('admin_note', $req->admin_note) }}</textarea>
                    <button class="btn btn-sm btn-primary mt-3">حفظ الملاحظة</button>
                </form>

                <div class="row g-3">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.cash_exchange.requests.complete', $req) }}"
                              onsubmit="return confirm('تأكيد تغيير الحالة إلى مكتمل؟');">
                            @csrf
                            <input type="hidden" name="admin_note" value="{{ $req->admin_note }}">
                            <button class="btn btn-sm btn-success" {{ ($req->status ?? 'pending') !== 'pending' ? 'disabled' : '' }}>
                                تغيير الحالة إلى مكتمل
                            </button>
                            @if(($req->status ?? '') !== 'pending')
                                <div class="text-muted mt-2">لا يمكن تنفيذ الإجراء إلا مرة واحدة عندما يكون الطلب قيد المراجعة.</div>
                            @endif
                        </form>
                    </div>

                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.cash_exchange.requests.reject', $req) }}"
                              onsubmit="return confirm('تأكيد رفض الطلب؟');">
                            @csrf
                            <label class="form-label fw-bold">سبب الرفض (مطلوب)</label>
                            <input type="text" name="admin_note" id="cashRejectNote" class="form-control mb-2"
                                   placeholder="مثال: البطاقة مستخدمة أو خاطئة"
                                   value="{{ old('admin_note') }}"
                                   {{ ($req->status ?? 'pending') !== 'pending' ? 'disabled' : '' }}>

                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-light"
                                        onclick="const i=document.getElementById('cashRejectNote'); if(i){ i.value='البطاقة مستخدمة'; i.focus(); }"
                                        {{ ($req->status ?? 'pending') !== 'pending' ? 'disabled' : '' }}>
                                    البطاقة مستخدمة
                                </button>
                                <button type="button" class="btn btn-sm btn-light"
                                        onclick="const i=document.getElementById('cashRejectNote'); if(i){ i.value='البطاقة خاطئة'; i.focus(); }"
                                        {{ ($req->status ?? 'pending') !== 'pending' ? 'disabled' : '' }}>
                                    البطاقة خاطئة
                                </button>
                            </div>

                            <button class="btn btn-sm btn-danger" {{ ($req->status ?? 'pending') !== 'pending' ? 'disabled' : '' }}>
                                رفض الطلب
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

