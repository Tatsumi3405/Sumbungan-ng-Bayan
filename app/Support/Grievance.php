<?php

namespace App\Support;

/**
 * Static lookup data ported from lib/models.dart
 * (categories, statuses and the 13 government sectors).
 */
class Grievance
{
    public const CATEGORIES = [
        'infrastructure'   => ['label' => 'Infrastructure',       'icon' => '🏗️'],
        'publicSafety'     => ['label' => 'Public Safety',        'icon' => '🛡️'],
        'environment'      => ['label' => 'Environment',          'icon' => '🌿'],
        'healthSanitation' => ['label' => 'Health & Sanitation',  'icon' => '💊'],
        'socialServices'   => ['label' => 'Social Services',      'icon' => '🤝'],
        'other'            => ['label' => 'Other',                'icon' => '📋'],
    ];

    public const STATUSES = [
        'pending'    => ['label' => 'Pending',     'color' => '#F59E0B', 'bg' => '#FFF7ED'],
        'inProgress' => ['label' => 'In Progress', 'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'resolved'   => ['label' => 'Resolved',    'color' => '#16A34A', 'bg' => '#F0FDF4'],
        'rejected'   => ['label' => 'Rejected',    'color' => '#DC2626', 'bg' => '#FEF2F2'],
    ];

    public const SECTORS = [
        ['id' => 'dpwh',  'name' => 'DPWH',     'full' => 'Dept. of Public Works & Highways',     'icon' => '🏗️'],
        ['id' => 'doh',   'name' => 'DOH',      'full' => 'Dept. of Health',                      'icon' => '🏥'],
        ['id' => 'dswd',  'name' => 'DSWD',     'full' => 'Dept. of Social Welfare & Development','icon' => '🤝'],
        ['id' => 'pnp',   'name' => 'PNP',      'full' => 'Philippine National Police',           'icon' => '🛡️'],
        ['id' => 'bfp',   'name' => 'BFP',      'full' => 'Bureau of Fire Protection',            'icon' => '🔥'],
        ['id' => 'menro', 'name' => 'MENRO',    'full' => 'Environment & Natural Resources Office','icon' => '🌿'],
        ['id' => 'mswdo', 'name' => 'MSWDO',    'full' => 'Municipal Social Welfare & Dev. Office','icon' => '🏠'],
        ['id' => 'mho',   'name' => 'MHO',      'full' => 'Municipal Health Office',              'icon' => '💊'],
        ['id' => 'meo',   'name' => 'MEO',      'full' => 'Municipal Engineering Office',         'icon' => '⚙️'],
        ['id' => 'dilg',  'name' => 'DILG',     'full' => 'Dept. of Interior & Local Government', 'icon' => '🏛️'],
        ['id' => 'deped', 'name' => 'DepEd',    'full' => 'Dept. of Education',                   'icon' => '📚'],
        ['id' => 'brgy',  'name' => 'Barangay', 'full' => 'Barangay Council',                     'icon' => '🏘️'],
        ['id' => 'other', 'name' => 'Other',    'full' => 'Other Government Agency',              'icon' => '📋'],
    ];

    public static function categoryKeys(): array { return array_keys(self::CATEGORIES); }
    public static function statusKeys(): array   { return array_keys(self::STATUSES); }
    public static function sectorIds(): array    { return array_column(self::SECTORS, 'id'); }

    public static function sector(?string $id): ?array
    {
        foreach (self::SECTORS as $s) {
            if ($s['id'] === $id) return $s;
        }
        return null;
    }
}
