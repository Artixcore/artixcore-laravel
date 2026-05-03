<?php

return [

    'openai_api_key' => env('OPENAI_API_KEY'),

    'model' => env('OPENAI_ARTICLE_MODEL', 'gpt-4.1-mini'),

    'author_name' => env('AI_CONTENT_AUTHOR', env('AI_ARTICLE_AUTHOR', 'Ali 1.0')),

    'auto_publish' => filter_var(env('AI_ARTICLE_AUTO_PUBLISH', false), FILTER_VALIDATE_BOOLEAN),

    /*
    | When auto_publish is false: draft or pending_review (invalid values fall back to pending_review).
    */
    'default_status' => env('AI_ARTICLE_DEFAULT_STATUS', 'pending_review'),

    'daily_limit' => max(0, min(20, (int) env('AI_ARTICLE_DAILY_LIMIT', 3))),

    'plagiarism_checker_api_key' => env('PLAGIARISM_CHECKER_API_KEY'),

    'content_buckets' => [
        'latest_discovery',
        'today_trends',
        'latest_tech',
    ],

];
