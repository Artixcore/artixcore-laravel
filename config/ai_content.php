<?php

return [

    /*
    | Shared AI author display name (Ali 1.0). Falls back to AI_ARTICLE_AUTHOR / ai_articles config.
    */
    'author_name' => env('AI_CONTENT_AUTHOR', env('AI_ARTICLE_AUTHOR', 'Ali 1.0')),

    'case_study' => [
        'enabled' => filter_var(env('AI_CASE_STUDY_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'interval_days' => max(1, (int) env('AI_CASE_STUDY_INTERVAL_DAYS', 2)),
        'daily_limit' => max(0, min(10, (int) env('AI_CASE_STUDY_DAILY_LIMIT', 1))),
        'auto_publish' => filter_var(env('AI_CASE_STUDY_AUTO_PUBLISH', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'market_update' => [
        'enabled' => filter_var(env('AI_MARKET_UPDATE_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'interval_days' => max(1, (int) env('AI_MARKET_UPDATE_INTERVAL_DAYS', 2)),
        'daily_limit' => max(0, min(10, (int) env('AI_MARKET_UPDATE_DAILY_LIMIT', 1))),
        'auto_publish' => filter_var(env('AI_MARKET_UPDATE_AUTO_PUBLISH', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'news_api_key' => env('NEWS_API_KEY'),

    'market_data_api_key' => env('MARKET_DATA_API_KEY'),

    /*
    | Pools rotated when no explicit topic is passed to generators.
    */
    'case_study_topics' => [
        'Business transformation outcomes',
        'SaaS business growth with automation',
        'AI automation in mid-market companies',
        'E-commerce conversion and operations uplift',
        'CRM/ERP implementation playbook',
        'Startup product development velocity',
        'Digital transformation change management',
        'Cloud migration execution',
        'Cybersecurity posture improvement',
        'Developer productivity platforms',
        'Enterprise workflow automation',
        'Quantum technology business impact (concept framing)',
        'Physics-inspired technology adoption',
        'Scientific computing platforms',
        'AI in research workflows',
        'Emerging applied science for enterprises',
    ],

    'market_update_areas' => [
        'SaaS market trends',
        'AI industry shifts',
        'Software development market landscape',
        'Cloud infrastructure adoption',
        'Cybersecurity demand signals',
        'E-commerce platform trends',
        'Startup funding climate for digital products',
        'Developer tooling adoption patterns',
        'Automation market dynamics',
        'Quantum computing commercial signals',
        'Physics / advanced science commercialization',
        'Bangladesh & global software industry context',
        'Enterprise technology buying behaviour',
    ],

];
