<?php

return [

    'chat_enabled' => env('AI_CHAT_ENABLED', true),

    'widget_agent_slug' => env('AI_WIDGET_AGENT_SLUG'),

    'intake_agent_slug' => env('AI_INTAKE_AGENT_SLUG'),

    'max_message_length' => (int) env('AI_CHAT_MAX_MESSAGE_LENGTH', 8000),

    'default_openai_base' => 'https://api.openai.com/v1',

    'default_grok_base' => 'https://api.x.ai/v1',

    'default_gemini_base' => 'https://generativelanguage.googleapis.com/v1beta/',

];
