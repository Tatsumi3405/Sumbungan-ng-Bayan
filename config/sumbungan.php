<?php

return [
    // First admin account, created by the seeder only if it does not exist yet.
    'admin_email'    => env('ADMIN_EMAIL', 'admin@maslog.gov.ph'),
    'admin_name'     => env('ADMIN_NAME', 'Barangay Maslog Admin'),
    'admin_password' => env('SEED_ADMIN_PASSWORD'),
];
