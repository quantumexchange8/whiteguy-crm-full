<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Site;
use App\Models\UserClient;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\UserClientChangelog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UserClientRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class UserClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $accManager = User::with(['accountManager:id,username,site_id', 'accountManager.site:id,name'])
        //                     ->limit(10)
        //                     ->orderByDesc('id')
        //                     ->get();

        // dd($accManager);

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
        // $userClientChanges = [];

        // Insert into users_clients table
        $newUserClientData = User::create([
            'password' => $data['password'],
            'last_login' => $data['last_login'],
            'is_superuser' => $data['is_superuser'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'is_staff' => $data['is_staff'],
            'is_active' => $data['is_active'],
            'date_joined' => $data['date_joined'],
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'profile_picture' => $data['profile_picture'] ?? '',
            'is_email_verified' => $data['is_email_verified'],
            'timezone' => $data['timezone'],
            'country' => $data['country'],
            'address' => $data['address'],
            'account_type' => $data['account_type'],
            'account_holder' => $data['account_holder'],
            'customer_type' => $data['customer_type'],
            'rank' => $data['rank'],
            'remark' => $data['remark'],
            'wallet_balance' => $data['wallet_balance'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['date_joined']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['date_joined']),
            'account_manager_id' => $data['account_manager_id'],
            'site_id' => $data['site_id'],
            'has_crm_access' => $data['has_crm_access'],
            'lead_status' => $data['lead_status'],
            'client_stage' => $data['client_stage'],
            'has_leads_access' => $data['has_leads_access'],
            'identification_document_1' => $data['identification_document_1'] ?? '',
            'identification_document_2' => $data['identification_document_2'] ?? '',
            'identification_document_3' => $data['identification_document_3'] ?? '',
            'kyc_status' => $data['kyc_status'],
            'proof_of_address_document_1' => $data['proof_of_address_document_1'] ?? '',
            'proof_of_address_document_2' => $data['proof_of_address_document_2'] ?? '',
            'account_id' => $data['previous_broker_name'],
            'previous_broker_name' => $data['account_id'],
        ]);
        
        // foreach ($this->roles as $role => $dataKey) {
        //     if ($data[$dataKey]) {
        //         $newUserClientData->assignRole($role);
        //     }
        // }

        $newUserClientData->save();

        // Add the change to the user changes array
        // $userClientChanges['New'] = [
        //     'description' => 'A new user has been created',
        // ];

        // if (count($userClientChanges) > 0) {
        //     $newUserClientChangelog = new UserClientChangelog;

        //     $newUserClientChangelog->users_clients_id = $newUserClientData->id;
        //     $newUserClientChangelog->column_name = 'users_clients';
        //     $newUserClientChangelog->changes = $userClientChanges;
        //     $newUserClientChangelog->description = 'The user has been successfully created';

        //     $newUserClientChangelog->save();
        // }
        
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
        $data = User::with(['site', 'orders'])
                        ->find($id);

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
        
        // $data['date_joined'] = preg_replace('/(\d{2})(\d{2})$/', '$1', $data['date_joined']);
        // $userClientChanges = [];

        $oldUserClientData = User::find($id);

        // if (isset($oldUserClientData)) {
        //     foreach ($oldUserClientData->toArray() as $key => $oldValue) {
        //         if ($key === 'created_at' || $key === 'updated_at' || $key === 'deleted_at' || $key === 'password') {
        //             continue;
        //         }

        //         if ($key === 'is_active' || $key === 'has_crm_access' || $key === 'has_leads_access' || $key === 'is_staff' || $key === 'is_superuser') {
        //             $oldValue = boolval($oldValue);
        //         }
                
        //         $newValue = $data[$key] ?? null;

        //         // Check if the value has changed
        //         if ($newValue !== $oldValue) {
        //             $userClientChanges[$key] = [
        //                 'old' => $oldValue,
        //                 'new' => $newValue,
        //             ];
        //         }
        //     }
        // }
        // dd($data);
        
        // Insert into users_clients table
        $oldUserClientData->update([
            'password' => $data['password'],
            'last_login' => $data['last_login'],
            'is_superuser' => $data['is_superuser'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'is_staff' => $data['is_staff'],
            'is_active' => $data['is_active'],
            'date_joined' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['date_joined']),
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'profile_picture' => $data['profile_picture'] ?? '',
            'is_email_verified' => $data['is_email_verified'],
            'timezone' => $data['timezone'],
            'country' => $data['country'],
            'address' => $data['address'],
            'account_type' => $data['account_type'],
            'account_holder' => $data['account_holder'],
            'customer_type' => $data['customer_type'],
            'rank' => $data['rank'],
            'remark' => $data['remark'],
            'wallet_balance' => $data['wallet_balance'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'account_manager_id' => $data['account_manager_id'],
            'site_id' => $data['site_id'],
            'has_crm_access' => $data['has_crm_access'],
            'lead_status' => $data['lead_status'],
            'client_stage' => $data['client_stage'],
            'has_leads_access' => $data['has_leads_access'],
            'identification_document_1' => $data['identification_document_1'] ?? '',
            'identification_document_2' => $data['identification_document_2'] ?? '',
            'identification_document_3' => $data['identification_document_3'] ?? '',
            'kyc_status' => $data['kyc_status'],
            'proof_of_address_document_1' => $data['proof_of_address_document_1'] ?? '',
            'proof_of_address_document_2' => $data['proof_of_address_document_2'] ?? '',
            'account_id' => $data['account_id'],
            'previous_broker_name' => $data['previous_broker_name'],
        ]);
        
        // $assignedRoles = [];
        
        // foreach ($this->roles as $role => $dataKey) {
        //     if ($request[$dataKey]) {
        //         $assignedRoles[] = $role;
        //     }
        // }

        // Sync roles
        // $oldUserClientData->syncRoles($assignedRoles);

        $oldUserClientData->save();
        
        // if (count($userClientChanges) > 0) {
        //     $newUserClientChangelog = new UserClientChangelog;

        //     $newUserClientChangelog->users_clients_id = $id;
        //     $newUserClientChangelog->column_name = 'users_clients';
        //     $newUserClientChangelog->changes = $userClientChanges;
        //     $newUserClientChangelog->description = 'The user has been successfully updated';

        //     $newUserClientChangelog->save();
        // }
        
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
            'password' => $validated['password'],
        ]);
        $user->save();

        // $userClientChanges = [];

        // // Add the change to the user changes array
        // $userClientChanges['Password'] = [
        //     'description' => 'The user password has been updated',
        // ];

        // if (count($userClientChanges) > 0) {
        //     $newUserClientChangelog = new UserClientChangelog;

        //     $newUserClientChangelog->users_clients_id = $request->id;
        //     $newUserClientChangelog->column_name = 'users_clients';
        //     $newUserClientChangelog->changes = $userClientChanges;
        //     $newUserClientChangelog->description = 'The user has been successfully created';

        //     $newUserClientChangelog->save();
        // }
        
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
        $newUserClientChangelog->description = 'The user has been successfully deleted';

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
            $query = User::query();

            foreach ($request['checkedFilters'] as $category => $options) {
                if (is_array($options) && count($options) > 0) {
                    $tempArray = [];
                    foreach ($options as $value) {
                        array_push($tempArray, (($value === "true" || $value === "false") ? $value : (int)$value));
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
            
            // Column-specific searches
            if (isset($request['params']['column_filters'])) {
                foreach ($request['params']['column_filters'] as $filter) {
                    if (!empty($filter['field'])) {
                        if ($filter['field'] === 'client_stage' || $filter['field'] === 'rank' || $filter['field'] === 'kyc_status' || $filter['field'] === 'account_manager_id') {
                            if (isset($filter['value'])) {
                                $filteredId = User::where($filter['field'], 'LIKE', '%' . $filter['value'] . '%')
                                                        ->select('id')
                                                        ->get();

                                $filteredIdArr = [];
                                foreach ($filteredId as $key => $value) {
                                    array_push($filteredIdArr, $value->id);
                                }
                            }

                            if ($filter['condition'] === 'not_contain' && isset($filteredId)) {
                                $query->where($filter['field'], 'NOT LIKE', '%' . $filteredId->id . '%');
                            } elseif ($filter['condition'] === 'equal' && isset($filteredId)) {
                                $query->where($filter['field'], $filteredId->id);
                            } elseif ($filter['condition'] === 'not_equal' && isset($filteredId)) {
                                $query->whereNot($filter['field'], $filteredId->id);
                            } elseif ($filter['condition'] === 'start_with' && isset($filteredId)) {
                                $query->where($filter['field'], 'LIKE', $filteredId->id . '%');
                            } elseif ($filter['condition'] === 'end_with' && isset($filteredId)) {
                                $query->where($filter['field'], 'LIKE', '%' . $filteredId->id);
                            } elseif ($filter['condition'] === 'is_null') {
                                $query->whereNull($filter['field']);
                            } elseif ($filter['condition'] === 'is_not_null') {
                                $query->whereNotNull($filter['field']);
                            } elseif ($filter['condition'] === 'contain' && isset($filteredId)) {
                                $query->whereIn($filter['field'], $filteredIdArr);
                            }
                        } else {
                            if ($filter['condition'] === 'not_contain' && isset($filter['value'])) {
                                $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
                            } elseif ($filter['condition'] === 'equal' && isset($filter['value'])) {
                                $query->where($filter['field'], $filter['value']);
                            } elseif ($filter['condition'] === 'not_equal' && isset($filter['value'])) {
                                $query->whereNot($filter['field'], $filter['value']);
                            } elseif ($filter['condition'] === 'start_with' && isset($filter['value'])) {
                                $query->where($filter['field'], 'LIKE', $filter['value'] . '%');
                            } elseif ($filter['condition'] === 'end_with' && isset($filter['value'])) {
                                $query->where($filter['field'], 'LIKE', '%' . $filter['value']);
                            } elseif ($filter['condition'] === 'greater_than' && isset($filter['value'])) {
                                $query->where($filter['field'], '>', $filter['value']);
                            } elseif ($filter['condition'] === 'greater_than_equal' && isset($filter['value'])) {
                                $query->where($filter['field'], '>=', $filter['value']);
                            } elseif ($filter['condition'] === 'less_than' && isset($filter['value'])) {
                                $query->where($filter['field'], '<', $filter['value']);
                            } elseif ($filter['condition'] === 'less_than_equal' && isset($filter['value'])) {
                                $query->where($filter['field'], '<=', $filter['value']);
                            } elseif ($filter['condition'] === 'is_null') {
                                $query->whereNull($filter['field']);
                            } elseif ($filter['condition'] === 'is_not_null') {
                                $query->whereNotNull($filter['field']);
                            } elseif ($filter['condition'] === 'contain' && isset($filter['value'])) {
                                $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                            }
                        }
                    }
                }
            }
                                    
            $tableColumns = Schema::getColumnListing('core_user');
            $sort_column = '';
    
            if ($request['params']['sort_column'] === 'actions') {
                $sort_column = 'id';
            }

            $data = $query->with(['site', 'accountManager:id,username,site_id', 'accountManager.site:id,name'])
                            ->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction'])
                            ->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);
        
            $ranks =[ "Normal", "VIP" ];
            $client_stages = [ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ];
            $kyc_statuses = [ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ];
            $acc_types = [ "Individual", "Joint", "Trust", "Corporate" ];
    
            foreach ($data as $user) {
                // Handle client_stage attribute
                if (!is_null($user->client_stage) && $user->client_stage !== '') {
                    $user->client_stage = $client_stages[$user->client_stage - 1];
                }
            
                // Handle rank attribute
                if (!is_null($user->rank) && $user->rank !== '') {
                    $user->rank = $ranks[$user->rank - 1];
                }
                
                // Handle kyc_status attribute
                if (!is_null($user->kyc_status) && $user->kyc_status !== '') {
                    $user->kyc_status = $kyc_statuses[$user->kyc_status - 1];
                }
                
                // Handle account_type attribute
                if (!is_null($user->account_type) && $user->account_type !== '') {
                    $user->account_type = $acc_types[$user->account_type - 1];
                }
    
                // Handle account_manager_id attribute
                if (!is_null($user->account_manager_id) && $user->account_manager_id !== '') {
                    // $accManager = User::find($user->account_manager_id);
                    $user->account_manager_id = $user->accountManager->username . " (" . $user->accountManager->site->name . ")";
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

        if ($request['params']['sort_column'] === 'actions') {
            $sort_column = 'id';
        }

        $queries = User::query();
        $queries->with(['site', 'accountManager:id,username,site_id', 'accountManager.site:id,name']);
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
                                    // if (!empty($filter['field'])) {
                                        // if ($filter['field'] === 'client_stage' || $filter['field'] === 'rank' || $filter['field'] === 'kyc_status' || $filter['field'] === 'account_manager_id') {
                                            if (isset($filter['value'])) {
                                                if ($filter['field'] === 'account_manager_id') {
                                                    $filteredId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                                            ->select('id')
                                                                            ->get();
                                                }
                                                
                                                if ($filter['field'] === 'client_stage' || $filter['field'] === 'rank' || $filter['field'] === 'kyc_status') {
                                                    $fieldValArr = [];
                                                    switch ($filter['field']) {
                                                        case 'rank':
                                                            $fieldValArr = [ "Normal", "VIP" ];
                                                            break;
                                                        case 'client_stage':
                                                            $fieldValArr = [ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ];
                                                            break;
                                                        case 'kyc_status':
                                                            $fieldValArr = [ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ];
                                                            break;
                                                    }

                                                    $filteredId = [];

                                                    foreach ($fieldValArr as $index => $fieldVal) {
                                                        if (str_contains($fieldVal, $filter['value']) !== false) {
                                                            array_push($filteredId, $index + 1);    
                                                        }
                                                    }
                                                    // dd($filteredId);
                                                }
                                                
                                                if (isset($filteredId)) {
                                                    $filteredIdArr = [];
                                                    foreach ($filteredId as $key => $value) {
                                                        array_push($filteredIdArr, ($filter['field'] === 'account_manager_id') ? $value->id : $value);
                                                    }
                                                }
                                                // dd($filteredIdArr);

                                                if ($filter['condition'] === 'not_contain') {
                                                    if (isset($filteredId)) {
                                                        $query->whereNotIn($filter['field'], $filteredIdArr);
                                                    } else {
                                                        $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');
                                                    }
                                                } elseif ($filter['condition'] === 'equal') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] === $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'not_equal') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] !== $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->whereNot($filter['field'], $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'start_with') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if (str_starts_with($fieldVal, $filter['value'])) {
                                                                array_push($tempArr, $index + 1);
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], 'LIKE', $filter['value'] . '%');
                                                    }
                                                } elseif ($filter['condition'] === 'end_with') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if (str_ends_with($fieldVal, $filter['value'])) {
                                                                array_push($tempArr, $index + 1);
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], 'LIKE', '%' . $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'greater_than') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] < $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], '>', $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'greater_than_equal') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] <= $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], '>=', $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'less_than') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] > $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], '<', $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'less_than_equal') {
                                                    if (isset($filteredId)) {
                                                        $tempArr = [];
                                                        foreach ($fieldValArr as $index => $fieldVal) {
                                                            if ($filter['value'] >= $fieldVal) {
                                                                array_push($tempArr, $index + 1);    
                                                            }
                                                        }
                                                        $query->whereIn($filter['field'], $tempArr);
                                                    } else {
                                                        $query->where($filter['field'], '<=', $filter['value']);
                                                    }
                                                } elseif ($filter['condition'] === 'contain') {
                                                    // dd($query->whereIn($filter['field'], $filteredIdArr)->paginate());
                                                    if (isset($filteredId)) {
                                                        $query->whereIn($filter['field'], $filteredIdArr);
                                                    } else {
                                                        $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                                                    }
                                                }
                                            }
                

                                            if ($filter['condition'] === 'is_null') {
                                                $query->orWhereNull($filter['field']);
                                            } elseif ($filter['condition'] === 'is_not_null') {
                                                $query->orWhereNotNull($filter['field']);
                                            }

                                        // } else {
                                        //     if ($filter['condition'] === 'not_contain' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], 'NOT LIKE', '%' . $filter['value'] . '%');

                                        //     } elseif ($filter['condition'] === 'equal' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], $filter['value']);

                                        //     } elseif ($filter['condition'] === 'not_equal' && isset($filter['value'])) {
                                        //         $query->whereNot($filter['field'], $filter['value']);

                                        //     } elseif ($filter['condition'] === 'start_with' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], 'LIKE', $filter['value'] . '%');

                                        //     } elseif ($filter['condition'] === 'end_with' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], 'LIKE', '%' . $filter['value']);

                                        //     } elseif ($filter['condition'] === 'greater_than' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], '>', $filter['value']);

                                        //     } elseif ($filter['condition'] === 'greater_than_equal' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], '>=', $filter['value']);
                                        
                                        //     } elseif ($filter['condition'] === 'less_than' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], '<', $filter['value']);

                                        //     } elseif ($filter['condition'] === 'less_than_equal' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], '<=', $filter['value']);

                                        //     } elseif ($filter['condition'] === 'is_null') {
                                        //         $query->whereNull($filter['field']);

                                        //     } elseif ($filter['condition'] === 'is_not_null') {
                                        //         $query->whereNotNull($filter['field']);

                                        //     } elseif ($filter['condition'] === 'contain' && isset($filter['value'])) {
                                        //         $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                                        //     }
                                        // }
                                    // }
                                }
                                // dd($query->toSQL());
                            }
                        });
        // dd($queries->toSQL());

        $queries->orderBy((($sort_column !== '') ? $sort_column : $request['params']['sort_column']), $request['params']['sort_direction']);
        // ->orderByDesc('id')
        $data = $queries->paginate($request['params']['pagesize'], ['*'], 'page', $request['params']['page']);
        // dd($data);
        

        $ranks =[ "Normal", "VIP" ];
        $client_stages = [ "ALLO", "NO ALLO", "REMM", "TT", "CLEARED", "PENDING", "KICKED", "CARRIED OVER", "FREE SWITCH", "CXL", "CXL-CLIENT DROPPED" ];
        $kyc_statuses = [ "Not started", "Pending documents", "In progress", "Rejected", "Approved" ];
        $acc_types = [ "Individual", "Joint", "Trust", "Corporate" ];

        foreach ($data as $user) {
            // Handle client_stage attribute
            if (!is_null($user->client_stage) && $user->client_stage !== '') {
                $user->client_stage = $client_stages[$user->client_stage - 1];
            }
        
            // Handle rank attribute
            if (!is_null($user->rank) && $user->rank !== '') {
                $user->rank = $ranks[$user->rank - 1];
            }
            
            // Handle kyc_status attribute
            if (!is_null($user->kyc_status) && $user->kyc_status !== '') {
                $user->kyc_status = $kyc_statuses[$user->kyc_status - 1];
            }
            
            // Handle account_type attribute
            if (!is_null($user->account_type) && $user->account_type !== '') {
                $user->account_type = $acc_types[$user->account_type - 1];
            }

            // Handle account_manager_id attribute
            if (!is_null($user->account_manager_id) && $user->account_manager_id !== '') {
                // $accManager = User::find($user->account_manager_id);
                $user->account_manager_id = $user->accountManager->username . " (" . $user->accountManager->site->name . ")";
            }
        }

        $records = [
            'data' => $data,
            'total_rows' => $data->total(),
        ];

        return response()->json($records);
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
        $site_id = DB::table('django_site')
                        ->select('id','domain')
                        ->orderBy('id')
                        ->groupBy('id')
                        ->get()
                        ->map(function ($site) {
                            return [
                                'id' => $site->id,
                                'title' => $site->domain,
                            ];
                        });

        $is_active = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];
        $account_type = [ 
            ['id' => 1, 'title' => "Individual"],
            ['id' => 2, 'title' => "Joint"],
            ['id' => 3, 'title' => "Trust"],
            ['id' => 4, 'title' => "Corporate"],
        ];
        $client_stage = [ 
            ['id' => 1, 'title' => "ALLO"],
            ['id' => 2, 'title' => "NO ALLO"],
            ['id' => 3, 'title' => "REMM"],
            ['id' => 4, 'title' => "TT"],
            ['id' => 5, 'title' => "CLEARED"],
            ['id' => 6, 'title' => "PENDING"],
            ['id' => 7, 'title' => "KICKED"],
            ['id' => 8, 'title' => "CARRIED OVER"],
            ['id' => 9, 'title' => "FREE SWITCH"],
            ['id' => 10, 'title' => "CXL"],
            ['id' => 11, 'title' => "CXL-CLIENT DROPPED"]
        ];
        $rank = [ 
            ['id' => 1, 'title' => "Normal"],
            ['id' => 2, 'title' => "VIP"],
        ];
        $kyc_status = [ 
            ['id' => 1, 'title' => "Not started"],
            ['id' => 2, 'title' => "Pending documents"],
            ['id' => 3, 'title' => "In progress"],
            ['id' => 4, 'title' => "Rejected"],
            ['id' => 5, 'title' => "Approved"]
        ];
        $has_crm_access = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];
        $has_leads_access = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];
        $is_staff = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];
        $is_superuser = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];

        $data = [
            'site_id' => $site_id,
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

    // public function getUserChangelogs(string $id)
    // {
    //     $existingUserClientChangelogs = UserClientChangelog::where('users_clients_id', $id)
    //                                             ->orderBy('created_at', 'desc')
    //                                             ->get();

    //     return response()->json($existingUserClientChangelogs);
    // }

    public function getUserLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'user')
                                        ->select('id')
                                        ->orderByDesc('id')
                                        ->get();

        $userLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($userLogEntries, $value);
            }
        }

        return response()->json($userLogEntries);
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

    public function getUserOrders(string $id)
    {
        $orders = User::find($id)
                        ->orders()
                        ->get();

        $limb_stages = [ "ALLO", "Allo + docs", "TT", "CLEARED", "Cancelled", "Cancelled - bank block", "Cancelled - HTR", 
                        "Cancelled - order drop", "Cancelled refuse trade", "Kicked", "Carry over", "Free switch" ];

        foreach ($orders as $userOrders) {
            // Handle limb_stage attribute
            if (!is_null($userOrders->limb_stage) && $userOrders->limb_stage !== '') {
                $userOrders->limb_stage = $limb_stages[$userOrders->limb_stage - 1];
            }
        }

        // dd($orders);

        return response()->json($orders);
    }

    public function getAllSites()
    {
        $sites = Site::all();

        return response()->json($sites);
    }

    public function getAccountManagers()
    {
        $accountManagers = User::where('has_crm_access', true)
                                    ->with('site')
                                    ->get();

        return response()->json($accountManagers);
    }

    public function getUserList()
    {
        $userList = User::with('site:id,name')->get();

        return response()->json($userList);
    }
}
