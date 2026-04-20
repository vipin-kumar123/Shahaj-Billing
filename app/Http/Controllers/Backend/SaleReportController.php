<?php

namespace App\Http\Controllers\Backend;

use App\Exports\ItemReportExport;
use App\Exports\SalePaymentExport;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sales;
use Illuminate\Http\Request;
use App\Services\SaleService;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\Shared\Validate;

class SaleReportController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function sales()
    {
        $data = $this->saleService->parms(auth()->id());
        return view('backend.reports.sale.sale-report', $data);
    }

    public function salesReport(Request $request)
    {
        $validator = $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'customer'  => 'nullable|array',
            'customer.*' => 'exists:customers,id'
        ]);

        $data = $this->saleService->saleReportData($validator);

        if ($request->action === 'export') {
            return Excel::download(
                new SalesExport($data['sales']),
                'sales-report.xlsx'
            );
        }

        $data['customers'] = Customer::select('id', 'first_name', 'last_name')->where('user_id', auth()->id())->where('type', 'customer')->get();

        return view('backend.reports.sale.sale-report', $data);
    }

    public function itemSales()
    {
        $data = $this->saleService->parms(auth()->id());
        return view('backend.reports.sale.item-sale', $data);
    }


    public function itemReport(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'customer'  => 'nullable|integer',
            'item_id'   => 'nullable|integer',
            'brand_id'  => 'nullable|integer',
        ]);

        $isExport = $request->action === 'export';

        $data = $this->saleService->itemReportData($validated, $isExport);

        if ($isExport) {
            return Excel::download(
                new ItemReportExport($data['items']),
                'item-report.xlsx'
            );
        }

        $data['customers'] = Customer::where('type', 'customer')->where('user_id', auth()->id())->get();
        $data['products']  = Product::where('user_id', auth()->id())->get();
        $data['brands']    = Brand::where('user_id', auth()->id())->get();

        return view('backend.reports.sale.item-sale', $data);
    }



    public function paymentReport()
    {
        return view('backend.reports.sale.payment-report');
    }

    public function salePaymentReport(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'customer'  => 'nullable|integer',
            'payment_type' => 'nullable|string',
        ]);

        $isExport = $request->action === 'export';

        $data = $this->saleService->salePaymentReportData($validated, $isExport);

        if ($isExport) {
            return Excel::download(
                new SalePaymentExport($data['items']),
                'sale-payment-report.xlsx'
            );
        }

        $data['customers'] = Customer::where('type', 'customer')->where('user_id', auth()->id())->get();

        return view('backend.reports.sale.payment-report', $data);
    }
}
