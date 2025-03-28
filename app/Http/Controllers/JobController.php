<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobFilterService;

class JobController extends Controller
{

    public function index(Request $request)
    {
        $filterService = new JobFilterService($request);
        $jobs = $filterService->applyFilters()->paginate(10);
        
        return response()->json($jobs);
    }
}
