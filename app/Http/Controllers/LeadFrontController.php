<?php

namespace App\Http\Controllers;

use App\Exports\LeadFrontsExport;
use App\Http\Requests\LeadFrontRequest;
use App\Models\ContentType;
use App\Models\Lead;
use App\Models\LeadChangelog;
use App\Models\LeadFront;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class LeadFrontController extends Controller
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
            return Inertia::render('CRM/LeadFronts/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/LeadFronts/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/LeadFronts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadFrontRequest $request)
    {
        $data = $request->all();

        // dd($data);
        // $leadFrontChanges = [];

        // Insert into lead_front table
        $newLeadFrontData = LeadFront::create([
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
        $newLeadFrontData->save();

        // // Add the change to the lead notes changes array
        // $leadFrontChanges['New'] = [
        //     'id' => $newLeadFrontData->id,
        //     'description' => 'A new lead front has been created',
        // ];

        // if (count($leadFrontChanges) > 0) {
        //     $newLeadChangelog = new LeadChangelog;

        //     $newLeadChangelog->lead_id = $data['linked_lead'];
        //     $newLeadChangelog->column_name = 'lead_front';
        //     $newLeadChangelog->lead_changes = [];
        //     $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        //     $newLeadChangelog->lead_notes_changes = [];
        //     $newLeadChangelog->description = 'The lead front has been successfully created';

        //     $newLeadChangelog->save();
        // }
        
        $errorMsgTitle = "You have successfully created a new lead front.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('lead-fronts.index')
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
        $data = LeadFront::with(['lead', 'lead.assignee:id,username,site_id', 'lead.assignee.site:id,name'])
                            ->find($id);

        return Inertia::render('CRM/LeadFronts/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadFrontRequest $request, string $id)
    {
        $data = $request->all();
        
        // Log changes
        // $leadFrontChanges = [];
        $oldLeadFrontData = LeadFront::find($id);

        // // Retrieve all column names from the database table
        // $leadFrontColumns = Schema::getColumnListing('lead_front');

        // // Mapping between request column names and database column names
        // $leadFrontColumnMappings = [
        //     'lead_front_name' => 'name',
        //     'lead_front_mimo' => 'mimo',
        //     'lead_front_product' => 'product',
        //     'lead_front_quantity' => 'quantity',
        //     'lead_front_price' => 'price',
        //     'lead_front_sdm' => 'sdm',
        //     'lead_front_liquid' => 'liquid',
        //     'lead_front_bank_name' => 'bank_name',
        //     'lead_front_bank_account' => 'bank_account',
        //     'lead_front_note' => 'note',
        //     'lead_front_commission' => 'commission',
        //     'lead_front_vc' => 'vc',
        //     'lead_front_edited_at' => 'edited_at',
        // ];

        if (isset($oldLeadFrontData)) {
            // foreach ($leadFrontColumns as $columnName) {
            //     // Skip 'created_at' and 'updated_at' columns
            //     if ($columnName === 'id' || $columnName === 'created_at' || $columnName === 'updated_at' || $columnName === 'deleted_at') {
            //         continue;
            //     }

            //     $requestColumnName = array_search($columnName, $leadFrontColumnMappings);
    
            //     // Get the old value from the database
            //     $oldValue = $oldLeadFrontData->$columnName;
    
            //     // Get the new value from the request using the mapped column name
            //     switch($columnName) {
            //         case('quantity'):
            //         case('price'):
            //         case('commission'):
            //             $oldValue = number_format($oldValue, 2);
            //             $newValue = number_format($data[$requestColumnName], 2) ?? null;
            //             break;
            //         case('sdm'):
            //         case('liquid'):
            //             $oldValue = boolval($oldValue);
            //             $newValue = $data[$requestColumnName] ?? null;
            //             break;
            //         case('total'):
            //             $oldValue = number_format($oldValue, 2);
            //             $newValue = number_format($data['lead_front_quantity'] * $data['lead_front_price'], 2) ?? null;
            //             break;
            //         case('linked_lead'):
            //             $newValue = $oldValue;
            //             break;
            //         default:
            //             $newValue = $data[$requestColumnName] ?? null;
            //     }
    
            //     // Check if the value has changed
            //     if ($newValue !== $oldValue) {
            //         $leadFrontChanges[$columnName] = [
            //             'id' => $id,
            //             'old' => $oldValue,
            //             'new' => $newValue,
            //         ];
            //     }
            // }

            $oldLeadFrontData->update([
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
                'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_edited_at']),
                'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['lead_front_created_at']),
                'lead_id' => $data['lead_id'],
                'email' => $data['lead_front_email'],
                'phone_number' => $data['lead_front_phone_number'],
            ]);
        }
        
        // if (count($leadFrontChanges) > 0) {
        //     $newLeadChangelog = new LeadChangelog;

        //     $newLeadChangelog->lead_id = $data['linked_lead'];
        //     $newLeadChangelog->column_name = 'lead_front';
        //     $newLeadChangelog->lead_changes = [];
        //     $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        //     $newLeadChangelog->lead_notes_changes = [];
        //     $newLeadChangelog->description = 'The lead front has been successfully updated';

        //     $newLeadChangelog->save();
        // }
		
        $errorMsgTitle = "You have successfully updated the lead front.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('lead-fronts.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingLeadFront = LeadFront::find($id);
        $linked_lead = $existingLeadFront->linked_lead;
        $existingLeadFront->delete();

        $leadFrontChanges = [];
        
        $leadFrontChanges['Delete'] = [
            'id' => $id,
            'description' => 'This lead front has been deleted',
        ];

        $newLeadChangelog = new LeadChangelog;

        $newLeadChangelog->lead_id = $linked_lead;
        $newLeadChangelog->column_name = 'lead_front';
        $newLeadChangelog->lead_changes = [];
        $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        $newLeadChangelog->lead_notes_changes = [];
        $newLeadChangelog->description = 'This lead front has been deleted';

        $newLeadChangelog->save();

        $errorMsgTitle = "You have successfully deleted the lead front.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('lead-fronts.index')
                        ->with('errorMsg', $errorMsg);
    }

    public function getAllLeadFronts(Request $request)
    {
        if ($request['checkedFilters']) {
            $query = Leadfront::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    if ($category === 'assignee_id'){
                        foreach ($options as $value) {
                            $query->whereHas('lead', function ($query) use ($value) {
                                $query->where('assignee_id', $value);
                            });
                        }
                    } else {
                        $tempArray = [];
                        foreach ($options as $value) {
                            // dd($category);
                            array_push($tempArray, (($category === 'vc') ? $value : (int)$value));
                        }
                        $query->whereIn($category, $tempArray);
                    }
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

            $tableColumns = Schema::getColumnListing('core_leadfront');
            $sort_column = '';

            if ($request['params']['sort_column'] === 'lead_front_commission') {
                $sort_column = 'commission';
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
                        if ($filter['field'] === 'lead_front_assignee') {
                            $leadAssigneeId = Lead::with('assignee')
                                                    ->whereHas('assignee', function ($query) use($filter) {
                                                        $query->where('username', 'LIKE', '%' . $filter['value'] . '%');
                                                    })
                                                    ->get()
                                                    ->map(function ($lead) {
                                                        return [
                                                            'id' => $lead->id,
                                                            'value' => $lead->assignee->username,
                                                        ];
                                                    });

                            $filteredId = [];
                            foreach ($leadAssigneeId as $key => $value) {
                                $filteredId[] = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
                                ];
                            }
                        }

                        if ($filter['field'] === 'lead_id') {
                            $leadId = Lead::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $filter['value'] . '%'])
                                                    ->select('id', 'first_name')
                                                    ->get();

                            $filteredId = [];
                            foreach ($leadId as $key => $value) {
                                $filteredId[] = [
                                    'id' => $value->id,
                                    'value' => $value->name,
                                ];
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
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
                    }
                }
            }
            
            $data = $query->with(['lead', 'lead.assignee', 'lead.assignee.site'])
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);

            $records = [
                'data' => $data,
                'total_rows' => $data->total(),
            ];

            return response()->json($records);
        }

        $tableColumns = Schema::getColumnListing('core_leadfront');
        $sort_column = '';

        if ($request['params']['sort_column'] === 'lead_front_commission') {
            $sort_column = 'commission';
        }

        $queries = LeadFront::query();
        $queries->with(['lead', 'lead.assignee', 'lead.assignee.site']);
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
                        if ($filter['field'] === 'lead_front_assignee') {
                            $leadAssigneeId = Lead::with('assignee')
                                                    ->whereHas('assignee', function ($query) use($filter) {
                                                        $query->where('username', 'LIKE', '%' . $filter['value'] . '%');
                                                    })
                                                    ->get()
                                                    ->map(function ($lead) {
                                                        return [
                                                            'id' => $lead->id,
                                                            'value' => $lead->assignee->username,
                                                        ];
                                                    });

                            // dd($leadAssigneeId);
                            $filteredId = [];
                            foreach ($leadAssigneeId as $key => $value) {
                                $filteredId[] = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
                                ];
                            }
                        }

                        if ($filter['field'] === 'lead_id') {
                            $leadId = Lead::whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $filter['value'] . '%'])
                                                    ->select('id', 'first_name')
                                                    ->get();

                            $filteredId = [];
                            foreach ($leadId as $key => $value) {
                                $filteredId[] = [
                                    'id' => $value->id,
                                    'value' => $value->name,
                                ];
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
                        
                        if ($filter['condition'] === 'is_null') {
                            $query->orWhereNull($filter['field']);
                        } elseif ($filter['condition'] === 'is_not_null') {
                            $query->orWhereNotNull($filter['field']);
                        }
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

    public function applyFilterCondition($query, $filter, $filteredIdArr) {
        switch ($filter['condition']) {
            case 'not_contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereNotIn('lead_id', $tempArr);
                    // if($filter['field'] === 'lead_front_assignee') {
                    // }
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    if ($filter['field'] === 'lead_id'){
                        $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 0);
                    } else {
                        $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    if ($filter['field'] === 'lead_id'){
                        $query->whereNot(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 0);
                    } else {
                        $query->whereNot(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'LIKE', '%' . $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '<', $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '<=', $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '>', $filter['value']);
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
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], '>=', $filter['value']);
                }
                break;
            case 'contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereIn('lead_id', $tempArr);
                } else {
                    $query->where(($filter['field'] === 'lead_front_commission') ? 'commission' : $filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                }
                break;
        }
    }
    
    public function getLatestLeadFronts()
    {
        $data = LeadFront::with([
                                'lead', 
                                'lead.assignee', 
                                'lead.assignee.site'
                            ])
                            ->latest()
                            ->take(5)
                            ->get();
        
        return response()->json($data);
    }

    public function getLead(string $id)
    {
        return response()->json(LeadFront::find($id)->lead);
    }
    
    public function getLeadDetails(string $id)
    {
        return response()->json(Lead::select('id', 'email', 'phone_number')->find($id));
    }
    
    public function getLeadList()
    {
        // Fetch leads without associated lead fronts
        $leadsWithoutFronts = Lead::whereDoesntHave('leadfront')
                                    ->limit(20)
                                    ->orderByDesc('id')
                                    ->get()
                                    ->map(function ($lead) {
                                        return [
                                            'id' => $lead->id,
                                            'value' => $lead->first_name . ' ' . $lead->last_name,
                                        ];
                                    });

        return response()->json($leadsWithoutFronts);
    }

    public function getLeadFrontCategories(Request $request)
    {
        // Fetch all filter categories
        $vc = LeadFront::select('vc')
                        ->orderBy('vc')
                        ->groupBy('vc')
                        ->get()
                        ->map(function ($vcs) {
                            return [
                                'id' => $vcs->vc,
                                'title' => $vcs->vc,
                            ];
                        });

        $assignee_id = User::has('assignedLeads')
                            ->has('assignedLeads.leadfront')
                            ->with('site:id,name')
                            ->select('id', 'username', 'site_id')
                            ->orderByDesc('id')
                            ->get()
                            ->map(function ($user) {
                                return [
                                    'id' => $user->id,
                                    'title' => $user->username . ' (' . $user->site->name . ')',
                                ];
                            });
                        
        $created_at = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $data = [
            'created_at' => $created_at,
            'vc' => $vc,
            'assignee_id' => $assignee_id,
        ];
        
        return response()->json($data);
    }
    
    public function exportToExcel($selectedRowsData)
    {
        $leadFrontsArr = explode(',', $selectedRowsData);
        foreach ($leadFrontsArr as $key => $value) {
            $leadFrontsArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'lead-fronts_' . $currentDate . '.xlsx';
        
        return (new LeadFrontsExport($leadFrontsArr))->download($exportTitle);
    }

    // Get the changelogs with this lead front id from the lead changelog table
    // public function getLeadFrontChangelogs(string $id)
    // {
    //     $leadFront = LeadFront::find($id);
        
    //     $existingLeadFrontChangelogs = LeadChangelog::where('lead_id', $leadFront->lead->id)
    //                                                 ->whereNull('deleted_at')
    //                                                 ->orderBy('created_at', 'desc')
    //                                                 ->get();

    //     $leadFrontChanges = [];
    //     foreach ($existingLeadFrontChangelogs as $changelog) {
    //         $changes = $changelog->lead_front_changes;
    //         if (isset($changes)) {
    //             foreach ($changes as $change) {
    //                 $leadFrontChanges[$changelog->id]['lead_changelog_id'] = $changelog->id;
    //                 $leadFrontChanges[$changelog->id]['description'] = $changelog->description;
    //                 $leadFrontChanges[$changelog->id]['created_at'] = $changelog->created_at->toDateTimeString();
                    
    //                 foreach ($changes as $field => $data) {
    //                     // Check if the 'id' key exists in the change data
    //                     if (isset($data['id']) && (string)$data['id'] === $id) {
    //                         $leadFrontChanges[$changelog->id]['lead_front_changes'][$field] = $data; // Collect changes for each lead front
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return response()->json($leadFrontChanges);
    // }

    public function getLeadFrontLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'leadfront')
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

    public function getTotalLeadFrontCount()
    {
        return response()->json(LeadFront::count());
    }
}
