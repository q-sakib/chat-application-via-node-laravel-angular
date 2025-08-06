<?php
return [
  'roles' => [
    'host' => [
      'name' => 'Crimson Sovereign',
      'max' => 3,
      'permissions' => ['invite', 'promote', 'ban', 'transfer'],
    ],
    'watcher' => [
      'name' => 'Celestial Watcher',
      'max' => 5,
      'permissions' => ['ban'],
    ],
    'elite' => [
      'name' => 'Elite Fan',
      'max' => null,
      'permissions' => [],
    ],
    'fan' => [
      'name' => 'Spirit Fan',
      'max' => 50,
      'permissions' => [],
    ]
  ]
];
