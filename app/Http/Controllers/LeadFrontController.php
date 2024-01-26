<?php

namespace App\Http\Controllers;

use App\Models\LeadFront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadFrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CRM/LeadFronts/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/LeadFronts/Create');
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
        $data = LeadFront::find($id);

        return Inertia::render('CRM/LeadFronts/Edit', [
            'data' => $data,
        ]);
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
        // dd($id);
        // $existingLeadFront = LeadFront::find($id);
        // $existingLeadFront->delete();


        // return redirect(route('leads.index'));
    }

    public function getLeadFronts(Request $request)
    {   
        // Fetch total count
        $dataTotalCount = DB::table('lead_front')->count();

        // Fetch announcements
        $data = DB::table('lead_front')->get();

        return response()->json($data);
    }
}
