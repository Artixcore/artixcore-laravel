<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(Request $request, string $path): PageResource
    {
        $page = Page::query()
            ->where('path', $path)
            ->with(['blocks' => fn ($q) => $q->orderBy('sort_order')])
            ->firstOrFail();

        $this->authorize('view', $page);

        return new PageResource($page);
    }
}
