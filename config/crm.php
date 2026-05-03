<?php

return [

    'email_enabled' => filter_var(env('CRM_EMAIL_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    'default_assignee_id' => env('CRM_DEFAULT_ASSIGNEE_ID') ? (int) env('CRM_DEFAULT_ASSIGNEE_ID') : null,

];
