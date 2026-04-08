<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('pages.faq', [
            'faqs' => Faq::query()
                ->published()
                ->where('show_on_general_faq', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
