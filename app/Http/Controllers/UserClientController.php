<?php

namespace App\Http\Controllers;

use App\Models\UserClient;
use App\Models\LeadChangelog;
use App\Http\Requests\UserClientRequest;
use App\Models\UserClientChangelog;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserClientController extends Controller
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
            return Inertia::render('CRM/UsersClients/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/UsersClients/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/UsersClients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserClientRequest $request)
    {
        $data = $request->all();
        // dd($data);
        
        $userClientChanges = [];

        // Insert into users_clients table
        $newUserClientData = UserClient::create([
            'site' => $data['site'],
            'username' => $data['username'],
            'password' => $data['password'],
            'account_id' => $data['account_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'full_legal_name' => $data['full_legal_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'country_of_citizenship' => $data['country_of_citizenship'],
            'account_holder' => $data['account_holder'],
            'account_type' => $data['account_type'],
            'customer_type' => $data['customer_type'],
            'account_manager' => $data['account_manager'],
            'lead_status' => $data['lead_status'],
            'client_stage' => $data['client_stage'],
            'rank' => $data['rank'],
            'remark' => $data['remark'],
            'previous_broker_name' => $data['previous_broker_name'],
            'kyc_status' => $data['kyc_status'],
            'is_active' => $data['is_active'],
            'has_crm_access' => $data['has_crm_access'],
            'has_leads_access' => $data['has_leads_access'],
            'is_staff' => $data['is_staff'],
            'is_superuser' => $data['is_superuser'],
            'last_login' => $data['last_login'],
        ]);
        $newUserClientData->save();

        // Add the change to the user changes array
        $userClientChanges['New'] = [
            'description' => 'A new user has been created',
        ];

        if (count($userClientChanges) > 0) {
            $newUserClientChangelog = new UserClientChangelog;

            $newUserClientChangelog->users_clients_id = $newUserClientData->id;
            $newUserClientChangelog->column_name = 'users_clients';
            $newUserClientChangelog->changes = $userClientChanges;
            $newUserClientChangelog->description = 'The user has been successfully created';

            $newUserClientChangelog->save();
        }
        
        $errorMsgTitle = "You have successfully created a new user.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('users-clients.index')
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
        $data = UserClient::find($id);

        return Inertia::render('CRM/UsersClients/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserClientRequest $request, string $id)
    {
        $data = $request->all();
        // dd($data);
        
        $userClientChanges = [];

        $oldUserClientData = UserClient::find($id);

        // Insert into users_clients table
        $oldUserClientData->update([
            'site' => $data['site'],
            'username' => $data['username'],
            'password' => $data['password'],
            'account_id' => $data['account_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'full_legal_name' => $data['full_legal_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'country_of_citizenship' => $data['country_of_citizenship'],
            'account_holder' => $data['account_holder'],
            'account_type' => $data['account_type'],
            'customer_type' => $data['customer_type'],
            'account_manager' => $data['account_manager'],
            'lead_status' => $data['lead_status'],
            'client_stage' => $data['client_stage'],
            'rank' => $data['rank'],
            'remark' => $data['remark'],
            'previous_broker_name' => $data['previous_broker_name'],
            'kyc_status' => $data['kyc_status'],
            'is_active' => $data['is_active'],
            'has_crm_access' => $data['has_crm_access'],
            'has_leads_access' => $data['has_leads_access'],
            'is_staff' => $data['is_staff'],
            'is_superuser' => $data['is_superuser'],
            'last_login' => $data['last_login'],
        ]);
        $oldUserClientData->save();

        if (isset($oldUserClientData)) {
            foreach ($oldUserClientData->toArray() as $key => $oldValue) {
                if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at') {
                    continue;
                }
                
                $newValue = $data[$key] ?? null;

                // Check if the value has changed
                if ($newValue !== $oldValue) {
                    $userClientChanges[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        if (count($userClientChanges) > 0) {
            $newUserClientChangelog = new UserClientChangelog;

            $newUserClientChangelog->users_clients_id = $id;
            $newUserClientChangelog->column_name = 'users_clients';
            $newUserClientChangelog->changes = $userClientChanges;
            $newUserClientChangelog->description = 'The user has been successfully updated';

            $newUserClientChangelog->save();
        }
        
        $errorMsgTitle = "You have successfully updated the user details.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('users-clients.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUsersClients()
    {   
        // Fetch announcements
        $data = DB::table('users_clients')->whereNull('deleted_at')->get();

        return response()->json($data);
    }

    // Generate random unique 12 characters account id (Checked against db)
    public function generateAccountId($length = 12) 
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $accountId = '';

        do {
            for ($i = 0; $i < $length; $i++) {
                $accountId .= $characters[rand(0, $charactersLength - 1)];
            }
        } while ($this->checkExistingAccountId($accountId)); // Regenerate if string exists in database

        return response()->json(['account_id' => $accountId]);
    }
    
    public function checkExistingAccountId($string)
    {
        $existingUser = UserClient::where('account_id', $string)->get();

        if (count($existingUser) > 0) {
            return true;
        }
        
        return false;
    }

    public function getCategories(Request $request)
    {
        $site = DB::table('users_clients')
                        ->orderBy('site')
                        ->groupBy('site')
                        ->pluck('site');

        $is_active = [ 0, 1 ];
        $account_type = [ 0, 1 ];
        $client_stage = [ 0, 1 ];
        $rank = [ 0, 1 ];
        $kyc_status = [ 
            "Not started", 
            "Pending documents", 
            "In progress", 
            "Rejected", 
            "Approved" 
        ];
        $has_crm_access = [ 0, 1 ];
        $has_leads_access = [ 0, 1 ];
        $is_staff = [ 0, 1 ];
        $is_superuser = [ 0, 1 ];

        $data = [
            'site' => $site,
            'is_active' => $is_active,
            'account_type' => $account_type,
            'client_stage' => $client_stage,
            'rank' => $rank,
            'kyc_status' => $kyc_status,
            'has_crm_access' => $has_crm_access,
            'has_leads_access' => $has_leads_access,
            'is_staff' => $is_staff,
            'is_superuser' => $is_superuser,
        ];
        
        return response()->json($data);
    }
}
