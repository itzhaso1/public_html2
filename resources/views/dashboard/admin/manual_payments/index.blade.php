<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'طلبات الدفع اليدوي' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<main class="max-w-7xl mx-auto p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold">{{ $pageTitle ?? 'طلبات الدفع اليدوي' }}</h1>
            <p class="text-sm text-gray-600 mt-1">مراجعة الإيصالات والموافقة/الرفض.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center justify-center rounded-xl bg-black px-4 py-2 text-sm font-bold text-white hover:bg-gray-800 transition">
            العودة للوحة التحكم
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5 overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
            <tr class="text-right">
                <th class="p-3 font-extrabold">المرجع</th>
                <th class="p-3 font-extrabold">الباقة</th>
                <th class="p-3 font-extrabold">Player ID</th>
                <th class="p-3 font-extrabold">المبلغ</th>
                <th class="p-3 font-extrabold">طريقة الدفع</th>
                <th class="p-3 font-extrabold">الحالة</th>
                <th class="p-3 font-extrabold">التاريخ</th>
                <th class="p-3 font-extrabold">إجراء</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($requests as $mpr)
                <tr class="text-right">
                    <td class="p-3 font-mono text-xs select-all">{{ $mpr->reference }}</td>
                    <td class="p-3 font-bold">{{ $mpr->product?->name ?? '-' }}</td>
                    <td class="p-3">{{ $mpr->player_id }}</td>
                    <td class="p-3 font-extrabold text-green-700">ر.س {{ number_format((float)$mpr->amount, 2) }}</td>
                    <td class="p-3">{{ $mpr->payment_method ?? '-' }}</td>
                    <td class="p-3">
                        @php
                            $badge = match($mpr->status) {
                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            };
                            $label = match($mpr->status) {
                                'approved' => 'مقبول',
                                'rejected' => 'مرفوض',
                                default => 'قيد المراجعة',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="p-3 text-gray-600">{{ $mpr->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.manual_payments.show', $mpr) }}"
                           class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-bold hover:bg-gray-50">
                            فتح
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td class="p-6 text-center text-gray-500" colspan="8">لا توجد طلبات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</main>
</body>
</html>

