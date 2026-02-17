@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="mb-5 card card-xxl-stretch mb-xl-8">
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{ $pageTitle }}</span>
                <span class="mt-1 text-muted fw-bold fs-7">التحكم بفئات الرصيد وقيمة التحويل</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('admin.cash_exchange.offers.create') }}" class="btn btn-sm btn-light btn-active-primary">
                    إضافة فئة
                </a>
            </div>
        </div>

        <div class="py-3 card-body">
            <div class="table-responsive">
                <table class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الفئة</th>
                        <th>الكاش</th>
                        <th>العملة</th>
                        <th>مفعل</th>
                        <th>ترتيب</th>
                        <th>إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($offers as $o)
                        <tr>
                            <td>{{ $o->id }}</td>
                            <td class="fw-bold">{{ $o->name }}</td>
                            <td>{{ $o->face_value }}</td>
                            <td class="fw-bold text-success">{{ $o->cash_value }}</td>
                            <td>{{ $o->currency }}</td>
                            <td>
                                @if($o->enabled)
                                    <span class="badge badge-light-success">نعم</span>
                                @else
                                    <span class="badge badge-light-danger">لا</span>
                                @endif
                            </td>
                            <td>{{ $o->sort_order }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-light btn-active-primary" href="{{ route('admin.cash_exchange.offers.edit', $o) }}">تعديل</a>
                                <form method="POST" action="{{ route('admin.cash_exchange.offers.destroy', $o) }}" onsubmit="return confirm('تأكيد حذف الفئة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light btn-active-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">لا يوجد فئات بعد.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $offers->links() }}
            </div>
        </div>
    </div>
@endsection

