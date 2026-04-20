<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseReportExport;
use App\Exports\ItemPurchaseExport;
use App\Exports\PurchasePaymentExport;
use App\Models\Brand;
use App\Models\Product;

class PurchaseReportController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function purchase()
    {
        $data = $this->purchaseService->master_parms(auth()->id());
        return view('backend.reports.purchase.purchase', $data);
    }


    public function purchaseReport(Request $request)
    {
        $validator = $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'supplier_id' => 'nullable|integer',
        ]);

        $isExport = $request->action === 'export';

        $data = $this->purchaseService->reportPurchaseData($validator, $isExport);

        if ($isExport) {
            return Excel::download(
                new PurchaseReportExport($data['items']),
                'purchase-report.xlsx'
            );
        }

        $data['suppliers'] = Customer::where('type', 'supplier')
            ->where('user_id', auth()->id())
            ->get();

        return view('backend.reports.purchase.purchase', $data);
    }

    public function itemPurchase()
    {
        $data = $this->purchaseService->master_parms(auth()->id());
        return view('backend.reports.purchase.item-purchase', $data);
    }


    public function itemPurchaseReport(Request $request)
    {
        $validated = $request->validate([
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
            'supplier_id' => 'nullable',
            'product_id'  => 'nullable',
            'brand_id'    => 'nullable',
        ]);

        $isExport = $request->action === 'export';

        $data = $this->purchaseService->itemPurchaseReportData($validated);

        if ($isExport) {
            return Excel::download(
                new ItemPurchaseExport($data['items']),
                'item-purchase-report.xlsx'
            );
        }

        // dropdown data
        $data['suppliers'] = Customer::where('type', 'supplier')->where('user_id', auth()->id())->get();
        $data['products'] = Product::where('is_active', 1)->where('user_id', auth()->id())->get();
        $data['brands']    = Brand::where('is_active', 1)->where('user_id', auth()->id())->get();

        return view('backend.reports.purchase.item-purchase', $data);
    }

    public function purchasePayment()
    {
        $data = $this->purchaseService->master_parms(auth()->id());
        return view('backend.reports.purchase.payment-purchase', $data);
    }


    public function purchasePaymentReport(Request $request)
    {
        $validated = $request->validate([
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'supplier_id'   => 'nullable',
            'payment_type'  => 'nullable',
        ]);

        $isExport = $request->action === 'export';

        $data = $this->purchaseService->purchasePaymentData($validated);

        if ($isExport) {
            return Excel::download(
                new PurchasePaymentExport($data['items']),
                'purchase-payment.xlsx'
            );
        }

        // dropdown data
        $data['suppliers'] = Customer::where('type', 'supplier')->where('user_id', auth()->id())->get();
        return view('backend.reports.purchase.payment-purchase', $data);
    }
}
