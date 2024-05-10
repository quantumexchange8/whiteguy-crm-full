<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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
        // dd($request);
        $total_rows = Lead::count();

        $rows = Lead::with([
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
                        ->orderByDesc('id')
                        ->paginate($request['pagination']['pageSize'], ['*'], 'page', $request['pagination']['pageIndex']);

        $data = [
            'rows' => $rows,
            'total_rows' => $total_rows,
        ];
        
        return response()->json($data);
    }
}
