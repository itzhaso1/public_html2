<?php

namespace App\Http\Controllers\Website\Customer;

use App\Http\Controllers\Controller;
use App\Models\CashExchangeRequest;
use App\Models\ManualPaymentRequest;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function index(Request $request)
    {
        $manualRequests = ManualPaymentRequest::query()
            ->where('user_id', auth()->id())
            ->with(['product', 'diamondCode'])
            ->latest()
            ->paginate(20);

        $cashRequests = CashExchangeRequest::query()
            ->where('user_id', auth()->id())
            ->with(['offer'])
            ->latest()
            ->get();

        return view('website.customer.purchases', [
            'pageTitle' => 'مشترياتي',
            'requests' => $manualRequests,
            'cashRequests' => $cashRequests,
        ]);
    }
}

