<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ApplicationController extends Controller
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
            return Inertia::render('CRM/Applications/Index', [
                'errors' => $errors,
                'errorMsg' => $errorMsg
            ]);
        }
        return Inertia::render('CRM/Applications/Index');
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

    public function getApplications(Request $request)
    {   
        // Fetch total count
        $dataTotalCount = DB::table('applications')->count();

        // Fetch announcements
        $data = DB::table('applications')->orderBy('id')->cursorPaginate($dataTotalCount);

        return response()->json($data);
    }

    public function getAllLeads(Request $request)
    {
        // Extract pagination parameters
        $pageSize = $request->input('pagination.pageSize', 10); // Default page size: 10
        $pageIndex = $request->input('pagination.pageIndex', 1); // Default page index: 1

        // Generate cache key for paginated leads
        $cacheKey = 'leads_page_' . $pageIndex . '_size_' . $pageSize;
    
        $rows = Cache::remember($cacheKey, 300, function() use ($pageSize, $pageIndex) {
            return Lead::with([
                'leadCreator:id,username,site_id', 
                'leadCreator.site:id,name', 
                'assignee:id,username,site_id', 
                'assignee.site:id,name', 
                'leadnotes',
                'leadnotes.leadNoteCreator:id,username,site_id',
                'leadnotes.leadNoteCreator.site:id,name',
                'contactOutcome:id,title',
                'stage:id,title',
                'appointmentLabel:id,title'
            ])
            ->select([
                'id', 'date', 'first_name', 'assignee_id', 'country', 'vc', 'phone_number',
                 'data_source', 'contacted_at', 'give_up_at', 'data_code', 'data_type'
            ])
            ->orderBy('id','desc')
            ->simplePaginate(
                                $pageSize, 
                                [
                                    'id', 'date', 'first_name', 'assignee_id', 'country', 'vc', 'phone_number',
                                    'data_source', 'contacted_at', 'give_up_at', 'data_code', 'data_type'
                                ], 
                                'page', 
                                $pageIndex
                            );
        });
    
        $total_rows = Cache::remember('leads_total_count', 300, function() {
            return Lead::count();
        });

        // $rows = [];
        
        // new LeadResource(Lead::with([
        //                         'leadCreator:id,username,site_id', 
        //                         'leadCreator.site:id,name', 
        //                         'assignee:id,username,site_id', 
        //                         'assignee.site:id,name', 
        //                         'leadnotes',
        //                         'leadnotes.leadNoteCreator:id,username,site_id',
        //                         'leadnotes.leadNoteCreator.site:id,name',
        //                         'contactOutcome:id,title',
        //                         'stage:id,title',
        //                         'appointmentLabel:id,title'
        //                     ])
        //                     ->limit(100)
        //                     ->cursor()
        //                     ->each(function ($lead) use (&$rows) {
        //                         // Modify the lead attributes as needed
        //                         $lead['username'] = 'yeaboi';
                        
        //                         // Add the modified lead to the rows array
        //                         $rows[] = $lead->toArray();
        //                     }));

        // $rows = [];

        // Lead::with([
        //         'leadCreator:id,username,site_id', 
        //         'leadCreator.site:id,name', 
        //         'assignee:id,username,site_id', 
        //         'assignee.site:id,name', 
        //         'leadnotes',
        //         'leadnotes.leadNoteCreator:id,username,site_id',
        //         'leadnotes.leadNoteCreator.site:id,name',
        //         'contactOutcome:id,title',
        //         'stage:id,title',
        //         'appointmentLabel:id,title'
        //     ])
        //     ->select(['id', 'date', 'first_name', 'assignee_id', 'country', 'vc', 'phone_number', 'data_source', 'contacted_at', 'give_up_at', 'data_code', 'data_type'])
        //     // ->limit(10000)
        //     ->orderByDesc('id')
        //     ->cursor()
        //     ->each(function ($lead) use (&$rows) {
        //         // Add the modified lead to the rows array
        //         $rows[] = $lead->toArray();
        //     });

        // $rows = Lead::with([
        //             'leadCreator:id,username,site_id', 
        //             'leadCreator.site:id,name', 
        //             'assignee:id,username,site_id', 
        //             'assignee.site:id,name', 
        //             'leadnotes',
        //             'leadnotes.leadNoteCreator:id,username,site_id',
        //             'leadnotes.leadNoteCreator.site:id,name',
        //             'contactOutcome:id,title',
        //             'stage:id,title',
        //             'appointmentLabel:id,title'
        //         ])
        //         ->lazyById(200, $column = 'id');

        // $rows = [];
        
        // foreach (Lead::with([
        //             'leadCreator:id,username,site_id', 
        //             'leadCreator.site:id,name', 
        //             'assignee:id,username,site_id', 
        //             'assignee.site:id,name', 
        //             'leadnotes',
        //             'leadnotes.leadNoteCreator:id,username,site_id',
        //             'leadnotes.leadNoteCreator.site:id,name',
        //             'contactOutcome:id,title',
        //             'stage:id,title',
        //             'appointmentLabel:id,title'
        //         ])
        //         ->lazy() as $lead) {
        //             $rows[] = $lead->toArray();
        //         }

        // dd($rows);

        $data = [
            'rows' => $rows,
            'total_rows' => $total_rows,
        ];
        
        return response()->json($data);
    }
}
