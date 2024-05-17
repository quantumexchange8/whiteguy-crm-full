<?php

namespace App\Http\Controllers;

use App\Exports\PaymentMethodExport;
use App\Models\ContentType;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getPaymentMethods(Request $request)
    {   
        $tableColumns = Schema::getColumnListing('core_accountmanagerprofile');
        $sort_column = '';

        $queries = PaymentMethod::query();
        // $queries->with(['user:id,username']);
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
}
