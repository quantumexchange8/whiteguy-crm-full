<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\PaymentSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;

class PaymentSubmissionController extends Controller
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
            return Inertia::render('CRM/PaymentSubmissions/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }

        return Inertia::render('CRM/PaymentSubmissions/Index');
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

    public function getPaymentSubmissions(Request $request)
    {
        if ($request['checkedFilters']) {
            $query = PaymentSubmission::query();

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

            $tableColumns = Schema::getColumnListing('core_user');
            $sort_column = '';
            
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
                                                    ->map(function ($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'value' => $item->username,
                                                        ];
                                                    });
                        }

                        if ($filter['field'] === 'payment_method_id') {
                            $filteredId = PaymentMethod::where('title', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'title')
                                                    ->get()
                                                    ->map(function ($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'value' => $item->title,
                                                        ];
                                                    });
                        }
                        
                        if ($filter['field'] === 'status') {
                            $fieldValArr = [ "Pending", "Approved", "Cancelled" ];

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
                                'user.site:id,name',
                                'paymentMethod:id,title'
                            ])
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

            $statusArray = [ 
                "Pending", "Approved", "Cancelled"
            ];
            
            foreach ($data as $paymentSubmission) {
                if (!is_null($paymentSubmission->status) && $paymentSubmission->status !== '') {
                    $paymentSubmission->status = $statusArray[$paymentSubmission->status - 1];
                }
            }
            
            $records = [
                'data' => $data,
                'total_rows' => $data->total(),
            ];
    
            return response()->json($records);
        }

        $tableColumns = Schema::getColumnListing('core_user');
        $sort_column = '';

        $queries = PaymentSubmission::query();
        $queries->with([
            'user:id,full_name,username,phone_number,email,country,address,site_id',
            'user.site:id,name',
            'paymentMethod:id,title'
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
                                                    ->map(function ($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'value' => $item->username,
                                                        ];
                                                    });
                        }

                        if ($filter['field'] === 'payment_method_id') {
                            $filteredId = PaymentMethod::where('title', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'title')
                                                    ->get()
                                                    ->map(function ($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'value' => $item->title,
                                                        ];
                                                    });
                        }
                        
                        if ($filter['field'] === 'status') {
                            $fieldValArr = [ "Pending", "Approved", "Cancelled" ];

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
        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

        $statusArray = [ 
            "Pending", "Approved", "Cancelled"
        ];
        
        foreach ($data as $paymentSubmission) {
            if (!is_null($paymentSubmission->status) && $paymentSubmission->status !== '') {
                $paymentSubmission->status = $statusArray[$paymentSubmission->status - 1];
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
                    if ($filter['field'] === 'user_id' || $filter['field'] === 'status' || $filter['field'] === 'payment_method_id') {
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
                    if ($filter['field'] === 'user_id' || $filter['field'] === 'status' || $filter['field'] === 'payment_method_id') {
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
    
    public function getLatestPaymentSubmissions()
    {
        $data = PaymentSubmission::with([
                                'user:id,full_name,username,phone_number,email,country,address,site_id',
                                'user.site:id,name',
                                'paymentMethod:id,title'
                            ])
                            ->latest()
                            ->take(5)
                            ->get();
        
        return response()->json($data);
    }

    public function getCategories(Request $request)
    {
        $date = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $status = [ 
            ['id' => 1, 'title' => "Pending"],
            ['id' => 2, 'title' => "Approved"],
            ['id' => 3, 'title' => "Cancelled"]
        ];

        $payment_method_id = PaymentMethod::select('id','title')
                        ->orderBy('id')
                        ->groupBy('id')
                        ->get()
                        ->map(function ($paymentMethod) {
                            return [
                                'id' => $paymentMethod->id,
                                'title' => $paymentMethod->title,
                            ];
                        });

        $data = [
            'date' => $date,
            'status' => $status,
            'payment_method_id' => $payment_method_id,
        ];
        
        return response()->json($data);
    }
    
    public function getPaymentSubmissionLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'paymentsubmission')
                                        ->select('id')
                                        ->get();

        $paymentSubmissionLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($paymentSubmissionLogEntries, $value);
            }
        }

        return response()->json($paymentSubmissionLogEntries);
    }
}
