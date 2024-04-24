<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'course-create',
            'course-viewall',
            'course-edit',
            'course-delete',
            'event-create',
            'event-viewall',
            'event-edit',
            'event-delete',
            'attendee-viewall',
            'news-create',
            'news-viewall',
            'news-edit',
            'news-delete',
            'user-viewall'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['guard_name' => 'admin', 'name' => $permission]);
        }
    }
}