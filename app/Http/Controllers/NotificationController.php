<?php

namespace App\Http\Controllers;

use App\Exports\NotificationsExport;
use App\Http\Requests\NotificationRequest;
use App\Models\ContentType;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
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
            return Inertia::render('CRM/Notifications/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/Notifications/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Notifications/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request)
    {
        $data = $request->all();
        
        $existingNotification = Notification::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'notification_type' => $data['notification_type'],
            'attachment' => '',
            'is_read' => $data['is_read'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
            'user_id' => $data['user_id'],
        ]);

        $existingNotification->save();

        $errorMsgTitle = "You have successfully created a new notification.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('notifications.index')
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
        $data = Notification::with('user:id')
                        ->find($id);

        return Inertia::render('CRM/Notifications/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        $existingNotification = Notification::find($id);
        
        $existingNotification->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'notification_type' => $data['notification_type'],
            'attachment' => '',
            'is_read' => $data['is_read'],
            'edited_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['edited_at']),
            'created_at' => preg_replace('/(\d{2})(\d{2})$/', '$1', $data['created_at']),
            'user_id' => $data['user_id'],
        ]);

        $existingNotification->save();

        $errorMsgTitle = "You have successfully updated the notification.";
        $errorMsgType = "success";

        $errorMsg = [
            'title' => $errorMsgTitle,
            'type' => $errorMsgType,
        ];

        return Redirect::route('notifications.index')
                        ->with('errorMsg', $errorMsg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getNotifications(Request $request)
    {   
        // dd($request);
        if ($request['checkedFilters']) {
            $query = Notification::query();

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

            $data = $query->with([
                                'user:id,full_name,username,phone_number,email,country,address,site_id',
                                'user.site:id,name'
                            ])
                            ->orderByDesc('id')
                            ->get();
            
            return response()->json($data);
        }
        $data = Notification::with([
                                'user:id,full_name,username,phone_number,email,country,address,site_id',
                                'user.site:id,name'
                            ])
                            ->orderByDesc('id')
                            ->get();
        
        foreach ($data as $notification) {
            // Handle client_stage attribute
            if (!is_null($notification->notification_type) && $notification->notification_type !== '') {
                if ($notification->notification_type === 'PRIVATE_MESSAGE') {
                    $notification->notification_type = 'Custom notification';
                }
                if ($notification->notification_type === 'NEW_ANNOUNCEMENT') {
                    $notification->notification_type = 'New announcement';
                }
            }
        }
    
        return response()->json($data);
    }

    public function getCategories(Request $request)
    {
        $is_read = [ 
            ['id' => true, 'title' => "Yes"],
            ['id' => false, 'title' => "No"]
        ];

        $data = [
            'is_read' => $is_read,
        ];
        
        return response()->json($data);
    }
    
    public function getNotificationLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'notification')
                                        ->select('id')
                                        ->get();

        $notificationLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($notificationLogEntries, $value);
            }
        }

        return response()->json($notificationLogEntries);
    }
    
    public function exportToExcel($selectedRowsData)
    {
        $notificationsArr = explode(',', $selectedRowsData);
        foreach ($notificationsArr as $key => $value) {
            $notificationsArr[$key] = (int)$value;
        }

        $currentDate = Carbon::now()->format('Ymd_His');
        $exportTitle = 'notifications_' . $currentDate . '.xlsx';
        
        return (new NotificationsExport($notificationsArr))->download($exportTitle);
    }
}
