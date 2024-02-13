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
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

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

        if (isset($errors) && isset($errorMsg)) {
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
        // dd($data['lead_notes']);
        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $request->validate($leadFrontRequest->rules());
        }

        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $request->validate($leadNoteRequest->rules());
        }

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
        }

        // dd(count($request->lead_notes) > 0);
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
            }
            $newLeadNoteData->save();
        }
        

		return redirect(route('leads.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $data = Lead::find($id);

        // return Inertia::render('', ['data'=> $data]);
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
        
        // dd($data);

        // Additional validation based on user selection (Lead Front | Lead Notes)
        if ($data['create_lead_front']) {
            $leadFrontRequest = new LeadFrontRequest();
            $request->validate($leadFrontRequest->rules());
        }

        if (count($data['lead_notes']) > 0) {
            $leadNoteRequest = new LeadNotesRequest();
            $request->validate($leadNoteRequest->rules());
        }
        
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

        // need to update / destroy the records when necessary [currently focus on other page]
        if ($data['create_lead_front']) {
            $total = $request->lead_front_quantity * $request->lead_front_price;

            if (isset($data['lead_front_id']) && $data['lead_front_id'] !== ''){
                $existingLeadFront = LeadFront::find($data['lead_front_id']);

                // dd($existingLeadFront);
                
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
            }
        }

        // dd(count($request->lead_notes) > 0);
        if (count($request->lead_notes) > 0) {
            // Loop over array to insert into table
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
                }
            }
        }

		return redirect(route('leads.index'));
    }

    /**
     * Delete this lead.
     */
    public function destroy(string $id)
    {
        // dd($id);
        // $existingLeadFront = LeadNote::find($id);
        // $linkedLead = $existingLeadFront->linked_lead;
        // $existingLeadFront->delete();


        // return redirect(route('leads.edit', $linkedLead));
    }

    /**
     * Delete lead front record that has this lead's id as its linked lead.
     */
    public function deleteLeadFront(string $id)
    {
        // dd($id);
        $existingLeadFront = LeadFront::find($id);
        $linkedLead = $existingLeadFront->linked_lead;
        $existingLeadFront->delete();


        return redirect(route('leads.edit', $linkedLead));
    }

    /**
     * Delete lead note record that has this lead's id as its linked lead.
     */
    public function deleteLeadNote(string $id)
    {
        // dd($id);
        $existingLeadNote = LeadNote::find($id);
        $linkedLead = $existingLeadNote->linked_lead;
        $existingLeadNote->delete();


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
            $query = DB::table('leads');

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
        $data = DB::table('leads')->get();

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
        } else {
            $errorMsgTitle = "You have successfully imported the leads into the system.";
            $errorMsgContent = "Any duplicated leads have been moved to the duplicated leads table. The import errors are as below.";
            $errorMsgType = "success";
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
        ];

        return Redirect::route('leads.index')
                        ->with('errors', $errors)
                        ->with('errorMsg', $errorMsg);
        // return Inertia::render('CRM/Leads/Index', [
        //     'errors' => $errors,
        //     'errorMsg' => $errorMsg
        // ]);
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
}
