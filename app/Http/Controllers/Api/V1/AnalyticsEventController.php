<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAnalyticsEventRequest;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Response;

class AnalyticsEventController extends Controller
{
    public function store(StoreAnalyticsEventRequest $request): Response
    {
        AnalyticsEvent::query()->create($request->validated());

        return response()->noContent();
    }
}
