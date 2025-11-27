<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Définir les ressources et leurs actions
        $resources = [
            'materiels' => ['view', 'create', 'update', 'delete', 'export', 'import'],
            'employees' => ['view', 'create', 'update', 'delete', 'export', 'import'],
            'services' => ['view', 'create', 'update', 'delete'],
            'attributions' => ['view', 'create', 'update', 'delete'],
            'materiel_types' => ['view', 'create', 'update', 'delete'],
            'accessories' => ['view', 'create', 'update', 'delete'],
        ];

        // Créer les permissions
        $permissions = [];
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                $permission = Permission::firstOrCreate(['name' => "{$action}_{$resource}"]);
                $permissions["{$action}_{$resource}"] = $permission;
            }
        }

        // Permissions spéciales
        $specialPermissions = [
            'view_dashboard',
            'view_widgets',
            'manage_users',
            'manage_roles',
        ];

        foreach ($specialPermissions as $permissionName) {
            $permissions[$permissionName] = Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Créer les rôles et assigner les permissions

        // 1. Super Admin - Accès total
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Gestion complète sauf gestion des rôles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = Permission::where('name', '!=', 'manage_roles')->get();
        $admin->syncPermissions($adminPermissions);

        // 3. Gestionnaire - Gestion des matériels et attributions
        $gestionnaire = Role::firstOrCreate(['name' => 'gestionnaire']);
        $gestionnairePermissions = [
            'view_dashboard',
            'view_widgets',
            // Matériels
            'view_materiels',
            'create_materiels',
            'update_materiels',
            'delete_materiels',
            'export_materiels',
            'import_materiels',
            // Employés
            'view_employees',
            'create_employees',
            'update_employees',
            'export_employees',
            'import_employees',
            // Services
            'view_services',
            'create_services',
            'update_services',
            // Attributions
            'view_attributions',
            'create_attributions',
            'update_attributions',
            'delete_attributions',
            // Types de matériel
            'view_materiel_types',
            'create_materiel_types',
            'update_materiel_types',
            // Accessoires
            'view_accessories',
            'create_accessories',
            'update_accessories',
            'delete_accessories',
        ];
        $gestionnaire->syncPermissions($gestionnairePermissions);

        // 4. Utilisateur - Consultation uniquement
        $utilisateur = Role::firstOrCreate(['name' => 'utilisateur']);
        $utilisateurPermissions = [
            'view_dashboard',
            'view_materiels',
            'view_employees',
            'view_services',
            'view_attributions',
            'view_materiel_types',
            'view_accessories',
        ];
        $utilisateur->syncPermissions($utilisateurPermissions);

        $this->command->info('Rôles et permissions créés avec succès !');
        $this->command->info('Rôles créés : super_admin, admin, gestionnaire, utilisateur');
    }
}
