<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadFrontRequest;
use App\Http\Requests\LeadNotesRequest;
use App\Http\Requests\LeadRequest;
use App\Models\Lead;
use App\Models\LeadFront;
use App\Models\LeadNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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

        // dd($request->lead_notes);
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
        // Fetch total count
        $dataTotalCount = DB::table('leads')->count();

        // Fetch announcements
        $data = DB::table('leads')->get();

        return response()->json($data);
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
