<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobFilterService;
use App\Models\Job;
use App\Http\Resources\JobResource;
class JobController extends Controller
{

    public function index(Request $request)
    {
        if($request->query('filter')){
            try{
                $filterService = new JobFilterService($request);
                //Parse the query string to json
                $parsedFilter = $filterService->parseToJson($request->query('filter'));
                //apply filter to the parsed filtera
                $jobs = $filterService->applyFilters($parsedFilter,['languages', 'locations','categories']);
               //return response
               return JobResource::collection($jobs);
            }catch(\Exception $e){
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }else{
            //return all jobs if no filter exists
            return JobResource::collection(Job::all());

        }

    }
}
