<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        return view('pages.careers', [
            'jobs' => JobPosting::query()->published()->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }
}
