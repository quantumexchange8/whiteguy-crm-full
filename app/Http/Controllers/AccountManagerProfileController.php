<?php

namespace App\Http\Controllers;

use App\Exports\AccountManagerProfileExport;
use App\Http\Requests\AccountManagerProfileRequest;
use App\Models\AccountManagerProfile;
use App\Models\ContentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class AccountManagerProfileController extends Controller
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
            return Inertia::render('CRM/AccountManagerProfiles/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/AccountManagerProfiles/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/AccountManagerProfiles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountManagerProfileRequest $request)
    {
        $data = $request->all();
                
        $fileName = '';
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->getClientOriginalName();
        }

        AccountManagerProfile::create([
            'file' => 'account_manager_profiles/' . $data['user_id'] . '/' . $fileName,
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
            'user_id' => $data['user_id'],
        ]);
        
        $errorMsgTitle = "You have successfully created a new account manager profile.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('account-manager-profiles.index')
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
        $data = AccountManagerProfile::find($id);

        return Inertia::render('CRM/AccountManagerProfiles/Edit', [
            'data' => $data,
        ]);     
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountManagerProfileRequest $request, string $id)
    {
        $data = $request->all();

        $existingProfile = AccountManagerProfile::find($id);
        
        $fileName = '';

        if (isset($data['file'])) {
            if ($request->hasFile('new_file')) {
                $fileName = 'account_manager_profiles/' . $data['user_id'] . '/' . $request->file('new_file')->getClientOriginalName();
            } else {
                $fileName = $data['file'];
            }
        }

        $existingProfile->update([
            'file' => $fileName,
            'edited_at' => $data['edited_at'],
            'created_at' => $data['created_at'],
            'user_id' => $data['user_id'],
        ]);
		
        $errorMsgTitle = "You have successfully updated the account manager profile.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('account-manager-profiles.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getAccountManagerProfiles(Request $request)
    {   
        $tableColumns = Schema::getColumnListing('core_accountmanagerprofile');
        $sort_column = '';

        $queries = AccountManagerProfile::query();
        $queries->with(['user:id,username,full_name,phone_number,email,address,country,site_id', 'user.site:id,name']);
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
                            $profileUserId = User::where('username', 'LIKE', '%' . $filter['value'] . '%')
                                                    ->select('id', 'username')
                                                    ->get()
                                                    ->map(function ($item) {
                                                        return [
                                                            'id' => $item->id,
                                                            'value' => $item->username,
                                                        ];
                                                    });

                            $filteredId = [];
                            foreach ($profileUserId as $key => $value) {
                                $filteredId[] = [
                                    'id' => $value['id'],
                                    'value' => $value['value'],
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
                        
                    }

                    if ($filter['condition'] === 'is_null') {
                        $query->orWhereNull($filter['field']);
                    } elseif ($filter['condition'] === 'is_not_null') {
                        $query->orWhereNotNull($filter['field']);
                    }
                }
            }
        });
        $queries->orderBy(($sort_column !== '') ? $sort_column : $request['params']['sort_column'], $request['params']['sort_direction']);
        $data = $queries->paginate($request['params']['pagesize'], ['id', 'file', 'user_id', 'edited_at', 'created_at'], 'page', $request['params']['page']);

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
                    $query->whereNotIn('user_id', $tempArr);
                    // if($filter['field'] === 'lead_front_assignee') {
                    // }
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    if ($filter['field'] === 'user_id'){
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    if ($filter['field'] === 'user_id'){
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
                    $query->whereIn('user_id', $tempArr);
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
                    $query->whereIn('user_id', $tempArr);
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    $query->where($filter['field'], '>', $filter['value']);
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    $query->where($filter['field'], '>=', $filter['value']);
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    $query->where($filter['field'], '<', $filter['value']);
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
                    $query->whereIn('user_id', $tempArr);
                } else {
                    $query->where($filter['field'], '<=', $filter['value']);
                }
                break;
            case 'contain':
                if ($filteredIdArr) {
                    $tempArr = [];
                    foreach ($filteredIdArr as $index => $value) {
                        array_push($tempArr, $value['id']);   
                    }
                    $query->whereIn('user_id', $tempArr);
                } else {
                    $query->where($filter['field'], 'LIKE', '%' . $filter['value'] . '%');
                }
                break;
            case 'is_null':
                $query->orWhereNull($filter['field']);
                break;
            case 'is_not_null':
                $query->orWhereNotNull($filter['field']);
                break;
        }
    }

    public function exportToExcel($selectedRowsData)
    {
        $profileArr = explode(',', $selectedRowsData);
        foreach ($profileArr as $key => $value) {
            $profileArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'account_manager_profile_' . $currentDate . '.xlsx';
        
        return (new AccountManagerProfileExport($profileArr))->download($exportTitle);
    }

    public function getAllAccountManagerProfileForExport()
    {
        $data = AccountManagerProfile::with(['user:id,username,site_id', 'user.site:id,name'])
                                        ->get();

        return response()->json($data);
    }

    public function getAccontManagerProfileLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'accountmanagerprofile')
                                        ->select('id')
                                        ->get();

        $accMngProfLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($accMngProfLogEntries, $value);
            }
        }

        return response()->json($accMngProfLogEntries);
    }
}
