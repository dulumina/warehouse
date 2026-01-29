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
            // Main Menu
            [
                'name' => __('Dashboard'),
                'route' => 'dashboard',
                'active' => request()->routeIs('dashboard'),
                'roles' => '*',
                'icon' => 'ti-layout-dashboard'
            ],

            // Warehouse Management
            [
                'name' => __('Warehouses'),
                'route' => 'warehouses.index',
                'active' => request()->routeIs('warehouses.*'),
                'permission' => 'view warehouses',
                'icon' => 'ti-building-warehouse'
            ],

            // Product Management
            [
                'name' => __('Products'),
                'route' => 'products.index',
                'active' => request()->routeIs('products.*'),
                'permission' => 'view products',
                'icon' => 'ti-package'
            ],

            // Inventory Management
            [
                'name' => __('Inventory'),
                'icon' => 'ti-layout-list',
                'active' => request()->routeIs('inventory.*'),
                'permission' => 'view inventory',
                'submenu' => [
                    [
                        'name' => __('Stock Overview'),
                        'route' => 'inventory.index',
                        'active' => request()->routeIs('inventory.index'),
                        'permission' => 'view inventory'
                    ],
                    [
                        'name' => __('Low Stock'),
                        'route' => 'inventory.low-stock',
                        'active' => request()->routeIs('inventory.low-stock'),
                        'permission' => 'view inventory'
                    ],
                    [
                        'name' => __('Expiring Items'),
                        'route' => 'inventory.expiring',
                        'active' => request()->routeIs('inventory.expiring'),
                        'permission' => 'view inventory'
                    ],
                    [
                        'name' => __('Batches'),
                        'route' => 'batches.index',
                        'active' => request()->routeIs('batches.*'),
                        'permission' => 'view batch info'
                    ],
                ]
            ],

            // Stock Transactions
            [
                'name' => __('Stock Transactions'),
                'icon' => 'ti ti-arrows-left-right',
                'active' => request()->routeIs('stock-*'),
                'permission' => 'create stock in',
                'submenu' => [
                    [
                        'name' => __('Stock In'),
                        'route' => 'stock-ins.index',
                        'active' => request()->routeIs('stock-ins.*'),
                        'permission' => 'create stock in',
                        'icon' => 'ti-arrow-down-right'
                    ],
                    [
                        'name' => __('Stock Out'),
                        'route' => 'stock-outs.index',
                        'active' => request()->routeIs('stock-outs.*'),
                        'permission' => 'create stock out',
                        'icon' => 'ti-arrow-up-left'
                    ],
                    [
                        'name' => __('Stock Transfer'),
                        'route' => 'stock-transfers.index',
                        'active' => request()->routeIs('stock-transfers.*'),
                        'permission' => 'create stock transfer',
                        'icon' => 'ti-arrow-right'
                    ],
                    [
                        'name' => __('Stock Adjustment'),
                        'route' => 'stock-adjustments.index',
                        'active' => request()->routeIs('stock-adjustments.*'),
                        'permission' => 'create stock adjustment',
                        'icon' => 'ti-adjustments'
                    ],
                ]
            ],

            // Approvals (if user has approval permissions)
            [
                'name' => __('Approvals'),
                'icon' => 'ti-check',
                'active' => request()->routeIs('approvals.*'),
                'permission' => 'approve stock in',
                'submenu' => [
                    [
                        'name' => __('Stock In'),
                        'route' => 'approvals.stock-ins',
                        'active' => request()->routeIs('approvals.stock-ins'),
                        'permission' => 'approve stock in'
                    ],
                    [
                        'name' => __('Stock Out'),
                        'route' => 'approvals.stock-outs',
                        'active' => request()->routeIs('approvals.stock-outs'),
                        'permission' => 'approve stock out'
                    ],
                    [
                        'name' => __('Stock Transfer'),
                        'route' => 'approvals.stock-transfers',
                        'active' => request()->routeIs('approvals.stock-transfers'),
                        'permission' => 'approve stock transfer'
                    ],
                    [
                        'name' => __('Stock Adjustment'),
                        'route' => 'approvals.stock-adjustments',
                        'active' => request()->routeIs('approvals.stock-adjustments'),
                        'permission' => 'approve stock adjustment'
                    ],
                ]
            ],

            // Reports
            [
                'name' => __('Reports'),
                'route' => 'reports.index',
                'icon' => 'ti-chart-bar',
                'active' => request()->routeIs('reports.*'),
                'permission' => 'view stock reports',
                'submenu' => [
                    [
                        'name' => __('Stock Report'),
                        'route' => 'reports.stock',
                        'active' => request()->routeIs('reports.stock'),
                        'permission' => 'view stock reports'
                    ],
                    [
                        'name' => __('Movement Report'),
                        'route' => 'reports.movements',
                        'active' => request()->routeIs('reports.movements'),
                        'permission' => 'view movement reports'
                    ],
                    [
                        'name' => __('Valuation Report'),
                        'route' => 'reports.valuation',
                        'active' => request()->routeIs('reports.valuation'),
                        'permission' => 'view valuation reports'
                    ],
                ]
            ],
            // TAMBAHKAN INI DULU KE NavigationService:
            [
                'name' => __('Master Data'),
                'icon' => 'ti-database-import',
                'active' => request()->routeIs('categories.*', 'suppliers.*', 'units.*'),
                'permission' => 'manage categories',
                'submenu' => [
                    [
                        'name' => __('Categories'),
                        'route' => 'categories.index',
                        'active' => request()->routeIs('categories.*'),
                        'permission' => 'manage categories',
                        'icon' => 'ti-category'
                    ],
                    [
                        'name' => __('Suppliers'),
                        'route' => 'suppliers.index',
                        'active' => request()->routeIs('suppliers.*'),
                        'permission' => 'manage suppliers',
                        'icon' => 'ti-truck'
                    ],
                    [
                        'name' => __('Units'),
                        'route' => 'units.index',
                        'active' => request()->routeIs('units.*'),
                        'permission' => 'manage categories',
                        'icon' => 'ti-ruler'
                    ]
                ]
            ],
            // Settings Divider
            [
                'name' => __('Settings'),
                'icon' => 'ti-settings',
                'active' => request()->routeIs('users.*', 'roles.*', 'permissions.*'),
                'roles' => ['super admin', 'admin'],
                'submenu' => [
                    [
                        'name' => __('Users'),
                        'route' => 'users.index',
                        'active' => request()->routeIs('users.*'),
                        'roles' => ['super admin', 'admin'],
                        'icon' => 'ti-user'
                    ],
                    [
                        'name' => __('Roles'),
                        'route' => 'roles.index',
                        'active' => request()->routeIs('roles.*'),
                        'roles' => ['super admin', 'admin'],
                        'icon' => 'ti-lock'
                    ],
                    [
                        'name' => __('Permissions'),
                        'route' => 'permissions.index',
                        'active' => request()->routeIs('permissions.*'),
                        'roles' => ['super admin', 'admin'],
                        'icon' => 'ti-key'
                    ],
                ]
            ],
        ];

        return collect($menus)->filter(function ($item) use ($user) {
            if (!$user) return false;

            // Super Admin can access everything
            if ($user->hasRole('super admin')) {
                // Filter submenu items for super admin (show all)
                if (isset($item['submenu'])) {
                    $item['submenu'] = collect($item['submenu'])->filter(function ($subitem) use ($user) {
                        return true; // Show all submenu items for super admin
                    })->values()->all();
                }
                return true;
            }

            // Check roles first
            if (isset($item['roles'])) {
                if ($item['roles'] === '*') return true;
                return $user->hasAnyRole($item['roles']);
            }

            // Check permissions
            if (isset($item['permission'])) {
                return $user->hasPermissionTo($item['permission']);
            }

            // If has submenu, filter submenu items
            if (isset($item['submenu'])) {
                $item['submenu'] = collect($item['submenu'])->filter(function ($subitem) use ($user) {
                    if (isset($subitem['roles'])) {
                        return $user->hasAnyRole($subitem['roles']);
                    }
                    if (isset($subitem['permission'])) {
                        return $user->hasPermissionTo($subitem['permission']);
                    }
                    return true;
                })->values()->all();

                return count($item['submenu']) > 0;
            }

            return true;
        });
    }
}
