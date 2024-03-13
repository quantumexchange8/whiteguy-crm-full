<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
        if ($request->send_notification) {
            $request->validate([
                'send_notification' => 'required|boolean',
                'notification_title' => 'required|string|max:200',
                'notification_description' => 'required|string|max:1000',
            ]);
        } else {
            $request->validate([
                'send_notification' => 'nullable|boolean',
                'notification_title' => 'nullable|string|max:200',
                'notification_description' => 'nullable|string|max:1000',
            ]);
        }
        
        $data = $request->all();

        // $userClientChanges = [];

        // Insert into orders table
        $newUserClientData = Order::create([
            'trade_id' => $data['trade_id'],
            'date' => $data['date'],
            'action_type' => $data['action_type'],
            'stock_type' => $data['stock_type'],
            'stock' => $data['stock'],
            'unit_price' => $data['unit_price'],
            'quantity' => $data['quantity'],
            'total_price' => $data['unit_price'] * $data['quantity'],
            'current_price' => $data['current_price'],
            'profit' => $data['profit'],
            'status' => $data['status'],
            'confirmed_at' => $data['confirmed_at'],
            'confirmation_name' => $data['confirmation_name'],
            'limb_stage' => $data['limb_stage'],
            'users_id' => $data['user_link'],
            'send_notification' => $data['send_notification'],
            'notification_title' => $data['notification_title'],
            'notification_description' => $data['notification_description'],
        ]);

        $newUserClientData->save();

        // Add the change to the user changes array
        // $userClientChanges['New'] = [
        //     'description' => 'A new user has been created',
        // ];

        // if (count($userClientChanges) > 0) {
        //     $newUserClientChangelog = new UserClientChangelog;

        //     $newUserClientChangelog->users_clients_id = $newUserClientData->id;
        //     $newUserClientChangelog->column_name = 'users_clients';
        //     $newUserClientChangelog->changes = $userClientChanges;
        //     $newUserClientChangelog->description = 'The user has been successfully created';

        //     $newUserClientChangelog->save();
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
        $data = Order::find($id);

        return Inertia::render('CRM/Orders/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getOrders(Request $request)
    {   
        $data = DB::table('orders')->whereNull('deleted_at')->get();

        return response()->json($data);
    }

    public function getTotalOrderCount()
    {
        return response()->json(Order::count());
    }

    public function getCategories(Request $request)
    {
        $status = DB::table('orders')
                        ->orderBy('status')
                        ->groupBy('status')
                        ->pluck('status');

        $limb_stage = DB::table('orders')
                            ->orderBy('limb_stage')
                            ->groupBy('limb_stage')
                            ->pluck('limb_stage');

        $action_type = DB::table('orders')
                            ->orderBy('action_type')
                            ->groupBy('action_type')
                            ->pluck('action_type');


        $stock_type = DB::table('orders')
                    ->orderBy('stock_type')
                    ->groupBy('stock_type')
                    ->pluck('stock_type');

        $created_at = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $data = [
            'status' => $status,
            'limb_stage' => $limb_stage,
            'action_type' => $action_type,
            'stock_type' => $stock_type,
            'created_at' => $created_at,
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
}
