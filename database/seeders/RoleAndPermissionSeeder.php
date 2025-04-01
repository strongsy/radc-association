<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //permission permissions
        Permission::create(['name' => 'permission-index']);
        Permission::create(['name' => 'permission-create']);
        Permission::create(['name' => 'permission-read']);
        Permission::create(['name' => 'permission-update']);
        Permission::create(['name' => 'permission-destroy']);

        //role permissions
        Permission::create(['name' => 'role-index']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-read']);
        Permission::create(['name' => 'role-update']);
        Permission::create(['name' => 'role-destroy']);

        //mail permissions
        Permission::create(['name' => 'mail-index']);
        Permission::create(['name' => 'mail-create']);
        Permission::create(['name' => 'mail-read']);
        Permission::create(['name' => 'mail-update']);
        Permission::create(['name' => 'mail-send']);
        Permission::create(['name' => 'mail-restore']);
        Permission::create(['name' => 'mail-force-delete']);
        Permission::create(['name' => 'mail-destroy']);

        //reply permissions
        Permission::create(['name' => 'reply-index']);
        Permission::create(['name' => 'reply-create']);
        Permission::create(['name' => 'reply-read']);
        Permission::create(['name' => 'reply-update']);
        Permission::create(['name' => 'reply-send']);
        Permission::create(['name' => 'reply-restore']);
        Permission::create(['name' => 'reply-force-delete']);
        Permission::create(['name' => 'reply-destroy']);

        //registrant permissions
        Permission::create(['name' => 'registrant-index']);
        Permission::create(['name' => 'registrant-create']);
        Permission::create(['name' => 'registrant-read']);
        Permission::create(['name' => 'registrant-update']);
        Permission::create(['name' => 'registrant-destroy']);
        Permission::create(['name' => 'registrant-authorize']);

        //users permissions
        Permission::create(['name' => 'user-index']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-read']);
        Permission::create(['name' => 'user-update']);
        Permission::create(['name' => 'user-block']);
        Permission::create(['name' => 'user-action']);
        Permission::create(['name' => 'user-unblock']);
        Permission::create(['name' => 'user-destroy']);

        //event permissions
        Permission::create(['name' => 'event-index']);
        Permission::create(['name' => 'event-create']);
        Permission::create(['name' => 'event-read']);
        Permission::create(['name' => 'event-publish']);
        Permission::create(['name' => 'event-unpublish']);
        Permission::create(['name' => 'event-approve']);
        Permission::create(['name' => 'event-unapproved']);
        Permission::create(['name' => 'event-update']);
        Permission::create(['name' => 'event-destroy']);
        Permission::create(['name' => 'event-restore']);
        Permission::create(['name' => 'event-force-delete']);

        //post permissions
        Permission::create(['name' => 'post-index']);
        Permission::create(['name' => 'post-create']);
        Permission::create(['name' => 'post-read']);
        Permission::create(['name' => 'post-publish']);
        Permission::create(['name' => 'post-unpublish']);
        Permission::create(['name' => 'post-approve']);
        Permission::create(['name' => 'post-unapproved']);
        Permission::create(['name' => 'post-update']);
        Permission::create(['name' => 'post-destroy']);
        Permission::create(['name' => 'post-restore']);
        Permission::create(['name' => 'post-force-delete']);

        //article permissions
        Permission::create(['name' => 'article-index']);
        Permission::create(['name' => 'article-create']);
        Permission::create(['name' => 'article-read']);
        Permission::create(['name' => 'article-publish']);
        Permission::create(['name' => 'article-unpublish']);
        Permission::create(['name' => 'article-approve']);
        Permission::create(['name' => 'article-unapproved']);
        Permission::create(['name' => 'article-update']);
        Permission::create(['name' => 'article-destroy']);
        Permission::create(['name' => 'article-restore']);
        Permission::create(['name' => 'article-force-delete']);

        //story permissions
        Permission::create(['name' => 'story-index']);
        Permission::create(['name' => 'story-create']);
        Permission::create(['name' => 'story-read']);
        Permission::create(['name' => 'story-publish']);
        Permission::create(['name' => 'story-unpublish']);
        Permission::create(['name' => 'story-approve']);
        Permission::create(['name' => 'story-unapproved']);
        Permission::create(['name' => 'story-update']);
        Permission::create(['name' => 'story-destroy']);
        Permission::create(['name' => 'story-restore']);
        Permission::create(['name' => 'story-force-delete']);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**************** create roles ********************/
        Role::create(['name' => 'super-admin'])->syncPermissions(Permission::all()); //my role only
        Role::create(['name' => 'editor'])->syncPermissions(Permission::all()); //secretary role
        Role::create(['name' => 'moderator'])->syncPermissions(['event-index', 'event-create', 'event-read', 'event-update', 'event-publish', 'event-unpublish', 'event-approve', 'event-unapproved', 'event-destroy', 'post-index', 'post-create', 'post-read',
            'post-update', 'post-publish', 'post-unpublish', 'post-approve', 'post-unapproved', 'article-index', 'article-read', 'story-index', 'story-create', 'story-read', 'story-update', 'story-publish', 'story-unpublish', 'story-approve', 'story-unapproved']); //moderator of posts
        Role::create(['name' => 'admin'])->syncPermissions(); //admin role
        Role::create(['name' => 'user'])->syncPermissions(['event-index', 'event-create', 'event-read', 'event-update', 'post-index', 'post-create', 'post-read',
            'post-update', 'article-index', 'article-read', 'story-index', 'story-create', 'story-read', 'story-update']); //user role
        Role::create(['name' => 'guest'])->syncPermissions('mail-send');
    }
}
