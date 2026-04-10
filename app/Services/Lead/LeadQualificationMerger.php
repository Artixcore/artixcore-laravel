<?php

namespace App\Services\Lead;

use App\Models\Lead;
use Illuminate\Support\Str;

class LeadQualificationMerger
{
    public function mergeFromUserMessage(Lead $lead, string $userMessage): void
    {
        $text = Str::lower(trim($userMessage));
        if ($text === '') {
            return;
        }

        $qual = $lead->custom_fields['qualification'] ?? [];
        if (! is_array($qual)) {
            $qual = [];
        }

        $dirty = false;

        if ($lead->budget === null || $lead->budget === '') {
            $budget = $this->guessBudget($text);
            if ($budget !== null) {
                $lead->budget = $budget;
                $qual['budget_range'] = $budget;
                $dirty = true;
            }
        }

        if ($lead->service_interest === null || $lead->service_interest === '') {
            $svc = $this->guessServiceLine($text);
            if ($svc !== null) {
                $lead->service_interest = $svc;
                $qual['service'] = $svc;
                $dirty = true;
            }
        }

        if ($lead->company === null || $lead->company === '') {
            $co = $this->guessCompany($text);
            if ($co !== null) {
                $lead->company = $co;
                $qual['business'] = $co;
                $dirty = true;
            }
        }

        $timeline = $this->guessTimeline($text);
        if ($timeline !== null && empty($qual['timeline'])) {
            $qual['timeline'] = $timeline;
            $dirty = true;
        }

        $pref = $this->guessContactPreference($text);
        if ($pref !== null && empty($qual['contact_preference'])) {
            $qual['contact_preference'] = $pref;
            $dirty = true;
        }

        if (! $dirty) {
            return;
        }

        $custom = $lead->custom_fields ?? [];
        $custom['qualification'] = array_merge(
            is_array($custom['qualification'] ?? null) ? $custom['qualification'] : [],
            $qual
        );
        $lead->custom_fields = $custom;
        $lead->save();
    }

    private function guessBudget(string $text): ?string
    {
        if (preg_match('/\$\s*[\d,]+(?:\.\d{2})?\s*(?:k|m|thousand|million)?/i', $text, $m)) {
            return trim($m[0]);
        }
        if (preg_match('/\b\d{1,3}\s*k\b/i', $text, $m)) {
            return Str::upper(trim($m[0]));
        }
        if (str_contains($text, 'budget') && preg_match('/budget[^\d]{0,24}([\d,]+(?:\.\d{2})?)/', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function guessServiceLine(string $text): ?string
    {
        $patterns = [
            'saas' => '/\bsaas\b|software as a service/',
            'mobile app' => '/\bmobile app\b|\bios\b|\bandroid\b/',
            'web app' => '/\bweb app\b|\bwebsite\b|\bweb application\b/',
            'ai' => '/\bai\b|machine learning|llm|automation agent/',
            'web3' => '/\bweb3\b|blockchain|smart contract/',
        ];
        foreach ($patterns as $label => $rx) {
            if (preg_match($rx, $text)) {
                return $label;
            }
        }

        if (preg_match('/looking for ([^.?\n]{3,120})/', $text, $m)) {
            return Str::limit(trim($m[1]), 120);
        }

        return null;
    }

    private function guessCompany(string $text): ?string
    {
        if (preg_match('/\b(?:at|from)\s+([a-z0-9][a-z0-9\s&\-.]{2,50})/i', $text, $m)) {
            return Str::limit(trim($m[1]), 80);
        }

        return null;
    }

    private function guessTimeline(string $text): ?string
    {
        if (preg_match('/\b(asap|urgent|this week|next week|this month|next month|q[1-4]|quarter)\b/', $text, $m)) {
            return $m[1];
        }

        return null;
    }

    private function guessContactPreference(string $text): ?string
    {
        if (preg_match('/\b(call me|phone|text me|email me|prefer (?:email|phone|video))\b/', $text, $m)) {
            return $m[0];
        }

        return null;
    }
}
