<?php

namespace App\Http\Controllers;

use App\Exports\SaleOrdersExport;
use App\Http\Requests\SaleOrderItemRequest;
use App\Http\Requests\SaleOrderRequest;
use App\Models\ContentType;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
        $data = $request->all();
        // dd($data);

        if (count($data['sale_order_items']) > 0) {
            $saleOrderItemRequest = new SaleOrderItemRequest();
            $request->validate($saleOrderItemRequest->rules());
        }

        $newSaleOrderData = SaleOrder::create([
            'public_id' => $this->generatePublicId('saleorder'),
            'written_date' => $data['written_date'],
            'vc' => $data['vc'],
            'room_number' => $data['room_number'],
            'allo' => $data['allo'],
            'allo_name' => $data['allo_name'],
            'sm_number' => $data['sm_number'],
            'gm_number' => $data['gm_number'],
            'fm_number' => $data['fm_number'],
            'lm_number' => $data['lm_number'],
            'ao_1' => $data['ao_1'],
            'ao_1_name' => $data['ao_1_name'],
            'ao_2' => $data['ao_2'],
            'ao_2_name' => $data['ao_2_name'],
            'bonus_comment' => $data['bonus_comment'],
            'currency_pair' => $data['currency_pair'],
            'exchange_rate' => $data['exchange_rate'],
            'registered_name' => $data['registered_name'],
            'contact_name' => $data['contact_name'],
            'office_number_1' => $data['office_number_1'],
            'office_number_2' => $data['office_number_2'],
            'home_number' => $data['home_number'],
            'mobile_number' => $data['mobile_number'],
            'fax_number' => $data['fax_number'],
            'date_of_birth' => $data['date_of_birth'],
            'email' => $data['email'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'city' => $data['city'],
            'country' => $data['country'],
            'exit_time_frame' => $data['exit_time_frame'],
            'sell_price' => $data['sell_price'],
            'allocation_officer' => $data['allocation_officer'],
            'trade_plus' => $data['trade_plus'],
            'settlement_date' => $data['settlement_date'],
            'factory' => $data['factory'],
            'pay_via' => $data['pay_via'],
            'allo_comment' => $data['allo_comment'],
            'docs_received' => $data['docs_received'],
            'tc_sent' => $data['tc_sent'],
            'tt_received' => $data['tt_received'],
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
            'balance_due' => $data['balance_due'],
            'exchanged_balance_due' => $data['exchanged_balance_due'],
            'site_id' => $data['site_id'],
            'se_name' => $data['se_name'],
            'se_number' => $data['se_number'],
            'created_by_id' => $data['created_by_id'],
        ]);
        $newSaleOrderData->save();

        if (count($data['sale_order_items']) > 0) {
            foreach ($data['sale_order_items'] as $key => $value) {
                $newSaleOrderItemData = SaleOrderItem::create([
                    'public_id' => $this->generatePublicId('saleorderitem'),
                    'order_type' => $value['order_type'],
                    'product' => $value['product'],
                    'price' => $value['price'],
                    'exchanged_price' => $value['exchanged_price'],
                    'quantity' => $value['quantity'],
                    'subtotal' => $value['subtotal'],
                    'commission_rate' => $value['commission_rate'],
                    'commission' => $value['commission'],
                    'total_exchanged_price' => $value['total_exchanged_price'],
                    'total_price' => $value['total_price'],
                    'edited_at' => $data['edited_at'],
                    'created_at' => $data['edited_at'],
                    'sale_order_id' => $newSaleOrderData->id,
                    'completed_at' => null,
                    'order_id' => null,
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

    public function generatePublicId($model)
    {
        do {
            $newPublicId = Str::uuid();
        } while ($this->checkExistingPublicId($newPublicId, $model));
        
        return (string) $newPublicId;
    }

    public function checkExistingPublicId($string, $model)
    {
        $existingPublicId = $model === 'saleorder'
                                ? SaleOrder::where('public_id', $string)->get()
                                : SaleOrderItem::where('public_id', $string)->get();

        if (count($existingPublicId) > 0) {
            return true;
        }
        
        return false;
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
        $data = SaleOrder::with(['creator:id,username', 'site', 'saleOrderItems'])
                            ->find($id);

        $existingSaleOrderItems = $data->saleOrderItems;


		return Inertia::render('CRM/SaleOrders/Edit', [
            'data' => $data, 
            'saleOrderItemsData' => $existingSaleOrderItems,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaleOrderRequest $request, string $id)
    {
        $data = $request->all();
        // dd($data);

        if (count($data['sale_order_items']) > 0) {
            $saleOrderItemRequest = new SaleOrderItemRequest();
            $this->validate($request, $saleOrderItemRequest->rules(), $saleOrderItemRequest->messages(), $saleOrderItemRequest->attributes());
        }
        $existingSaleOrder = SaleOrder::find($id);

        $existingSaleOrder->update([
            'public_id' => $data['public_id'],
            'written_date' => $data['written_date'],
            'vc' => $data['vc'],
            'room_number' => $data['room_number'],
            'allo' => $data['allo'],
            'allo_name' => $data['allo_name'],
            'sm_number' => $data['sm_number'],
            'gm_number' => $data['gm_number'],
            'fm_number' => $data['fm_number'],
            'lm_number' => $data['lm_number'],
            'ao_1' => $data['ao_1'],
            'ao_1_name' => $data['ao_1_name'],
            'ao_2' => $data['ao_2'],
            'ao_2_name' => $data['ao_2_name'],
            'bonus_comment' => $data['bonus_comment'],
            'currency_pair' => $data['currency_pair'],
            'exchange_rate' => $data['exchange_rate'],
            'registered_name' => $data['registered_name'],
            'contact_name' => $data['contact_name'],
            'office_number_1' => $data['office_number_1'],
            'office_number_2' => $data['office_number_2'],
            'home_number' => $data['home_number'],
            'mobile_number' => $data['mobile_number'],
            'fax_number' => $data['fax_number'],
            'date_of_birth' => $data['date_of_birth'],
            'email' => $data['email'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'city' => $data['city'],
            'country' => $data['country'],
            'exit_time_frame' => $data['exit_time_frame'],
            'sell_price' => $data['sell_price'],
            'allocation_officer' => $data['allocation_officer'],
            'trade_plus' => $data['trade_plus'],
            'settlement_date' => $data['settlement_date'],
            'factory' => $data['factory'],
            'pay_via' => $data['pay_via'],
            'allo_comment' => $data['allo_comment'],
            'docs_received' => $data['docs_received'],
            'tc_sent' => $data['tc_sent'],
            'tt_received' => $data['tt_received'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
            'balance_due' => $data['balance_due'],
            'exchanged_balance_due' => $data['exchanged_balance_due'],
            'site_id' => $data['site_id'],
            'se_name' => $data['se_name'],
            'se_number' => $data['se_number'],
            'created_by_id' => $data['created_by_id'],
        ]);
        $existingSaleOrder->save();

        if (count($data['sale_order_items']) > 0) {
            foreach ($data['sale_order_items'] as $key => $value) {
                if ($value['id'] === null) {
                    // dd($value['completed_at']);
                    SaleOrderItem::create([
                        'public_id' => $this->generatePublicId('saleorderitem'),
                        'order_type' => $value['order_type'],
                        'product' => $value['product'],
                        'price' => $value['price'],
                        'exchanged_price' => $value['exchanged_price'],
                        'quantity' => $value['quantity'],
                        'subtotal' => $value['subtotal'],
                        'commission_rate' => $value['commission_rate'],
                        'commission' => $value['commission'],
                        'total_exchanged_price' => $value['total_exchanged_price'],
                        'total_price' => $value['total_price'],
                        'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['edited_at']),
                        'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['created_at']),
                        'sale_order_id' => $data['id'],
                        'completed_at' => ($value['completed_at'] !== null) ? preg_replace('/(\d{2})(\d{2})$/', '$1', $value['completed_at']) : null,
                        'order_id' => null,
                    ]);
                } else {
                    $existingSaleOrderItemData = SaleOrderItem::find($value['id']);

                    $existingSaleOrderItemData->update([
                        'public_id' => $value['public_id'],
                        'order_type' => $value['order_type'],
                        'product' => $value['product'],
                        'price' => $value['price'],
                        'exchanged_price' => $value['exchanged_price'],
                        'quantity' => $value['quantity'],
                        'subtotal' => $value['subtotal'],
                        'commission_rate' => $value['commission_rate'],
                        'commission' => $value['commission'],
                        'total_exchanged_price' => $value['total_exchanged_price'],
                        'total_price' => $value['total_price'],
                        'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['edited_at']),
                        'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $value['created_at']),
                        'sale_order_id' => $data['id'],
                        'completed_at' => ($value['completed_at'] !== null) ? preg_replace('/(\d{2})(\d{2})$/', '$1', $value['completed_at']) : null,
                        'order_id' => null,
                    ]);
                }
            }
        }
		
        $errorMsgTitle = "You have successfully updated the sale order.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('sale-orders.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingSaleOrderItems = SaleOrderItem::where('sale_order_id', $id)
                                                    ->select('id')
                                                    ->delete();

        $anyExistingSaleOrderItems = SaleOrderItem::where('sale_order_id', $id)
                                                        ->select('id')
                                                        ->count();
        
        if ($anyExistingSaleOrderItems === 0) {
            SaleOrder::destroy($id);

            $errorMsgTitle = "You have successfully deleted the sale order.";
            $errorMsgType = "success";
        } else {
            $errorMsgTitle = "Unable to delete the sale order. There are still order items active.";
            $errorMsgType = "error";
        }

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('sale-orders.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Delete sale order item.
     */
    public function deleteSaleOrderItem(string $id)
    {
        $existingSaleOrderItem = SaleOrderItem::find($id);
        $saleOrderId = $existingSaleOrderItem->sale_order_id;
        $existingSaleOrderItem->delete();

        return redirect(route('sale-orders.edit', $saleOrderId));
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

    public function getAllUsers() 
    {
        $data = User::getAllUsersWithRelationships()
                        ->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'value' => $user->username . ' (' . $user->site->name . ')',
                            ];
                        });
        
        return response()->json($data);
    }
}
