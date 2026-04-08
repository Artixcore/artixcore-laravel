<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = LegalPage::query()->where('slug', $slug)->firstOrFail();

        return view('pages.legal.show', [
            'legalPage' => $page,
        ]);
    }
}
