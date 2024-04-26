<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Http\Requests\NotificationRequest;
use App\Models\OrderChangelog;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Models\Notification;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
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
            return Inertia::render('CRM/Orders/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/Orders/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Orders/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $data = $request->all();
        
        if ($data['send_notification']) {
            $request->validate([
                'notification_title' => 'required|string|max:250',
                'notification_description' => 'required|string',
            ]);
        }

        // dd($data);
        // $orderChanges = [];

        // Insert into orders table
        $existingOrder = Order::create([
            'trade_id' => $data['trade_id'],
            'date' => $data['date'],
            'action_type' => $data['action_type'],
            'stock_type' => $data['stock_type'],
            'stock' => $data['stock'],
            'unit_price' => $data['unit_price'],
            'quantity' => $data['quantity'],
            'current_unit_price' => $data['current_unit_price'],
            'profit' => $data['profit'],
            'status' => $data['status'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
            'user_id' => $data['user_id'],
            'is_deleted' => $data['is_deleted'],
            'limb_stage' => $data['limb_stage'],
            'confirmation_name' => $data['confirmation_name'],
            'confirmed_at' => $data['confirmed_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['confirmed_at']) : $data['confirmed_at'],
        ]);

        $existingOrder->save();

        if ($data['send_notification']) {
            $newNotification = Notification::create([
                'title' => $data['notification_title'],
                'description' => $data['notification_description'],
                'notification_type' => 'PRIVATE_MESSAGE',
                'attachment' => '',
                'is_read' => false,
                'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
                'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
                'user_id' => $data['user_id'],
            ]);
            $newNotification->save();
        }

        // Add the change to the user changes array
        // $orderChanges['New'] = [
        //     'description' => 'A new order has been created',
        // ];

        // if (count($orderChanges) > 0) {
        //     $newOrderChangelog = new OrderChangelog;

        //     $newOrderChangelog->orders_id = $existingOrder->id;
        //     $newOrderChangelog->column_name = 'orders';
        //     $newOrderChangelog->changes = $orderChanges;
        //     $newOrderChangelog->description = 'The order has been successfully created';

        //     $newOrderChangelog->save();
        // }
        
        $errorMsgTitle = "You have successfully created a new order.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('orders.index')
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
        $data = Order::with('user:id')
                        ->find($id);

        return Inertia::render('CRM/Orders/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, string $id)
    {
        $data = $request->all();

        if ($data['send_notification']) {
            $request->validate([
                'notification_title' => 'required|string|max:250',
                'notification_description' => 'required|string',
            ]);
        }
        
        // Additional validation based on user selection (Lead Front | Lead Notes)
        // if ($data['send_notification']) {
        //     $notificationRequest = new NotificationRequest();
        //     $request->validate($notificationRequest->rules());
        // }

        // dd($data);

        // $orderChanges = [];
        $existingOrder = Order::find($id);

        // if (isset($existingOrder)) {
        //     foreach ($existingOrder->toArray() as $key => $oldValue) {
        //         if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at') {
        //             continue;
        //         }

        //         // if ($key === 'send_notification') {
        //         //     $oldValue = boolval($oldValue);
        //         // }

        //         switch($key) {
        //             case('unit_price'):
        //             case('quantity'):
        //             case('total_price'):
        //             case('current_price'):
        //             case('profit'):
        //                 $oldValue = number_format($oldValue, 2);
        //                 $newValue = number_format($data[$key], 2) ?? null;
        //                 break;
        //             case('send_notification'):
        //                 $oldValue = boolval($oldValue);
        //                 $newValue = $data[$key] ?? null;
        //                 break;
        //             case('users_id'):
        //                 $newValue = $data['user_link'] ?? null;
        //                 break;
        //             default:
        //                 $newValue = $data[$key] ?? null;
        //         }
                
        //         // $newValue = $data[$key] ?? null;

        //         // Check if the value has changed
        //         if ($newValue !== $oldValue) {
        //             $orderChanges[$key] = [
        //                 'old' => $oldValue,
        //                 'new' => $newValue,
        //             ];
        //         }
        //     }
        // }

        // dd($orderChanges);

        // Insert into orders table
        $existingOrder->update([
            'trade_id' => $data['trade_id'],
            'date' => $data['date'],
            'action_type' => $data['action_type'],
            'stock_type' => $data['stock_type'],
            'stock' => $data['stock'],
            'unit_price' => $data['unit_price'],
            'quantity' => $data['quantity'],
            'current_unit_price' => $data['current_unit_price'],
            'profit' => $data['profit'],
            'status' => $data['status'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'user_id' => $data['user_id'],
            'is_deleted' => $data['is_deleted'],
            'limb_stage' => $data['limb_stage'],
            'confirmation_name' => $data['confirmation_name'],
            'confirmed_at' => $data['confirmed_at'] ? preg_replace('/(\d{2})(\d{2})$/', '$1', $data['confirmed_at']) : $data['confirmed_at'],
        ]);

        $existingOrder->save();

        if ($data['send_notification']) {
            $newNotification = Notification::create([
                'title' => $data['notification_title'],
                'description' => $data['notification_description'],
                'notification_type' => 'PRIVATE_MESSAGE',
                'attachment' => '',
                'is_read' => false,
                'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
                'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
                'user_id' => $data['user_id'],
            ]);
            $newNotification->save();
        }

        // if (count($orderChanges) > 0) {
        //     $newOrderChangelog = new OrderChangelog;

        //     $newOrderChangelog->orders_id = $id;
        //     $newOrderChangelog->column_name = 'orders';
        //     $newOrderChangelog->changes = $orderChanges;
        //     $newOrderChangelog->description = 'The order has been successfully updated';

        //     $newOrderChangelog->save();
        // }
        
        $errorMsgTitle = "You have successfully updated the order.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('orders.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, string $id)
    {
        $existingOrder = Order::find($id);
        
        $existingOrder->update([
            'is_deleted' => true,
        ]);

        // $existingOrder->save();

        // $orderChanges = [];
        
        // $orderChanges['Delete'] = [
        //     'id' => $id,
        //     'description' => 'This order has been deleted',
        // ];

        // $newOrderChangelog = new OrderChangelog;

        // $newOrderChangelog->orders_id = $id;
        // $newOrderChangelog->column_name = 'orders';
        // $newOrderChangelog->changes = $orderChanges;
        // $newOrderChangelog->description = 'The user has been successfully deleted';

        // $newOrderChangelog->save();

        $errorMsgTitle = "You have successfully deleted the order.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('orders.index')
                        ->with('errorMsg', $errorMsg);
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     $existingOrder = Order::find($id);

    //     $existingOrder->delete();

    //     // $orderChanges = [];
        
    //     // $orderChanges['Delete'] = [
    //     //     'id' => $id,
    //     //     'description' => 'This order has been deleted',
    //     // ];

    //     // $newOrderChangelog = new OrderChangelog;

    //     // $newOrderChangelog->orders_id = $id;
    //     // $newOrderChangelog->column_name = 'orders';
    //     // $newOrderChangelog->changes = $orderChanges;
    //     // $newOrderChangelog->description = 'The user has been successfully deleted';

    //     // $newOrderChangelog->save();

    //     $errorMsgTitle = "You have successfully deleted the user.";
    //     $errorMsgType = "success";

    //     $errorMsg = [
    //         'title' => $errorMsgTitle,
    //         'type' => $errorMsgType,
    //     ];

    //     return Redirect::route('orders.index')
    //                     ->with('errorMsg', $errorMsg);
    // }

    public function getOrders(Request $request)
    {   
        // dd($request);
        if ($request['checkedFilters']) {
            $query = Order::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $tempArray = [];
                    foreach ($options as $value) {
                        array_push($tempArray, (($category === "action_type" || $category === "status" || $category === "stock_type") ? $value : (int)$value));
                    }
                    
                    if($category === "site_id") {
                        $query->whereHas('user.site', function (EloquentBuilder $query) use ($tempArray) {
                            $query->whereIn('django_site.id', $tempArray);
                        });
                        continue;
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
        
            $tableColumns = Schema::getColumnListing('core_order');
            $sort_column = '';

            if ($request['params']['sort_column'] === 'order_confirmed_at') {
                $sort_column = 'confirmed_at';
            }

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
                        if ($filter['field'] === 'user_id') {
                            $filteredId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'username')
                                                    ->get()
                                                    ->map(function ($user) {
                                                        return [
                                                            'id' => $user->id,
                                                            'value' => $user->username,
                                                        ];
                                                    });
                        }
                        
                        if ($filter['field'] === 'status' || $filter['field'] === 'limb_stage') {
                            $fieldValArr = [];
                            switch ($filter['field']) {
                                case 'status':
                                    $fieldValArr = [ 
                                        "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
                                        "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
                                    ];
                                    break;
                                case 'limb_stage':
                                    $fieldValArr = [ 
                                        "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
                                        "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
                                    ];
                                    break;
                            }

                            $filteredId = [];

                            foreach ($fieldValArr as $index => $fieldVal) {
                                if (str_contains($fieldVal, $filter['value']) !== false) {
                                    $filteredId[] = [
                                        'id' => $index + 1,
                                        'value' => $fieldVal,
                                    ];
                                }
                            }
                        }
                        
                        if (isset($filteredId)) {
                            $filteredIdArr = [];
                            foreach ($filteredId as $key => $value) {
                                $filteredIdArr[] = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
                                ];

                            }
                        }

                        $this->applyFilterCondition($query, $filter, isset($filteredId) ? $filteredIdArr : []);
                    }

                    if ($filter['condition'] === 'is_null') {
                        $query->orWhereNull($filter['field']);
                    } elseif ($filter['condition'] === 'is_not_null') {
                        $query->orWhereNotNull($filter['field']);
                    }
                }
            }

            $data = $query->with([
                                'user:id,full_name,username,phone_number,email,country,address,site_id',
                                'user.site:id,name'
                            ])
                            ->where('is_deleted', false)
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

            $statusArray = [ 
                "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
                "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required" 
            ];

            $limbStageArray  = [ 
                "ALLO", "Allo + docs", "TT", "CLEARED", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
                "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
            ];

            foreach ($data as $order) {
                // Handle client_stage attribute
                if (!is_null($order->status) && $order->status !== '') {
                    $order->status = $statusArray[$order->status - 1];
                }
            
                // Handle limb attribute
                if (!is_null($order->limb_stage) && $order->limb_stage !== '') {
                    $order->limb_stage = $limbStageArray[$order->limb_stage - 1];
                }
            }
            
            $records = [
                'data' => $data,
                'total_rows' => $data->total(),
            ];
            
            return response()->json($records);
        }
        
        $tableColumns = Schema::getColumnListing('core_order');
        $sort_column = '';

        if ($request['params']['sort_column'] === 'order_confirmed_at') {
            $sort_column = 'confirmed_at';
        }

        $queries = Order::query();
        $queries->with([
            'user:id,full_name,username,phone_number,email,country,address,site_id',
            'user.site:id,name'
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
                        if ($filter['field'] === 'user_id') {
                            $filteredId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'username')
                                                    ->get()
                                                    ->map(function ($user) {
                                                        return [
                                                            'id' => $user->id,
                                                            'value' => $user->username,
                                                        ];
                                                    });
                        }
                        
                        if ($filter['field'] === 'status' || $filter['field'] === 'limb_stage') {
                            $fieldValArr = [];
                            switch ($filter['field']) {
                                case 'status':
                                    $fieldValArr = [ 
                                        "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
                                        "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
                                    ];
                                    break;
                                case 'limb_stage':
                                    $fieldValArr = [ 
                                        "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
                                        "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
                                    ];
                                    break;
                            }

                            $filteredId = [];

                            foreach ($fieldValArr as $index => $fieldVal) {
                                if (str_contains($fieldVal, $filter['value']) !== false) {
                                    $filteredId[] = [
                                        'id' => $index + 1,
                                        'value' => $fieldVal,
                                    ];
                                }
                            }
                        }
                        
                        if (isset($filteredId)) {
                            $filteredIdArr = [];
                            foreach ($filteredId as $key => $value) {
                                $filteredIdArr[] = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
                                ];

                            }
                        }

                        $this->applyFilterCondition($query, $filter, isset($filteredId) ? $filteredIdArr : []);
                    }

                    if ($filter['condition'] === 'is_null') {
                        $query->orWhereNull($filter['field']);
                    } elseif ($filter['condition'] === 'is_not_null') {
                        $query->orWhereNotNull($filter['field']);
                    }
                }
            }
        });
        $queries->where('is_deleted', false);
        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

        $statusArray = [ 
            "Pending", "In progress", "Active", "Cancelled", "Cancelled (approved)", "Cancelled (non-authorized)", 
            "Pending allocation", "Pending payment", "Pending clearance", "Cleared", "Trade confirmation required"
        ];
        $limbStageArray  = [ 
            "ALLO", "Allo + docs", "Tt", "Cleared", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
            "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" 
        ];

        foreach ($data as $order) {
            // Handle client_stage attribute
            if (!is_null($order->status) && $order->status !== '') {
                $order->status = $statusArray[$order->status - 1];
            }
        
            // Handle limb attribute
            if (!is_null($order->limb_stage) && $order->limb_stage !== '') {
                $order->limb_stage = $limbStageArray[$order->limb_stage - 1];
            }
        }
            
        $records = [
            'data' => $data,
            'total_rows' => $data->total(),
        ];

        return response()->json($records);
    }

    public function applyFilterCondition($query, $filter, $filteredIdArr) {
        switch ($filter['condition']) {
            case 'not_contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereNotIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
                }
                break;
            case 'equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] === $value['value']) {
                            array_push($tempArr, $value['id']);     
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    if ($filter['field'] === 'user_id' || $filter['field'] === 'status' || $filter['field'] === 'limb_stage'){
                        $query->where($filter['field'], 0);
                    } else {
                        $query->where($filter['field'], $filter['value']);
                    }
                }
                break;
            case 'not_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] !== $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    if ($filter['field'] === 'user_id' || $filter['field'] === 'status' || $filter['field'] === 'limb_stage'){
                        $query->whereNot($filter['field'], 0);
                    } else {
                        $query->whereNot($filter['field'], $filter['value']);
                    }
                }
                break;
            case 'start_with':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if (str_starts_with($value['value'], $filter['value'])) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'LIKE', $filter['value'] . '%');
                }
                break;
            case 'end_with':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if (str_ends_with($value['value'], $filter['value'])) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'LIKE', '%' . $filter['value']);
                }
                break;
            case 'greater_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($value['value'] > $filter['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '>', $filter['value']);
                }
                break;
            case 'greater_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($value['value'] >= $filter['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '>=', $filter['value']);
                }
                break;
            case 'less_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($value['value'] < $filter['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '<', $filter['value']);
                }
                break;
            case 'less_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($value['value'] <= $filter['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '<=', $filter['value']);
                }
                break;
            case 'contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                }
                break;
            case 'is_null':
                $query->orWhereNull($filter['field']);
                break;
            case 'is_not_null':
                $query->orWhereNotNull($filter['field']);
                break;
        }
    }

    public function getAllUsers() {
        $data = User::getAllUsersWithRelationships();
        
        return response()->json($data);
    }

    public function getTotalOrderCount()
    {
        return response()->json(Order::count());
    }

    public function getCategories(Request $request)
    {
        $status = [ 
            ['id' => 1, 'title' => "Pending"],
            ['id' => 2, 'title' => "In progress"],
            ['id' => 3, 'title' => "Active"],
            ['id' => 4, 'title' => "Cancelled"],
            ['id' => 5, 'title' => "Cancelled (approved)"],
            ['id' => 6, 'title' => "Cancelled (non-authorized)"],
            ['id' => 7, 'title' => "Pending allocation"],
            ['id' => 8, 'title' => "Pending payment"],
            ['id' => 9, 'title' => "Pending clearance"],
            ['id' => 10, 'title' => "Cleared"],
            ['id' => 11, 'title' => "Trade confirmation required"]
        ];

        $limb_stage = [ 
            ['id' => 1, 'title' => "ALLO"],
            ['id' => 2, 'title' => "Allo + docs"],
            ['id' => 3, 'title' => "TT"],
            ['id' => 4, 'title' => "CLEARED"],
            ['id' => 5, 'title' => "Cancelled"],
            ['id' => 6, 'title' => "Cancelled - bank block"],
            ['id' => 7, 'title' => "Cancelled - HTR"],
            ['id' => 8, 'title' => "Cancelled - order drop"],
            ['id' => 9, 'title' => "Cancelled refuse trade"],
            ['id' => 10, 'title' => "Kicked"],
            ['id' => 11, 'title' => "Carry over"],
            ['id' => 12, 'title' => "Free switch"]
        ];

        $action_type = [ 
            ['id' => "BUY", 'title' => "Buy"],
            ['id' => "SELL", 'title' => "Sell"]
        ];

        $site_id = Site::select('id','domain')
                        ->orderBy('id')
                        ->groupBy('id')
                        ->get()
                        ->map(function ($site) {
                            return [
                                'id' => $site->id,
                                'title' => $site->domain,
                            ];
                        });

        $stock_type = [ 
            ['id' => "BOND", 'title' => "Bonds"],
            ['id' => "SHARE", 'title' => "Shares"],
            ['id' => "ISA", 'title' => "ISAs"],
            ['id' => "FUND", 'title' => "Funds"],
            ['id' => "DIGITAL_ASSET", 'title' => "Digital Assets"],
            ['id' => "MUTUAL_FUND", 'title' => "Mutual Fund"],
            ['id' => "COMMODITY", 'title' => "Commodity"],
            ['id' => "FOREX", 'title' => "Forex"],
            ['id' => "UNKNOWN", 'title' => "Unknown"],
        ];

        $date = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $data = [
            'status' => $status,
            'limb_stage' => $limb_stage,
            'action_type' => $action_type,
            'site_id' => $site_id,
            'stock_type' => $stock_type,
            'date' => $date,
        ];
        
        return response()->json($data);
    }
    
    public function exportToExcel($selectedRowsData)
    {
        $ordersArr = explode(',', $selectedRowsData);
        foreach ($ordersArr as $key => $value) {
            $ordersArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'orders_' . $currentDate . '.xlsx';
        
        return (new OrdersExport($ordersArr))->download($exportTitle);
    }

    // Generate random unique 12 characters account id (Checked against db)
    public function generateTradeId($length = 12) 
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $tradeId = '';

        do {
            for ($i = 0; $i < $length; $i++) {
                $tradeId .= $characters[rand(0, $charactersLength - 1)];
            }
        } while ($this->checkExistingTradeId($tradeId)); // Regenerate if string exists in database

        return response()->json(['trade_id' => $tradeId]);
    }
    
    public function checkExistingTradeId($string)
    {
        $existingOrder = Order::where('trade_id', $string)->get();

        if (count($existingOrder) > 0) {
            return true;
        }
        
        return false;
    }

    // public function getOrderChangelogs(string $id)
    // {
    //     // $existingOrderChangelogs = OrderChangelog::where('orders_id', $id)
    //     //                                         ->orderBy('created_at', 'desc')
    //     //                                         ->get();
    //     $existingOrderChangelogs = Order::find($id)
    //                                     ->orderChangelogs()
    //                                     ->where('orders_id', $id)
    //                                     ->orderBy('created_at', 'desc')
    //                                     ->get();

    //     return response()->json($existingOrderChangelogs);
    // }
    
    public function getOrderLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'order')
                                        ->select('id')
                                        ->get();

        $leadFrontLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($leadFrontLogEntries, $value);
            }
        }

        return response()->json($leadFrontLogEntries);
    }
}
