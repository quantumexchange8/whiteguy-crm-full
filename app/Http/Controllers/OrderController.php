<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        //
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
}
