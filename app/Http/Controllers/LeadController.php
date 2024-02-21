<?php

namespace App\Http\Controllers;

use App\Exports\LeadsExport;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LeadFrontRequest;
use App\Http\Requests\LeadNotesRequest;
use App\Http\Requests\LeadRequest;
use App\Imports\LeadsImport;
use App\Models\LeadFront;
use App\Models\LeadNote;
use App\Models\Lead;
use App\Models\LeadChangelog;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class LeadController extends Controller
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
            return Inertia::render('CRM/Leads/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/Leads/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Leads/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadRequest $request)
    {
        $data = $request->all();
        
        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $request->validate($leadFrontRequest->rules());
        }

        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $request->validate($leadNoteRequest->rules());
        }
        
        // Lead Notes changes
        $leadChanges = [];
        $leadFrontChanges = [];
        $leadNotesChanges = [];
    
        // Insert into table
		$newLeadData = Lead::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'address' => $data['address'],
            'date_oppd_in' => $data['date_oppd_in'],
            'campaign_product' => $data['campaign_product'],
            'sdm' => $data['sdm'],
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'],
            'agents_book' => $data['agents_book'],
            'account_manager' => $data['account_manager'],
            'vc' => $data['vc'],
            'data_type' => $data['data_type'],
            'data_source' => $data['data_source'],
            'data_code' => $data['data_code'],
            'email' => $data['email'],
            'email_alt_1' => $data['email_alt_1'],
            'email_alt_2' => $data['email_alt_2'],
            'email_alt_3' => $data['email_alt_3'],
            'phone_number' => $data['phone_number'],
            'phone_number_alt_1' => $data['phone_number_alt_1'],
            'phone_number_alt_2' => $data['phone_number_alt_2'],
            'phone_number_alt_3' => $data['phone_number_alt_3'],
            'private_remark' => $data['private_remark'],
            'remark' => $data['remark'],
            'appointment_start_at' => $data['appointment_start_at'],
            'appointment_end_at' => $data['appointment_end_at'],
            'last_called' => $data['last_called'],
            'assignee_read_at' => $data['assignee_read_at'],
            'give_up_at' => $data['give_up_at'],
            'appointment_label' => $data['appointment_label'],
            'contact_outcome' => $data['contact_outcome'],
            'stage' => $data['stage'],
            'assignee' => $data['assignee'],
            'created_by' => $data['created_by'],
            'delete_at' => $data['delete_at'],
        ]);
        $newLeadData->save();

        // Add the change to the lead notes changes array
        $leadChanges['New'] = [
            'id' => $newLeadData->id,
            'description' => 'A new lead has been created',
        ];

        if ($data['create_lead_front']) {
            $total = $request->lead_front_quantity * $request->lead_front_price;

            // dd($data);
            // Insert into lead_front table
            $newLeadFrontData = LeadFront::create([
                'name' => $data['lead_front_name'],
                'assignee' => $data['assignee'],
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
                'linked_lead' => $newLeadData->id,
                'edited_at' => $data['lead_front_edited_at'],
            ]);
            $newLeadFrontData->save();

            // Add the change to the lead notes changes array
            $leadFrontChanges['New'] = [
                'id' => $newLeadFrontData->id,
                'description' => 'A new lead front has been created',
            ];
        }

        if (count($request->lead_notes) > 0) {
            // Loop over array to insert into table
            foreach ($request->lead_notes as $key => $value) {
                // Insert into lead_notes table
                $newLeadNoteData = LeadNote::create([
                    'linked_lead' => $newLeadData->id,
                    'note' => $value['note'],
                    'user_editable' => $value['user_editable'],
                    'created_by' => $value['created_by'],
                ]);

                // Add the change to the lead notes changes array
                $leadNotesChanges[$newLeadNoteData->id]['New'] = [
                    'id' => $newLeadNoteData->id,
                    'description' => 'A new lead note has been created',
                ];
            }
            $newLeadNoteData->save();
        }

        if (count($leadChanges) > 0 || count($leadFrontChanges) > 0 || count($leadNotesChanges) > 0) {
            $newLeadChangelog = new LeadChangelog;

            $newLeadChangelog->lead_id = $newLeadData->id;
            $newLeadChangelog->column_name = 'leads';
            $newLeadChangelog->lead_changes = $leadChanges;
            $newLeadChangelog->lead_front_changes = $leadFrontChanges;
            // The lead note's id is placed as the key at the top level
            $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
            $newLeadChangelog->description = 'The lead has been successfully created';

            $newLeadChangelog->save();
        }
        
        $errorMsgTitle = "You have successfully created a new lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
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
        $data = Lead::find($id);
        $data->phone_number = strval($data->phone_number);
        
        $existingLeadFront = DB::table('lead_front')
                                ->join('leads', 'lead_front.linked_lead', '=', 'leads.id')
                                ->where('lead_front.linked_lead', '=', $id)
                                ->where('lead_front.deleted_at', '=', null)
                                ->select('lead_front.*')
                                ->first();
        
        $existingLeadNotes = DB::table('lead_notes')
                                ->join('leads', 'lead_notes.linked_lead', '=', 'leads.id')
                                ->where('lead_notes.linked_lead', '=', $id)
                                ->where('lead_notes.deleted_at', '=', null)
                                ->select('lead_notes.*')
                                ->get();

        foreach($existingLeadNotes as $key=>$value) {
            $existingLeadNotes[$key]->user_editable = boolval($existingLeadNotes[$key]->user_editable);
        }
        
        // dd($existingLeadFront);

		return Inertia::render('CRM/Leads/Edit', [
            'data' => $data, 
            'leadFrontData' => $existingLeadFront,
            'leadNotesData' => $existingLeadNotes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadRequest $request, string $id)
    {
        $data = $request->all();

        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $request->validate($leadFrontRequest->rules());
        }

        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $request->validate($leadNoteRequest->rules());
        }
        
        // Lead changes
        $leadChanges = [];
        $oldLeadData = Lead::find($id);

        // Loop through existing data across lead, lead front, and lead notes and compare against the request to detect any changes
        // Only log found changes into lead changelog table
        if (isset($oldLeadData)) {
            foreach ($oldLeadData->toArray() as $key => $oldValue) {
                if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at') {
                    continue;
                }
                
                $newValue = $data[$key] ?? null;

                // Check if the value has changed
                if ($newValue !== $oldValue) {
                    $leadChanges[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        // Lead Front changes
        $leadFrontChanges = [];
        $oldLeadFrontData = LeadFront::where('linked_lead', $id)->get();

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

        if (isset($oldLeadFrontData) && count($oldLeadFrontData) > 0) {
            foreach ($leadFrontColumns as $columnName) {
                // Skip 'created_at' and 'updated_at' columns
                if ($columnName === 'id' || $columnName === 'created_at' || $columnName === 'updated_at' || $columnName === 'deleted_at') {
                    continue;
                }

                $requestColumnName = array_search($columnName, $leadFrontColumnMappings);
    
                
                // Get the old value from the database
                $oldValue = $oldLeadFrontData[0]->$columnName;
    
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
                    case('assignee'):
                        $newValue = $data['assignee'];
                        break;
                    case('total'):
                        $oldValue = number_format($oldValue, 2);
                        $newValue = number_format($data['lead_front_quantity'] * $data['lead_front_price'], 2) ?? null;
                        break;
                    case('linked_lead'):
                        $newValue = $id;
                        break;
                    default:
                        $newValue = $data[$requestColumnName] ?? null;
                }
    
                // Check if the value has changed
                if ($newValue !== $oldValue) {
                    $leadFrontChanges[$columnName] = [
                        'id' => $data['lead_front_id'],
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        // Lead Notes changes
        $leadNotesChanges = [];
        $oldLeadNotesData = LeadNote::where('linked_lead', $id)->get();

        // Check if lead notes data exists in the request
        if (isset($data['lead_notes']) && is_array($data['lead_notes'])) {
            // Retrieve old lead notes data from the database
            $oldLeadNotesData = LeadNote::where('linked_lead', $id)->get();

            // Iterate over each old lead note
            foreach ($oldLeadNotesData as $oldLeadNote) {
                // Get the ID of the lead note
                $leadNoteId = $oldLeadNote->id;

                // Find the corresponding lead note data in the request
                $newLeadNoteData = collect($data['lead_notes'])->firstWhere('id', $leadNoteId);

                // If lead note data exists in the request
                if ($newLeadNoteData) {
                    // Iterate over each attribute of the lead note
                    foreach ($newLeadNoteData as $key => $newValue) {
                        // Skip 'id', 'linked_lead', 'created_at', 'updated_at', and 'deleted_at' columns
                        if (in_array($key, ['id', 'linked_lead', 'created_at', 'updated_at', 'deleted_at'])) {
                            continue;
                        }

                        // Retrieve the corresponding old value from the database
                        $oldValue = ($key === 'user_editable') ? boolval( $oldLeadNote->{$key}) : $oldLeadNote->{$key};
                        // $oldValue = $oldLeadNote->{$key};

                        // Check if the value has changed
                        if ($newValue !== $oldValue) {
                            // Add the change to the lead notes changes array
                            $leadNotesChanges[$leadNoteId][$key] = [
                                'id' => $oldLeadNote->id,
                                'old' => $oldValue,
                                'new' => $newValue,
                            ];
                        }
                    }
                }
            }
        }
        
        $updateActionCount = 0;
        
        $existingLead = Lead::find($id);
        
        $existingLead->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'address' => $data['address'],
            'date_oppd_in' => $data['date_oppd_in'],
            'campaign_product' => $data['campaign_product'],
            'sdm' => $data['sdm'],
            'date_of_birth' => $data['date_of_birth'],
            'occupation' => $data['occupation'],
            'agents_book' => $data['agents_book'],
            'account_manager' => $data['account_manager'],
            'vc' => $data['vc'],
            'data_type' => $data['data_type'],
            'data_source' => $data['data_source'],
            'data_code' => $data['data_code'],
            'email' => $data['email'],
            'email_alt_1' => $data['email_alt_1'],
            'email_alt_2' => $data['email_alt_2'],
            'email_alt_3' => $data['email_alt_3'],
            'phone_number' => $data['phone_number'],
            'phone_number_alt_1' => $data['phone_number_alt_1'],
            'phone_number_alt_2' => $data['phone_number_alt_2'],
            'phone_number_alt_3' => $data['phone_number_alt_3'],
            'private_remark' => $data['private_remark'],
            'remark' => $data['remark'],
            'appointment_start_at' => $data['appointment_start_at'],
            'appointment_end_at' => $data['appointment_end_at'],
            'last_called' => $data['last_called'],
            'assignee_read_at' => $data['assignee_read_at'],
            'give_up_at' => $data['give_up_at'],
            'appointment_label' => $data['appointment_label'],
            'contact_outcome' => $data['contact_outcome'],
            'stage' => $data['stage'],
            'assignee' => $data['assignee'],
            'created_by' => $data['created_by'],
            'delete_at' => $data['delete_at'],
		]);
        $updateActionCount++;

        // Update existing lead front or insert a new lead front
        if ($data['create_lead_front']) {
            $total = $request->lead_front_quantity * $request->lead_front_price;

            if (isset($data['lead_front_id']) && $data['lead_front_id'] !== ''){
                $existingLeadFront = LeadFront::find($data['lead_front_id']);

                if (isset($existingLeadFront)) {
                    $existingLeadFront->update([
                        'name' => $data['lead_front_name'],
                        'assignee' => $data['assignee'],
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
                        'linked_lead' => $id,
                        'edited_at' => $data['lead_front_edited_at'],
                    ]);
                    $updateActionCount++;
                }

            } else {
                // Insert into lead_front table
                $newLeadFront = LeadFront::create([
                    'name' => $data['lead_front_name'],
                    'assignee' => $data['assignee'],
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
                    'linked_lead' => $id,
                    'edited_at' => $data['lead_front_edited_at'],
                ]);
                $newLeadFront->save();

                // Add the change to the lead notes changes array
                $leadFrontChanges['New'] = [
                    'id' => $newLeadFront->id,
                    'description' => 'A new lead front has been created',
                ];
            }
        }

        if (count($request->lead_notes) > 0) {
            foreach ($request->lead_notes as $key => $value) {
                if (isset($value['id'])) {
                    $existingLeadNotes = LeadNote::find($value['id']);
            
                    $existingLeadNotes->update([
                        'linked_lead' => $id,
                        'note' => $value['note'],
                        'user_editable' => $value['user_editable'],
                        'created_by' => $value['created_by'],
                    ]);
                } else {
                    // Insert into lead_notes table
                    $newLeadNote = LeadNote::create([
                        'linked_lead' => $id,
                        'note' => $value['note'],
                        'user_editable' => $value['user_editable'],
                        'created_by' => $value['created_by'],
                    ]);
                    $newLeadNote->save();

                    // Add the change to the lead notes changes array
                    $leadNotesChanges[$newLeadNote->id]['New'] = [
                        'id' => $newLeadNote->id,
                        'description' => 'A new lead note has been created',
                    ];
                }
                $updateActionCount++;
            }
        }

        if ($updateActionCount > 0) {
            if (count($leadChanges) > 0 || count($leadFrontChanges) > 0 || count($leadNotesChanges) > 0) {
                $newLeadChangelog = new LeadChangelog;

                $newLeadChangelog->lead_id = $id;
                $newLeadChangelog->column_name = 'leads';
                $newLeadChangelog->lead_changes = $leadChanges;
                $newLeadChangelog->lead_front_changes = $leadFrontChanges;
                // The lead note's id is placed as the key at the top level
                $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
                $newLeadChangelog->description = 'The lead has been successfully updated';

                $newLeadChangelog->save();
            }
        }
		
        $errorMsgTitle = "You have successfully updated the lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Delete this lead.
     */
    public function destroy(string $id)
    {
        $existingLead = Lead::find($id);
        $existingLead->delete();

        $leadChanges = [];
        // Add the change to the lead notes changes array
        $leadChanges['Delete'] = [
            'id' => $id,
            'description' => 'This lead has been deleted',
        ];

        $newLeadChangelog = new LeadChangelog;

        $newLeadChangelog->lead_id = $id;
        $newLeadChangelog->column_name = 'leads';
        $newLeadChangelog->lead_changes = $leadChanges;
        $newLeadChangelog->lead_front_changes = [];
        $newLeadChangelog->lead_notes_changes = [];
        $newLeadChangelog->description = 'This lead has been deleted';

        $newLeadChangelog->save();

        $errorMsgTitle = "You have successfully deleted the lead.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('leads.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Delete lead front record that has this lead's id as its linked lead.
     */
    public function deleteLeadFront(string $id)
    {
        $existingLeadFront = LeadFront::find($id);
        $linkedLead = $existingLeadFront->linked_lead;
        $existingLeadFront->delete();

        $leadFrontChanges = [];
        // Add the change to the lead notes changes array
        $leadFrontChanges['Delete'] = [
            'id' => $id,
            'description' => 'This lead front has been deleted',
        ];

        $newLeadChangelog = new LeadChangelog;

        $newLeadChangelog->lead_id = $linkedLead;
        $newLeadChangelog->column_name = 'leads';
        $newLeadChangelog->lead_changes = [];
        $newLeadChangelog->lead_front_changes = $leadFrontChanges;
        $newLeadChangelog->lead_notes_changes = [];
        $newLeadChangelog->description = 'This lead front has been deleted';

        $newLeadChangelog->save();

        return redirect(route('leads.edit', $linkedLead));
    }

    /**
     * Delete lead note record that has this lead's id as its linked lead.
     */
    public function deleteLeadNote(string $id)
    {
        $existingLeadNote = LeadNote::find($id);
        $linkedLead = $existingLeadNote->linked_lead;
        $existingLeadNote->delete();

        $leadNotesChanges = [];
        // Add the change to the lead notes changes array
        $leadNotesChanges[$id]['Delete'] = [
            'id' => $id,
            'description' => 'This lead note has been deleted',
        ];

        $newLeadChangelog = new LeadChangelog;

        $newLeadChangelog->lead_id = $linkedLead;
        $newLeadChangelog->column_name = 'leads';
        $newLeadChangelog->lead_changes = [];
        $newLeadChangelog->lead_front_changes = [];
        $newLeadChangelog->lead_notes_changes = $leadNotesChanges;
        $newLeadChangelog->description = 'This lead note has been deleted';

        $newLeadChangelog->save();


        return redirect(route('leads.edit', $linkedLead));
    }

    public function getLeads(Request $request)
    {
        // $data = $request->all();
        // $datas = $request['checkedFilters']['contact_outcome'][0];

        if ($request['checkedFilters']) {
            // // Fetch filtered leads based on array
            // if (is_array($request->category_name)) {
            //     $data = Lead::whereIn($request->category, $request->category_name)
            //                     ->get();
                                
            //     return response()->json($data);
            // }

            // // Fetch filtered leads
            // if ($request->category === 'last_called' || $request->category === 'give_up_at') {
            //     switch($request->category_name) {
            //         case('Today'):
            //             $data = Lead::whereBetween($request->category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()])
            //                             ->get();
                        
            //             // $date = Carbon::now()->subDays(7)->toDateTimeString();
            //             break;
            //         case('Past 7 days'):
            //             $data = Lead::whereBetween($request->category, [Carbon::today()->subDays(7)->toDateTimeString(), Carbon::today()->toDateTimeString()])
            //                             ->get();
                        
            //             break;
            //         case('This month'):
            //             $data = DB::table('leads')
            //                         ->whereMonth($request->category, Carbon::today()->month)
            //                         ->get();
                        
            //             break;
            //         case('This year'):
            //             $data = DB::table('leads')
            //                         ->whereYear($request->category, Carbon::today()->year)
            //                         ->get();
                        
            //             break;
            //         case('No date'):
            //             $data = DB::table('leads')
            //                         ->whereNull($request->category)
            //                         ->get();
                        
            //             break;
            //         case('Has date'):
            //             $data = DB::table('leads')
            //                         ->whereNotNull($request->category)
            //                         ->get();
                        
            //             break;
            //         default:
            //             $data = Lead::whereBetween($request->category, [Carbon::today()->toDateTimeString(), Carbon::today()->addHours(24)->toDateTimeString()])
            //                         ->get();
            //     }
            //     return response()->json($data);
            // }

            // $data = DB::table('leads')
            //             ->where($request->category, '=', $request->category_name)
            //             ->get();
            $query = DB::table('leads')->whereNull('deleted_at');

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $query->whereIn($category, $options);
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
            $data = $query->get();
            
            // if(isset($request['checkedFilters']['contact_outcome']) && count($request['checkedFilters']['contact_outcome']) > 0) {
            //     $data = DB::table('leads')
            //             ->whereIn('contact_outcome', $request['checkedFilters']['contact_outcome'])
            //             ->whereIn('assignee', $request['checkedFilters']['assignee'])
            //             ->get();

            // } else {
            //     $data = DB::table('leads')->get();
            // }

            return response()->json($data);
        }

        // Default fetch all on load
        $data = DB::table('leads')->whereNull('deleted_at')->get();

        return response()->json($data);
    }

    public function getDuplicatedLeads(Request $request)
    {
        // Fetch all duplicated leads
        $data = DB::table('duplicated_leads')->whereNull('deleted_at')->get();

        return response()->json($data);
    }

    public function getCategories(Request $request)
    {
        $contact_outcome = [ 
            "Disconnected Line", 
            "Voicemail", 
            "No Answer", 
            "W/N", 
            "Line not connecting", 
            "H/U", 
            "HU/DC", 
            "N/I", 
            "Deceased", 
            "Left company", 
            "Gate Keeper", 
            "HU/NI", 
            "Voip Blocker", 
            "Front", 
            "Call Back", 
            "DEAL!", 
            "Misc", 
            "Voicemail (Ring)", 
            "Not speak English", 
            "Dup Batch" ];

        $stage = [ "Contact Made", "HTR", "Failed Close", "Front (Front Form)", "New Account (Sale Order Form)" ];

        $last_called = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $give_up_at = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        // Fetch filtered announcements
        $assignee = DB::table('leads')
                        ->orderBy('assignee')
                        ->groupBy('assignee')
                        ->pluck('assignee');

        $data = [
            'contact_outcome' => $contact_outcome,
            'stage' => $stage,
            'last_called' => $last_called,
            'give_up_at' => $give_up_at,
            'assignee' => $assignee,
        ];
        
        return response()->json($data);
    }

    public function getLeadFront(string $id)
    {
        $existingLeadFront = LeadFront::where('linked_lead', $id)->get();

        return response()->json($existingLeadFront);
    }

    public function getLeadNotes(string $id)
    {
        $existingLeadNotes = LeadNote::where('linked_lead', $id)
                                        ->orderBy('created_at', 'desc')
                                        ->get()
                                        ->map(function ($note) {
                                            $note['source'] = 'lead_note';
                                            return $note;
                                        });

        foreach($existingLeadNotes as $key=>$value) {
            $existingLeadNotes[$key]->user_editable = boolval($existingLeadNotes[$key]->user_editable);
        }

        return response()->json($existingLeadNotes);
    }

    public function getLeadChangelogs(string $id)
    {
        $existingLeadChangelogs = LeadChangelog::where('lead_id', $id)
                                                ->orderBy('created_at', 'desc')
                                                ->get()
                                                ->map(function ($changelog) {
                                                    $changelog['source'] = 'lead_changelog';
                                                    return $changelog;
                                                });

        return response()->json($existingLeadChangelogs);
    }

    public function exportToExcel($leads)
    {
        $leadsArr = explode(',', $leads);
        foreach ($leadsArr as $key => $value) {
            $leadsArr[$key] = (int)$value;
        }
        
        return (new LeadsExport($leadsArr))->download('leads.xlsx');
    }

    public function importExcel(Request $request)
    {
        $file = $request->file('leadExcelFile');
        $import = new LeadsImport();
        $import->import($file);
        
        $errors = [];
        $duplicatedLeads = [];
        
        foreach ($import->failures() as $failure) {
            $rowErrors = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];

            array_push($errors, $rowErrors);
        }

        // $errorMsgTitle = "";
        // $errorMsgContent = "";

        // dd($errors, $duplicatedLeads);

        // Set error messages to be displayed based on whether there are any errors
        if (count($errors) > 0) {
            $errorMsgTitle = "You have partially imported the leads into the system.";
            $errorMsgContent = "There are rows that have not been inputted correctly or filled completely. Any duplicated leads have been moved to the duplicated leads table. The import errors are as below.";
            $errorMsgType = "error";
            $rowErrorMsg = true;
        } else {
            $errorMsgTitle = "You have successfully imported the leads into the system.";
            $errorMsgContent = "Any duplicated leads have been moved to the duplicated leads table. The import errors are as below.";
            $errorMsgType = "success";
            $rowErrorMsg = false;
        }

        // Excel::import(new LeadsImport, $file);
        // try {
        //     $import->import($file);
        // } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        //     $failures = $e->failures();
            
        //     foreach ($failures as $failure) {
        //         $rowErrors = [
        //             'row' => $failure->row(),
        //             'attribute' => $failure->attribute(),
        //             'errors' => $failure->errors(),
        //             'values' => $failure->values(),
        //         ];

        //         array_push($errors, $rowErrors);
        //         if ($failure->attribute() === 'Email' || $failure->attribute() === 'Phone Number') {
        //             if ($failure->errors() === "The Email has already been taken." || $failure->errors() === "The Phone Number has already been taken.") {

        //            }
        //         }
        //     }
        // }

        // dd($errors);

        $errorMsg = [
            'title' => $errorMsgTitle,
            'content' => $errorMsgContent,
            'type' => $errorMsgType,
            'rowErrorMsg' => $rowErrorMsg,
        ];

        return Redirect::route('leads.index')
                        ->with('errors', $errors)
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Restore the lead if needed. Currently not put to use anywhere.
     */
    public function restore(string $id)
    {
        $softDeletedLead = Lead::withTrashed()->find($id);

        $softDeletedLead->restore();

        return redirect(route('leads.index'));
    }

    public function clearSessionMessages(Request $request)
    {
        // Clear the flashed messages from the session
        $request->session()->forget('errors');
        $request->session()->forget('errorMsg');
        
        return redirect(route('leads.index'));
    }
}
