<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Job;


class JobFilterService
{
    protected Builder $query;
    protected array $filters;

    public function __construct(Request $request)
    {
        $this->query = Job::query();
        $this->filters = $request->query('filter', []);
    }
}