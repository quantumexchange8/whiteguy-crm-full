<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\UserClient;
use App\Http\Requests\UserClientRequest;
use App\Models\User;
use App\Models\UserClientChangelog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

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

    protected $roles = [
        'crm_user' => 'has_crm_access',
        'lead_user' => 'has_leads_access',
        'staff' => 'is_staff',
        'superuser' => 'is_superuser',
    ];

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserClientRequest $request)
    {
        $data = $request->all();
        
        $userClientChanges = [];

        // Insert into users_clients table
        $newUserClientData = User::create([
            'site' => $data['site'],
            'username' => $data['username'],
            'password' => encrypt($data['password']),
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
        
        foreach ($this->roles as $role => $dataKey) {
            if ($data[$dataKey]) {
                $newUserClientData->assignRole($role);
            }
        }

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
        $data = User::find($id);

        $data->password = decrypt($data->password);

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
        
        $userClientChanges = [];

        $oldUserClientData = User::find($id);

        if (isset($oldUserClientData)) {
            foreach ($oldUserClientData->toArray() as $key => $oldValue) {
                if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at' || $key === 'password') {
                    continue;
                }

                if ($key === 'is_active' || $key === 'has_crm_access' || $key === 'has_leads_access' || $key === 'is_staff' || $key === 'is_superuser') {
                    $oldValue = boolval($oldValue);
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
        
        // Insert into users_clients table
        $oldUserClientData->update([
            'site' => $data['site'],
            'username' => $data['username'],
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
        
        $assignedRoles = [];
        
        foreach ($this->roles as $role => $dataKey) {
            if ($request[$dataKey]) {
                $assignedRoles[] = $role;
            }
        }

        // Sync roles
        $oldUserClientData->syncRoles($assignedRoles);

        $oldUserClientData->save();
        
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
     * Update the user's password.
     */
    public function updateUserPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => [
                'required',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                'confirmed'
            ],
        ]);

        $user = User::find($request->id);
        
        $user->update([
            'password' => encrypt($validated['password']),
        ]);
        $user->save();

        $userClientChanges = [];

        // Add the change to the user changes array
        $userClientChanges['Password'] = [
            'description' => 'The user password has been updated',
        ];

        if (count($userClientChanges) > 0) {
            $newUserClientChangelog = new UserClientChangelog;

            $newUserClientChangelog->users_clients_id = $request->id;
            $newUserClientChangelog->column_name = 'users_clients';
            $newUserClientChangelog->changes = $userClientChanges;
            $newUserClientChangelog->description = 'The user has been successfully created';

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
        $existingUserClient = User::find($id);
        $existingUserClient->delete();

        $userClientChanges = [];
        
        $userClientChanges['Delete'] = [
            'id' => $id,
            'description' => 'This user has been deleted',
        ];

        $newUserClientChangelog = new UserClientChangelog;

        $newUserClientChangelog->users_clients_id = $id;
        $newUserClientChangelog->column_name = 'users_clients';
        $newUserClientChangelog->changes = $userClientChanges;
        $newUserClientChangelog->description = 'The user has been successfully created';

        $newUserClientChangelog->save();

        $errorMsgTitle = "You have successfully deleted the user.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('users-clients.index')
                        ->with('errorMsg', $errorMsg);
    }

    public function getUsersClients(Request $request)
    {   
        if ($request['checkedFilters']) {
            $query = DB::table('users')->whereNull('deleted_at');

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

            return response()->json($data);
        }
        $data = DB::table('users')->whereNull('deleted_at')->get();

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
        $existingUser = User::where('account_id', $string)->get();

        if (count($existingUser) > 0) {
            return true;
        }
        
        return false;
    }

    public function getCategories(Request $request)
    {
        $site = DB::table('users')
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

    public function getUserChangelogs(string $id)
    {
        $existingUserClientChangelogs = UserClientChangelog::where('users_clients_id', $id)
                                                ->orderBy('created_at', 'desc')
                                                ->get();

        return response()->json($existingUserClientChangelogs);
    }
    
    public function exportToExcel($selectedRowsData)
    {
        $userClientArr = explode(',', $selectedRowsData);
        foreach ($userClientArr as $key => $value) {
            $userClientArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'user-clients_' . $currentDate . '.xlsx';
        
        return (new UsersExport($userClientArr))->download($exportTitle);
    }
}
