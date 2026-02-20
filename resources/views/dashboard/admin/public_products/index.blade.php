@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="mb-5 card card-xxl-stretch mb-xl-8">
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{ $pageTitle }}</span>
                <span class="mt-1 text-muted fw-bold fs-7">طلبات نشر الحسابات القادمة من صفحة النشر الخارجي</span>
            </h3>

            <div class="card-toolbar d-flex gap-2">
                <a class="btn btn-sm {{ ($status ?? 'pending') === 'pending' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.public_products.index', ['status' => 'pending']) }}">قيد المراجعة</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'approved' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.public_products.index', ['status' => 'approved']) }}">منشور</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'rejected' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.public_products.index', ['status' => 'rejected']) }}">مرفوض</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'all' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.public_products.index', ['status' => 'all']) }}">الكل</a>
            </div>
        </div>

        <div class="py-3 card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>#</th>
                        <th>الاسم</th>
                        <th>السعر</th>
                        <th>رقم الزبون</th>
                        <th>الحالة</th>
                        <th>تاريخ</th>
                        <th>فتح</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($requests as $p)
                        @php
                            $label = match((string) ($p->status ?? 'draft')) {
                                'published' => ['منشور', 'badge-light-success'],
                                'archived' => ['مرفوض', 'badge-light-danger'],
                                default => ['قيد المراجعة', 'badge-light-warning'],
                            };
                        @endphp
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td class="fw-bold">{{ $p->name ?? '—' }}</td>
                            <td class="fw-bold text-success">{{ number_format((float) ($p->price ?? 0), 2) }} ر.س</td>
                            <td class="font-monospace">{{ $p->client_number ?? '—' }}</td>
                            <td><span class="badge {{ $label[1] }}">{{ $label[0] }}</span></td>
                            <td>{{ $p->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <a class="btn btn-sm btn-light btn-active-primary" href="{{ route('admin.public_products.show', $p) }}">تفاصيل</a>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.public_products.destroy', $p) }}" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="confirm" value="DELETE">
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="const v=prompt('اكتب DELETE لتأكيد حذف هذا الطلب'); if(v==='DELETE'){ this.form.submit(); }">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">لا توجد طلبات.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection

