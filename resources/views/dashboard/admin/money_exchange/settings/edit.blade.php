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
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.money_exchange.settings.update') }}" class="row g-4">
                @csrf

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled" {{ old('enabled', $settings->enabled) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="enabled">تفعيل الخدمة</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">سعر SAR لكل 1 USDT (ريال → USDT)</label>
                    <input type="number" step="0.0001" name="sar_per_usdt" value="{{ old('sar_per_usdt', $settings->sar_per_usdt) }}"
                           class="form-control" required>
                    <div class="form-text">مثال: 4.0000 (يعني كل 4 ريال = 1 USDT)</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">نسبة الربح % (تُحسب تلقائيًا)</label>
                    <input type="number" step="0.01" name="profit_percent" value="{{ old('profit_percent', $settings->profit_percent) }}"
                           class="form-control" required>
                    <div class="form-text">مثال: 6.25% يعطي USDT→SAR = 3.75 إذا كان SAR/USDT=4</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">سعر USDT → SAR (محسوب)</label>
                    @php
                        $sarPer = (float) (old('sar_per_usdt', $settings->sar_per_usdt) ?: 0);
                        $profit = (float) (old('profit_percent', $settings->profit_percent) ?: 0);
                        $computed = $sarPer > 0 ? round($sarPer * (1 - ($profit / 100)), 4) : null;
                    @endphp
                    <input type="text" class="form-control" value="{{ $computed ?? ($settings->usdt_to_sar_rate ?? '') }}" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Min SAR</label>
                    <input type="number" step="0.01" name="min_sar" value="{{ old('min_sar', $settings->min_sar) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Max SAR</label>
                    <input type="number" step="0.01" name="max_sar" value="{{ old('max_sar', $settings->max_sar) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Min USDT</label>
                    <input type="number" step="0.0001" name="min_usdt" value="{{ old('min_usdt', $settings->min_usdt) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Max USDT</label>
                    <input type="number" step="0.0001" name="max_usdt" value="{{ old('max_usdt', $settings->max_usdt) }}" class="form-control">
                </div>

                <div class="col-12">
                    <hr class="my-2">
                    <div class="fw-bold">معلومات التحويل للعميل</div>
                    <div class="form-text">هذه البيانات ستظهر للعميل في صفحة "تحويل الأموال" حسب نوع التحويل.</div>
                </div>

                <div class="col-12">
                    <div class="fw-bold mb-2">1) العميل يرسل SAR (ريال → USDT)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">اسم البنك</label>
                    <input type="text" name="receive_sar_bank_name" value="{{ old('receive_sar_bank_name', $settings->receive_sar_bank_name) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">اسم صاحب الحساب</label>
                    <input type="text" name="receive_sar_account_name" value="{{ old('receive_sar_account_name', $settings->receive_sar_account_name) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">رقم الحساب</label>
                    <input type="text" name="receive_sar_account_number" value="{{ old('receive_sar_account_number', $settings->receive_sar_account_number) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">IBAN</label>
                    <input type="text" name="receive_sar_iban" value="{{ old('receive_sar_iban', $settings->receive_sar_iban) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">ملاحظة (اختياري)</label>
                    <textarea name="receive_sar_note" class="form-control" rows="2">{{ old('receive_sar_note', $settings->receive_sar_note) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="fw-bold mb-2">2) العميل يرسل USDT (USDT → ريال)</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">عنوان TRC20 (USDT)</label>
                    <input type="text" name="receive_usdt_trc20_address" value="{{ old('receive_usdt_trc20_address', $settings->receive_usdt_trc20_address) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Binance ID (اختياري)</label>
                    <input type="text" name="receive_usdt_binance_id" value="{{ old('receive_usdt_binance_id', $settings->receive_usdt_binance_id) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">ملاحظة (اختياري)</label>
                    <textarea name="receive_usdt_note" class="form-control" rows="2">{{ old('receive_usdt_note', $settings->receive_usdt_note) }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">حفظ</button>
                </div>
            </form>

            <hr class="my-4">

            <div class="alert alert-info mb-0">
                <div class="fw-bold mb-1">ملاحظة</div>
                <div>معلومات التحويل (حساب البنك / عنوان TRC20) تُعرض للعميل داخل صفحة "تحويل الأموال" حسب نوع التحويل.</div>
            </div>
        </div>
    </div>
@endsection

