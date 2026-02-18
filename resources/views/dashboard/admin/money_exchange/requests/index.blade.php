@extends('dashboard.layouts.master')

@section('pageTitle')
    {{ $pageTitle }}
@endsection

@section('content')
    <div class="mb-5 card card-xxl-stretch mb-xl-8">
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{ $pageTitle }}</span>
            </h3>
            <div class="card-toolbar d-flex gap-2">
                <a class="btn btn-sm {{ ($status ?? 'pending') === 'pending' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.money_exchange.requests.index', ['status' => 'pending']) }}">معلق</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'completed' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.money_exchange.requests.index', ['status' => 'completed']) }}">مكتمل</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'rejected' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.money_exchange.requests.index', ['status' => 'rejected']) }}">مرفوض</a>
                <a class="btn btn-sm {{ ($status ?? '') === 'all' ? 'btn-primary' : 'btn-light' }}"
                   href="{{ route('admin.money_exchange.requests.index', ['status' => 'all']) }}">الكل</a>
            </div>
        </div>

        <div class="py-3 card-body">
            <div class="table-responsive">
                <table class="table table-striped table-row-bordered gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>#</th>
                        <th>الرقم</th>
                        <th>المستخدم</th>
                        <th>الاتجاه</th>
                        <th>من</th>
                        <th>إلى</th>
                        <th>الحالة</th>
                        <th>فتح</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($requests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td class="fw-bold">{{ $r->reference }}</td>
                            <td>{{ $r->user?->email ?? '-' }}</td>
                            <td>{{ $r->direction === 'usdt_to_sar' ? 'USDT → SAR' : 'SAR → USDT' }}</td>
                            <td>{{ $r->amount_from }}</td>
                            <td>{{ $r->amount_to }}</td>
                            <td>
                                @if($r->status === 'completed')
                                    <span class="badge badge-light-success">مكتمل</span>
                                @elseif($r->status === 'rejected')
                                    <span class="badge badge-light-danger">مرفوض</span>
                                @else
                                    <span class="badge badge-light-warning">معلق</span>
                                @endif
                            </td>
                            <td><a class="btn btn-sm btn-light btn-active-primary" href="{{ route('admin.money_exchange.requests.show', $r) }}">تفاصيل</a></td>
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

