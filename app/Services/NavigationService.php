<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NavigationService
{
    public function getMenuItems()
    {
        $user = Auth::user();
        
        // Definisikan semua menu di sini
        $menus = [
            [
                'name' => __('Dashboard'),
                'route' => 'dashboard',
                'active' => request()->routeIs('dashboard'),
                'roles' => '*' // Semua orang bisa lihat
            ],
            [
                'name' => __('Users'),
                'route' => 'users.index',
                'active' => request()->routeIs('users.*'),
                'roles' => ['admin'],
                'dropdown' => 'Settings'
            ],
            [
                'name' => __('Roles'),
                'route' => 'roles.index',
                'active' => request()->routeIs('roles.*'),
                'roles' => ['admin'],
                'dropdown' => 'Settings'
            ],
            [
                'name' => __('Permissions'),
                'route' => 'permissions.index',
                'active' => request()->routeIs('permissions.*'),
                'roles' => ['admin'],
                'dropdown' => 'Settings'
            ],
        ];

        return collect($menus)->filter(function ($item) use ($user) {
            if ($item['roles'] === '*') return true;
            return $user && $user->hasAnyRole($item['roles']);
        });
    }
}