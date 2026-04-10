<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageBuilderController extends Controller
{
    public function show(Request $request, Page $page): View
    {
        $this->authorize('update', $page);

        return view('layouts.builder', [
            'pageId' => $page->id,
            'pageTitle' => $page->title,
            'pagePath' => $page->path,
        ]);
    }
}
