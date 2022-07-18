<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nom',
    'column.guard_name' => 'Nom du Guard',
    'column.roles' => 'Rôles',
    'column.permissions' => 'Permissions',
    'column.updated_at' => 'Mis à jour à',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nom',
    'field.guard_name' => 'Nom du Guard',
    'field.permissions' => 'Permissions',
    'field.select_all.name' => 'Tout sélectionner',
    'field.select_all.message' => 'Activer toutes les autorisations pour ce rôle',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rôles',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rôle',
    'resource.label.roles' => 'Rôles',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */
    'section' => 'Entités',
    'resources' => 'Ressources',
    'widgets' => 'Widgets',
    'pages' => 'Pages',
    'custom' => 'Permissions personnalisées',

    /**
     * Role Setting Page
     */
    'page' => [
        'name' => 'Paramètres Shield',
        'icon' => 'heroicon-o-adjustments',
        'save' => 'Sauvegarder',
        'generate' => 'Sauvegarder & Générer',
        'load_default_settings' => 'Charger les paramètres par défaut',
        'cancel' => 'Annuler',
    ],
    'labels.super_admin.toggle_input' => 'Rôle SuperAdmin',
    'labels.super_admin.text_input' => 'Nom du rôle',
    'labels.filament_user.toggle_input' => 'Rôle de l\'utilisateur Filament',
    'labels.filament_user.text_input' => 'Nom du rôle',
    'labels.role_policy.toggle_input' => 'Policy du rôle enregistré ?',
    'labels.role_policy.message' => 'Assurez-vous que la policy est enregistrée et que les permissions sont appliquées.',
    'labels.permission_prefixes.placeholder' => 'Préfixes d\'autorisation par défaut',
    'labels.permission_prefixes.resource' => 'Resource',
    'labels.permission_prefixes.resource.placeholder' => 'Ajouter ou supprimer des autorisations de ressources...',
    'labels.permission_prefixes.page' => 'Page',
    'labels.permission_prefixes.widget' => 'Widget',
    'labels.entities.placeholder' => 'Générateurs de permission d\'entité et onglets',
    'labels.entities.message' => 'Les générateurs et les onglets sont ',
    'labels.entities.resources' => 'Resources',
    'labels.entities.pages' => 'Pages',
    'labels.entities.widgets' => 'Widgets',
    'labels.entities.custom_permissions' => 'Permissions personnalisées',
    'labels.entities.custom_permissions.message' => 'L\'onglet est ',
    'labels.status.enabled' => 'Activé',
    'labels.status.disabled' => 'Désactivé',
    'labels.status.yes' => 'Oui',
    'labels.status.no' => 'Non',
    'labels.exclude.placeholder' => 'Mode d\'exclusion',
    'labels.exclude.message' => 'En activant le mode d\'exclusion, vous pouvez demander au générateur de permissions de ne pas créer de permissions pour les entités que vous sélectionnez..',
    'labels.exclude.resources' => 'Resources',
    'labels.exclude.resources.placeholder' => 'Sélectionnez les ressources ...',
    'labels.exclude.pages' => 'Pages',
    'labels.exclude.pages.placeholder' => 'Sélectionnez les pages ...',
    'labels.exclude.widgets' => 'Widgets',
    'labels.exclude.widgets.placeholder' => 'Sélectionnez les widgets  ...',

    /**
     * Messages
     */
    'forbidden' => 'Vous n\'avez pas la permission d\'accéder',
    'update' => 'Configuration shield mise à jour !',
    'generate' => 'Mise à jour de la configuration du shield et génération des autorisations et/ou des politiques en fonction de la nouvelle configuration..',
    'loaded_default_settings' => 'Chargement des paramètres par défaut de Shield.',
];
