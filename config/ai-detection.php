<?php

return [
    'enabled' => env('AI_DETECTION_ENABLED', true),
    
    'risk_thresholds' => [
        'low' => 0.3,
        'medium' => 0.6,
        'high' => 0.8,
        'critical' => 0.9,
    ],
    
    'factors' => [
        'location' => [
            'weight' => 0.3,
            'unusual_country_risk' => 0.8,
            'unusual_city_risk' => 0.6,
        ],
        'device' => [
            'weight' => 0.25,
            'new_device_risk' => 0.7,
            'different_device_type_risk' => 0.9,
        ],
        'time' => [
            'weight' => 0.15,
            'unusual_hour_risk' => 0.6,
            'unusual_day_risk' => 0.5,
        ],
        'velocity' => [
            'weight' => 0.2,
            'attempts_per_hour_threshold' => 5,
            'risk_per_attempt' => 0.2,
        ],
        'ip_reputation' => [
            'weight' => 0.1,
            'vpn_proxy_risk' => 1.0,
            'high_risk_countries' => ['CN', 'RU', 'KP', 'IR', 'SY'],
        ],
    ],
    
    'verification' => [
        'required_score' => 0.7,
        'methods' => ['2fa', 'email', 'security_questions'],
        '2fa_expiry' => 10, // minutes
        'email_expiry' => 30, // minutes
    ],
    
    'notifications' => [
        'email' => env('AI_NOTIFICATION_EMAIL', true),
        'sms' => env('AI_NOTIFICATION_SMS', false),
        'in_app' => env('AI_NOTIFICATION_IN_APP', true),
        'admin_notification_threshold' => 0.8,
    ],
    
    'ml' => [
        'enabled' => env('AI_ML_ENABLED', false),
        'model_path' => storage_path('app/ai/models/'),
        'training_data_days' => 90,
        'retrain_interval' => 7, // days
    ],
];
