<?php

namespace App\Http\Controllers;

use App\Models\SaleOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getSaleOrders(Request $request)
    {   
        if ($request['checkedFilters']) {
            $query = SaleOrder::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $tempArray = [];
                    foreach ($options as $value) {
                        array_push($tempArray, (int)$value);
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

            $tableColumns = Schema::getColumnListing('core_lead');

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
                        // if ($filter['field'] === 'lead_assignee') {
                        //     $leadAssigneeId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                        //                             ->select('id', 'username')
                        //                             ->get();

                        //     $assigneeIdArr = [];
                        //     foreach ($leadAssigneeId as $key => $value) {
                        //         $assigneeIdArr[] = [
                        //             'id' => $value->id,
                        //             'value' => $value->username,
                        //         ];
                        //     }
                        // }
                        $this->applyFilterCondition($query, $filter, isset($assigneeIdArr) ? $assigneeIdArr : []);
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
                    }
                }
            }
                                    
            $sort_column = '';

            // dd($query->toSql(), $query->getBindings());
            $data = $query->with([
                                'leadCreator:id,username,site_id', 
                                'leadCreator.site:id,name', 
                                'assignee:id,username,site_id', 
                                'assignee.site:id,name', 
                                'leadnotes',
                                'leadnotes.leadNoteCreator:id,username,site_id',
                                'leadnotes.leadNoteCreator.site:id,name',
                                'contactOutcome:id,title',
                                'stage:id,title',
                                'appointmentLabel:id,title'
                            ])
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
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
        // $queries->with([
        //                     'leadCreator:id,username,site_id', 
        //                     'leadCreator.site:id,name', 
        //                     'assignee:id,username,site_id', 
        //                     'assignee.site:id,name', 
        //                     'leadnotes',
        //                     'leadnotes.leadNoteCreator:id,username,site_id',
        //                     'leadnotes.leadNoteCreator.site:id,name',
        //                     'contactOutcome:id,title',
        //                     'stage:id,title',
        //                     'appointmentLabel:id,title'
        // ]);
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
                        // if ($filter['field'] === 'lead_assignee') {
                        //     $leadAssigneeId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                        //                             ->select('id', 'username')
                        //                             ->get();

                        //     $assigneeIdArr = [];
                        //     foreach ($leadAssigneeId as $key => $value) {
                        //         $assigneeIdArr[] = [
                        //             'id' => $value->id,
                        //             'value' => $value->username,
                        //         ];
                        //     }
                        // }
                        $this->applyFilterCondition($query, $filter, isset($assigneeIdArr) ? $assigneeIdArr : []);
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
                    }
                }
            }
            // dd($query->toSQL());
        });
        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
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
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereNotIn($filter['field'], $tempArr);
                } else {
                    $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
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
                    $query->where($filter['field'], $filter['value']);
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
                    $query->whereNot($filter['field'], $filter['value']);
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
                    $query->where($filter['field'], 'LIKE', $filter['value'] . '%');
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
                    $query->where($filter['field'], 'LIKE', '%' . $filter['value']);
                }
                break;
            case 'greater_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] < $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where($filter['field'], '<', $filter['value']);
                }
                break;
            case 'greater_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] <= $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where($filter['field'], '<=', $filter['value']);
                }
                break;
            case 'less_than':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] > $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where($filter['field'], '>', $filter['value']);
                }
                break;
            case 'less_than_equal':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        if ($filter['value'] >= $value['value']) {
                            array_push($tempArr, $value['id']);    
                        }
                    }
                    $query->whereIn($filter['field'], $tempArr);
                } else {
                    $query->where($filter['field'], '>=', $filter['value']);
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
                    $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                }
                break;
        }
    }

    public function getTotalSaleOrderCount()
    {   
        return response()->json(SaleOrder::count());
    }
}
