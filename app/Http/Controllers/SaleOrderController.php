<?php

namespace App\Http\Controllers;

use App\Exports\SaleOrdersExport;
use App\Http\Requests\SaleOrderItemRequest;
use App\Http\Requests\SaleOrderRequest;
use App\Models\ContentType;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class SaleOrderController extends Controller
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
            return Inertia::render('CRM/SaleOrders/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/SaleOrders/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/SaleOrders/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleOrderRequest $request)
    {
        dd($request->sale_order_items);
        $data = $request->all();

        if (count($data['sale_order_items']) > 0) {
            $saleOrderItemRequest = new SaleOrderItemRequest();
            $request->validate($saleOrderItemRequest->rules());
        }

        $newSaleOrderData = SaleOrder::create([
            'name' => $data['lead_front_name'],
            'mimo' => $data['lead_front_mimo'],
            'product' => $data['lead_front_product'],
            'quantity' => $data['lead_front_quantity'],
            'price' => $data['lead_front_price'],
            'vc' => $data['lead_front_vc'],
            'sdm' => $data['lead_front_sdm'],
            'liquid' => $data['lead_front_liquid'],
            'bank' => $data['lead_front_bank'],
            'note' => $data['lead_front_note'],
            'commission' => $data['lead_front_commission'],
            'edited_at' => $data['lead_front_edited_at'],
            'created_at' => $data['lead_front_created_at'],
            'lead_id' => $data['lead_id'],
            'email' => $data['lead_front_email'],
            'phone_number' => $data['lead_front_phone_number'],
        ]);
        $newSaleOrderData->save();

        if (count($data['sale_order_items']) > 0) {
            foreach ($request->lead_notes as $key => $value) {
                $newSaleOrderItemData = SaleOrderItem::create([
                    'note' => $value['note'],
                    'created_by_id' => $value['created_by_id'],
                    'lead_id' => $newLeadData->id,
                    'user_editable' => $value['user_editable'],
                ]);
            }
            $newSaleOrderItemData->save();
        }

        $errorMsgTitle = "You have successfully created a new sale order.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('sale-orders.index')
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

    public function getSaleOrders(Request $request)
    {   
        if ($request['checkedFilters']) {
            $query = SaleOrder::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $tempArray = [];
                    foreach ($options as $value) {
                        array_push($tempArray, $value);
                    }
                    $query->whereIn($category, $tempArray);

                } elseif (is_string($options) && $options !== '') {
                    switch($options) {
                        case('Today'):
                            $query->whereBetween($category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()]);
                            break;
                        case('Past 7 days'):
                            $query->whereBetween($category, [Carbon::today()->subDays(7)->toDateTimeString(), Carbon::today()->toDateTimeString()]);
                            break;
                        case('This month'):
                            $query->whereMonth($category, Carbon::today()->month);
                            break;
                        case('This year'):
                            $query->whereYear($category, Carbon::today()->year);
                            break;
                        case('No date'):
                            $query->whereNull($category);
                            break;
                        case('Has date'):
                            $query->whereNotNull($category);
                            break;
                        default:
                            $query->whereBetween($category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()]);
                    }
                }
            }

            $tableColumns = Schema::getColumnListing('core_saleorder');

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
                        $this->applyFilterCondition($query, $filter, isset($assigneeIdArr) ? $assigneeIdArr : []);
                        
                    }
                    
                    if ($filter['condition'] === 'is_null') {
                        $query->orWhereNull($filter['field']);
                    } elseif ($filter['condition'] === 'is_not_null') {
                        $query->orWhereNotNull($filter['field']);
                    }
                }
            }
                                    
            $sort_column = '';

            // dd($query->toSql(), $query->getBindings());
            $data = $query->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);
                            
            $records = [
                'data' => $data,
                'total_rows' => $data->total(),
            ];
            
            return response()->json($records);
        }
        
        $tableColumns = Schema::getColumnListing('core_saleorder');
        $sort_column = '';
        
        // Default fetch all on load
        $queries = SaleOrder::query();
        $queries->with([
                            'creator:id,username,site_id', 
                            'creator.site:id,name', 
                            'site:id,name', 
        ]);
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
        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);
        
        $records = [
            'data' => $data,
            'total_rows' => $data->total(),
        ];

        return response()->json($records);
    }

    public function applyFilterCondition($query, $filter, $filteredIdArr) 
    {
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

    public function getCategories(Request $request)
    {
        $written_date = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $tc_sent = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $tt_received = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $settlement_date = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $currency_pair = [ 
            ['id' => 'USDEUR', 'title' => "USD/EUR"],
            ['id' => 'USDGBP', 'title' => "USD/GBP"],
            ['id' => 'USDAUD', 'title' => "USD/AUD"],
            ['id' => 'USDNZD', 'title' => "USD/NZD"],
        ];

        $data = [
            'written_date' => $written_date,
            'tc_sent' => $tc_sent,
            'tt_received' => $tt_received,
            'settlement_date' => $settlement_date,
            'currency_pair' => $currency_pair,
        ];
        
        return response()->json($data);
    }

    public function getSaleOrderItems(string $id)
    {
        $data = SaleOrder::find($id);

        return response()->json($data->saleOrderItems);
    }

    public function getAllSaleOrdersForExport()
    {
        $data = SaleOrder::with([
                                'creator:id,username,site_id', 
                                'creator.site:id,name', 
                                'site:id,name', 
                            ])
                            ->orderByDesc('id')
                            ->get();

        return response()->json($data);
    }

    public function getTotalSaleOrderCount()
    {   
        return response()->json(SaleOrder::count());
    }

    public function getSaleOrderLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'saleorder')
                                        ->select('id')
                                        ->orderByDesc('id')
                                        ->get();

        $saleOrderLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($saleOrderLogEntries, $value);
            }
        }

        return response()->json($saleOrderLogEntries);
    }
    
    public function exportToExcel($selectedRowsData)
    {
        $saleOrderArr = explode(',', $selectedRowsData);
        foreach ($saleOrderArr as $key => $value) {
            $saleOrderArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'sale-order_' . $currentDate . '.xlsx';
        
        return (new SaleOrdersExport($saleOrderArr))->download($exportTitle);
    }

    public function getAllSites()
    {
        $sites = Site::all();

        return response()->json($sites);
    }
}
