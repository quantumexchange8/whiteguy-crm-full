<?php

namespace App\Http\Controllers;

use App\Exports\PaymentMethodExport;
use App\Http\Requests\PaymentMethodRequest;
use App\Models\ContentType;
use App\Models\PaymentMethod;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the flashed messages from the session
        $errors = $request->session()->get('errors');
        $errorMsg = $request->session()->get('errorMsg');

        // Clear the flashed messages from the session
        $request->session()->forget('errors');
        $request->session()->forget('errorMsg');
        $request->session()->save();

        if (isset($errorMsg)) {
            return Inertia::render('CRM/PaymentMethods/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }

        return Inertia::render('CRM/PaymentMethods/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/PaymentMethods/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request)
    {
        $data = $request->all();
        $fileName = '';
        
        if ($request->hasFile('logo')) {
            $fileName = $request->file('logo')->getClientOriginalName();
        }

        $newPaymentMethod = PaymentMethod::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'bank_name_label' => 'Cryptocurrency',
            'bank_name' => $data['bank_name'],
            'account_name_label' => 'Network',
            'account_name' => $data['account_name'],
            'account_number_label' => 'Wallet Address',
            'account_number' => $data['account_number'],
            'logo' => 'payment_methods/' . Carbon::now()->format('Y/m/d') . '/' . $fileName,
            'currency' => 'CRYPTO',
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
        ]);
        $newPaymentMethod->save();

        /* Need modifications on saving approach */
        if (isset($data['sites']) && count($data['sites']) > 0) {
            foreach ($data['sites'] as $key => $value) {
                $newPaymentMethod->sites()->attach($value['site_id'], [
                    'edited_at' => $value['edited_at'],
                    'created_at' => $value['created_at'],
                ]);
            }
        }

        $errorMsgTitle = "You have successfully created a new payment method.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('payment-methods.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = PaymentMethod::find($id);

        $sites = DB::table('core_sitepaymentmethod')
                        ->where('payment_method_id', $id)
                        ->select(['id', 'site_id', 'edited_at', 'created_at'])
                        ->get();

        return Inertia::render('CRM/PaymentMethods/Edit', [
            'data' => $data,
            'sites' => $sites
        ]);     
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        // dd($request);
        $fileName = '';
        
        if (isset($data['logo'])) {
            if ($request->hasFile('logo')) {
                $fileName = 'payment_methods/' . Carbon::now()->format('Y/m/d') . '/' . $request->file('logo')->getClientOriginalName();
            } else {
                $fileName = $data['logo'];
            }
        }

        $newPaymentMethod = PaymentMethod::find($id);
        
        $newPaymentMethod->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'bank_name_label' => 'Cryptocurrency',
            'bank_name' => $data['bank_name'],
            'account_name_label' => 'Network',
            'account_name' => $data['account_name'],
            'account_number_label' => 'Wallet Address',
            'account_number' => $data['account_number'],
            'logo' => $fileName,
            'currency' => 'CRYPTO',
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
        ]);
        $newPaymentMethod->save();

        /* Need modifications on saving approach */
        if (isset($data['sites']) && count($data['sites']) > 0) {
            // $newPaymentMethod->sites()->detach();
            foreach ($data['sites'] as $key => $value) {
                if (!$newPaymentMethod->sites()->where('site_id', $value['site_id'])->exists()) {
                    $newPaymentMethod->sites()->attach($value['site_id'], [
                        'edited_at' => $value['edited_at'],
                        'created_at' => $value['created_at'],
                    ]);
                } else {
                    $newPaymentMethod->sites()->updateExistingPivot($value['site_id'], [
                        'edited_at' => $value['edited_at'],
                    ]);
                }
            }
        }

        $errorMsgTitle = "You have successfully updated the payment method.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('payment-methods.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
    /**
     * Delete site payment method.
     */
    public function deleteSitePaymentMethod(Request $request, string $id)
    {
        // dd($request, $id, $request->sites);
        $existingSitePaymentMethod = PaymentMethod::find($request->id);
        $existingSitePaymentMethod->sites()->detach($request->sites[0]['site_id']);

        return redirect(route('payment-methods.edit', $request->id));
    }

    public function getPaymentMethods(Request $request)
    {   
        $tableColumns = Schema::getColumnListing('core_paymentmethod');
        $sort_column = '';

        $queries = PaymentMethod::query();
        $queries->withCount(['sites']);
        $queries->where(function ($query) use ($tableColumns, $request) {
            // Global search
            $searchTerm = $request['params']['search'];
            if (!empty($searchTerm)) {
                $query->where(function ($innerQuery) use ($tableColumns, $searchTerm) {
                    foreach ($tableColumns as $column) {
                        $innerQuery->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                    }
                });
            }
            
            // Column-specific searches
            if (isset($request['params']['column_filters'])) {
                foreach ($request['params']['column_filters'] as $filter) {
                    if (isset($filter['value'])) {
                        $this->applyFilterCondition($query, $filter, []);
                    }

                    if ($filter['condition'] === 'is_null') {
                        $query->orWhereNull($filter['field']);
                    } elseif ($filter['condition'] === 'is_not_null') {
                        $query->orWhereNotNull($filter['field']);
                    }
                }
            }
        });
        $queries->orderBy(($sort_column !== '') ? $sort_column : $request['params']['sort_column'], $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

        $records = [
            'data' => $data,
            'total_rows' => $data->total(),
        ];

        return response()->json($records);
    }

    public function applyFilterCondition($query, $filter, $filteredIdArr) {
        switch ($filter['condition']) {
            case 'not_contain':
                $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
                break;
            case 'equal':
                $query->where($filter['field'], $filter['value']);
                break;
            case 'not_equal':
                $query->whereNot($filter['field'], $filter['value']);
                break;
            case 'start_with':
                $query->where($filter['field'], 'LIKE', $filter['value'] . '%');
                break;
            case 'end_with':
                $query->where($filter['field'], 'LIKE', '%' . $filter['value']);
                break;
            case 'greater_than':
                $query->where($filter['field'], '>', $filter['value']);
                break;
            case 'greater_than_equal':
                $query->where($filter['field'], '>=', $filter['value']);
                break;
            case 'less_than':
                $query->where($filter['field'], '<', $filter['value']);
                break;
            case 'less_than_equal':
                $query->where($filter['field'], '<=', $filter['value']);
                break;
            case 'contain':
                $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                break;
            case 'is_null':
                $query->orWhereNull($filter['field']);
                break;
            case 'is_not_null':
                $query->orWhereNotNull($filter['field']);
                break;
        }
    }

    // public function exportToExcel($selectedRowsData)
    // {
    //     $paymentMethodsArr = explode(',', $selectedRowsData);
    //     foreach ($paymentMethodsArr as $key => $value) {
    //         $paymentMethodsArr[$key] = (int)$value;
    //     }

    //     $currentDate = Carbon::now()->format('Ymd_His');
    //     $exportTitle = 'payment_method_' . $currentDate . '.xlsx';
        
    //     return (new PaymentMethodExport($paymentMethodsArr))->download($exportTitle);
    // }

    public function getAllPaymentMethodForExport()
    {
        $data = PaymentMethod::getAllPaymentMethods();

        return response()->json($data);
    }

    public function getPaymentMethodLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'paymentmethod')
                                        ->select('id')
                                        ->get();

        $paymentMethodLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($paymentMethodLogEntries, $value);
            }
        }

        return response()->json($paymentMethodLogEntries);
    }

    public function getAllSites()
    {
        $sites = Site::getAllSites();

        return response()->json($sites);
    }
}
