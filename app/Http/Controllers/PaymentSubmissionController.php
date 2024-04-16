<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use App\Models\PaymentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Redirect;

class PaymentSubmissionController extends Controller
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
            return Inertia::render('CRM/PaymentSubmissions/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }

        return Inertia::render('CRM/PaymentSubmissions/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getPaymentSubmissions(Request $request)
    {
        if ($request['checkedFilters']) {
            $query = PaymentSubmission::query();

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
                                'user.site:id,name',
                                'paymentMethod:id,title'
                            ])
                            ->orderByDesc('id')
                            ->get();

            $statusArray = [ 
                "Pending", "Approved", "Cancelled"
            ];
            
            foreach ($data as $paymentSubmission) {
                if (!is_null($paymentSubmission->status) && $paymentSubmission->status !== '') {
                    $paymentSubmission->status = $statusArray[$paymentSubmission->status - 1];
                }
            }
            
            return response()->json($data);
        }
        $data = PaymentSubmission::with([
                                'user:id,full_name,username,phone_number,email,country,address,site_id',
                                'user.site:id,name',
                                'paymentMethod:id,title'
                            ])
                            ->orderByDesc('id')
                            ->get();

        $statusArray = [ 
            "Pending", "Approved", "Cancelled"
        ];
        
        foreach ($data as $paymentSubmission) {
            if (!is_null($paymentSubmission->status) && $paymentSubmission->status !== '') {
                $paymentSubmission->status = $statusArray[$paymentSubmission->status - 1];
            }
        }
    
        return response()->json($data);
    }

    public function getCategories(Request $request)
    {
        $date = [ "Today", "Past 7 days", "This month", "This year", "No date", "Has date" ];

        $status = [ 
            ['id' => 1, 'title' => "Pending"],
            ['id' => 2, 'title' => "Approved"],
            ['id' => 3, 'title' => "Cancelled"]
        ];

        $payment_method_id = PaymentMethod::select('id','title')
                        ->orderBy('id')
                        ->groupBy('id')
                        ->get()
                        ->map(function ($paymentMethod) {
                            return [
                                'id' => $paymentMethod->id,
                                'title' => $paymentMethod->title,
                            ];
                        });

        $data = [
            'date' => $date,
            'status' => $status,
            'payment_method_id' => $payment_method_id,
        ];
        
        return response()->json($data);
    }
    
    public function getPaymentSubmissionLogEntries(string $id)
    {
        $contentTypeId = ContentType::with('auditLogEntries')
                                        ->where('app_label', 'core')
                                        ->where('model', 'paymentsubmission')
                                        ->select('id')
                                        ->get();

        $paymentSubmissionLogEntries = [];

        foreach ($contentTypeId[0]->auditLogEntries as $key => $value) {
            if ((string)$value->object_id === $id){
                array_push($paymentSubmissionLogEntries, $value);
            }
        }

        return response()->json($paymentSubmissionLogEntries);
    }
}
