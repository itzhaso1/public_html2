@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $pageTitle }}</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.cash_exchange.offers.store') }}" class="row g-4">
                @csrf
                <div class="col-md-6">
                    <label class="form-label fw-bold">الاسم</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="مثال: سوا 20" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">فئة الرصيد</label>
                    <input type="number" name="face_value" value="{{ old('face_value') }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">الكاش (للعميل)</label>
                    <input type="number" step="0.01" name="cash_value" value="{{ old('cash_value') }}" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">العملة</label>
                    <input type="text" name="currency" value="{{ old('currency', 'SAR') }}" class="form-control" maxlength="3" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                </div>
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled" {{ old('enabled', 1) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="enabled">مفعل</label>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.cash_exchange.offers.index') }}" class="btn btn-light">رجوع</a>
                </div>
            </form>
        </div>
    </div>
@endsection

