<?php

namespace App\Http\Controllers;

use App\Exports\LeadFrontsExport;
use App\Http\Requests\LeadFrontRequest;
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
        
        $leadFrontChanges = [];

        $total = $request->lead_front_quantity * $request->lead_front_price;

        // Insert into lead_front table
        $newLeadFrontData = LeadFront::create([
            'name' => $data['lead_front_name'],
            'product' => $data['lead_front_product'],
            'quantity' => $data['lead_front_quantity'],
            'price' => $data['lead_front_price'],
            'total' => $total,
            'commission' => $data['lead_front_commission'],
            'vc' => $data['lead_front_vc'],
            'sdm' => $data['lead_front_sdm'],
            'liquid' => $data['lead_front_liquid'],
            'mimo' => $data['lead_front_mimo'],
            'bank_name' => $data['lead_front_bank_name'],
            'bank_account' => $data['lead_front_bank_account'],
            'note' => $data['lead_front_note'],
            'linked_lead' => $data['linked_lead'],
            'edited_at' => $data['lead_front_edited_at'],
        ]);
        $newLeadFrontData->save();

        // Add the change to the lead notes changes array
        $leadFrontChanges['New'] = [
            'id' => $newLeadFrontData->id,
            'description' => 'A new lead front has been created',
        ];

        if (count($leadFrontChanges) > 0) {
            $newLeadChangelog = new LeadChangelog;

            $newLeadChangelog->lead_id = $data['linked_lead'];
            $newLeadChangelog->column_name = 'lead_front';
            $newLeadChangelog->lead_changes = [];
            $newLeadChangelog->lead_front_changes = $leadFrontChanges;
            $newLeadChangelog->lead_notes_changes = [];
            $newLeadChangelog->description = 'The lead front has been successfully created';

            $newLeadChangelog->save();
        }
        
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
        $data = LeadFront::find($id);

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
        $leadFrontChanges = [];
        $oldLeadFrontData = LeadFront::find($id);

        // Retrieve all column names from the database table
        $leadFrontColumns = Schema::getColumnListing('lead_front');

        // Mapping between request column names and database column names
        $leadFrontColumnMappings = [
            'lead_front_name' => 'name',
            'lead_front_mimo' => 'mimo',
            'lead_front_product' => 'product',
            'lead_front_quantity' => 'quantity',
            'lead_front_price' => 'price',
            'lead_front_sdm' => 'sdm',
            'lead_front_liquid' => 'liquid',
            'lead_front_bank_name' => 'bank_name',
            'lead_front_bank_account' => 'bank_account',
            'lead_front_note' => 'note',
            'lead_front_commission' => 'commission',
            'lead_front_vc' => 'vc',
            'lead_front_edited_at' => 'edited_at',
        ];

        if (isset($oldLeadFrontData)) {
            foreach ($leadFrontColumns as $columnName) {
                // Skip 'created_at' and 'updated_at' columns
                if ($columnName === 'id' || $columnName === 'created_at' || $columnName === 'updated_at' || $columnName === 'deleted_at') {
                    continue;
                }

                $requestColumnName = array_search($columnName, $leadFrontColumnMappings);
    
                // Get the old value from the database
                $oldValue = $oldLeadFrontData->$columnName;
    
                // Get the new value from the request using the mapped column name
                switch($columnName) {
                    case('quantity'):
                    case('price'):
                    case('commission'):
                        $oldValue = number_format($oldValue, 2);
                        $newValue = number_format($data[$requestColumnName], 2) ?? null;
                        break;
                    case('sdm'):
                    case('liquid'):
                        $oldValue = boolval($oldValue);
                        $newValue = $data[$requestColumnName] ?? null;
                        break;
                    case('total'):
                        $oldValue = number_format($oldValue, 2);
                        $newValue = number_format($data['lead_front_quantity'] * $data['lead_front_price'], 2) ?? null;
                        break;
                    case('linked_lead'):
                        $newValue = $oldValue;
                        break;
                    default:
                        $newValue = $data[$requestColumnName] ?? null;
                }
    
                // Check if the value has changed
                if ($newValue !== $oldValue) {
                    $leadFrontChanges[$columnName] = [
                        'id' => $id,
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            $total = $request->lead_front_quantity * $request->lead_front_price;

            $oldLeadFrontData->update([
                'name' => $data['lead_front_name'],
                'product' => $data['lead_front_product'],
                'quantity' => $data['lead_front_quantity'],
                'price' => $data['lead_front_price'],
                'total' => $total,
                'commission' => $data['lead_front_commission'],
                'vc' => $data['lead_front_vc'],
                'sdm' => $data['lead_front_sdm'],
                'liquid' => $data['lead_front_liquid'],
                'mimo' => $data['lead_front_mimo'],
                'bank_name' => $data['lead_front_bank_name'],
                'bank_account' => $data['lead_front_bank_account'],
                'note' => $data['lead_front_note'],
                'linked_lead' => $data['linked_lead'],
                'edited_at' => $data['lead_front_edited_at'],
            ]);
            
            $oldLeadFrontData->save();
        }
        
        if (count($leadFrontChanges) > 0) {
            $newLeadChangelog = new LeadChangelog;

            $newLeadChangelog->lead_id = $data['linked_lead'];
            $newLeadChangelog->column_name = 'lead_front';
            $newLeadChangelog->lead_changes = [];
            $newLeadChangelog->lead_front_changes = $leadFrontChanges;
            $newLeadChangelog->lead_notes_changes = [];
            $newLeadChangelog->description = 'The lead front has been successfully updated';

            $newLeadChangelog->save();
        }
		
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
            $data = $query->with(['lead', 'lead.assignee', 'lead.assignee.site'])
                            ->limit(9000)
                            ->orderByDesc('id')
                            ->get();

            return response()->json($data);
        }

        $data = LeadFront::with(['lead', 'lead.assignee', 'lead.assignee.site'])
                            ->limit(9000)
                            ->orderByDesc('id')
                            ->get();

        return response()->json($data);
    }
    
    public function getLead(string $id)
    {
        $linkedLead = Lead::where('id', $id)->get();

        return response()->json($linkedLead);
    }
    
    public function getLeadList()
    {
        // Fetch leads without associated lead fronts
        $leadsWithoutFronts = Lead::whereDoesntHave('leadfront')
                                    ->whereNull('deleted_at')
                                    ->pluck('id');

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
    public function getLeadFrontChangelogs(string $id)
    {
        $leadFront = LeadFront::find($id);
        
        $existingLeadFrontChangelogs = LeadChangelog::where('lead_id', $leadFront->lead->id)
                                                    ->whereNull('deleted_at')
                                                    ->orderBy('created_at', 'desc')
                                                    ->get();

        $leadFrontChanges = [];
        foreach ($existingLeadFrontChangelogs as $changelog) {
            $changes = $changelog->lead_front_changes;
            if (isset($changes)) {
                foreach ($changes as $change) {
                    $leadFrontChanges[$changelog->id]['lead_changelog_id'] = $changelog->id;
                    $leadFrontChanges[$changelog->id]['description'] = $changelog->description;
                    $leadFrontChanges[$changelog->id]['created_at'] = $changelog->created_at->toDateTimeString();
                    
                    foreach ($changes as $field => $data) {
                        // Check if the 'id' key exists in the change data
                        if (isset($data['id']) && (string)$data['id'] === $id) {
                            $leadFrontChanges[$changelog->id]['lead_front_changes'][$field] = $data; // Collect changes for each lead front
                        }
                    }
                }
            }
        }

        return response()->json($leadFrontChanges);
    }

    public function getTotalLeadFrontCount()
    {
        return response()->json(LeadFront::count());
    }
}
