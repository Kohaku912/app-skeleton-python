<?php
return [
    'routes' => [
        ['name'=>'api.webhook', 'url'=>'/webhook', 'verb'=>'POST', 'controller'=>'ApiController', 'action'=>'webhook'],
        ['name'=>'api.health', 'url'=>'/health', 'verb'=>'GET', 'controller'=>'ApiController', 'action'=>'health'],

        // 管理者用 API (認証が必要)
        ['name'=>'admin.stats', 'url'=>'/admin/stats', 'verb'=>'GET', 'controller'=>'AdminController', 'action'=>'stats'],
        ['name'=>'admin.set_user_status', 'url'=>'/admin/user/{userId}/status', 'verb'=>'POST', 'controller'=>'AdminController', 'action'=>'setUserStatus'],
    ],
];
