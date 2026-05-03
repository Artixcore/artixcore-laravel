<?php

namespace App\Http\Requests\Admin;

/**
 * Alias for lead PATCH validation (status and related fields).
 * Prefer this type-hint in controllers when the intent is status-focused updates.
 */
class LeadStatusUpdateRequest extends UpdateLeadRequest
{
}
