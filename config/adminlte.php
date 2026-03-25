<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The default title of your admin panel, this goes into the title tag
    | of your page. You can override it per page with the title section.
    | You can optionally also specify a title prefix and/or postfix.
    |
    */

    'title' => 'Horae',

    'title_prefix' => '',

    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | This logo is displayed at the upper left corner of your admin panel.
    | You can use basic HTML here if you want. The logo has also a mini
    | variant, used for the mini side bar. Make it 3 letters or so
    |
    */

    'logo' => '<b>H</b>orae',

    'logo_mini' => '<b>H</b>or',

    /*
    |--------------------------------------------------------------------------
    | Skin Color
    |--------------------------------------------------------------------------
    |
    | Choose a skin color for your admin panel. The available skin colors:
    | blue, black, purple, yellow, red, and green. Each skin also has a
    | ligth variant: blue-light, purple-light, purple-light, etc.
    |
    */

    'skin' => 'blue',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Choose a layout for your admin panel. The available layout options:
    | null, 'boxed', 'fixed', 'top-nav'. null is the default, top-nav
    | removes the sidebar and places your menu in the top navbar
    |
    */

    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Collapse Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we choose and option to be able to start with a collapsed side
    | bar. To adjust your sidebar layout simply set this  either true
    | this is compatible with layouts except top-nav layout option
    |
    */

    'collapse_sidebar' => false,

    /*
    |--------------------------------------------------------------------------
    | Sidebar Mini
    |--------------------------------------------------------------------------
    |
    | This enables the sidebar mini feature. When collapsed, the sidebar will
    | show only icons. This is the feature activated by the hamburger button.
    | Values: 'lg', 'md', 'xs' (breakpoint at which sidebar mini is enabled)
    |
    */

    'sidebar_mini' => 'lg',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Register here your dashboard, logout, login and register URLs. The
    | logout URL automatically sends a POST request in Laravel 5.3 or higher.
    | You can set the request to a GET or POST with logout_method.
    | Set register_url to null if you don't want a register link.
    |
    */

    'dashboard_url' => 'eunomia/home',

    'logout_url' => 'logout',

    'logout_method' => null,

    'login_url' => 'login',

    'register_url' => '',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Specify your menu items to display in the left sidebar. Each menu item
    | should have a text and and a URL. You can also specify an icon from
    | Font Awesome. A string instead of an array represents a header in sidebar
    | layout. The 'can' is a filter on Laravel's built in Gate functionality.
    |
    */

    'menu' => [
        // IMPORTANTE: No dejar este array completamente vacío debido a un bug en AdminLTE 3.14.3
        // El menú real se crea dinámicamente en App/Providers/AppServiceProvider
        ['header' => 'MENÚ'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Choose what filters you want to include for rendering the menu.
    | You can add your own filters to this array after you've created them.
    | You can comment out the GateFilter if you don't want to use Laravel's
    | built in Gate functionality
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        // JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class, // Eliminado en AdminLTE 3.14+
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Choose which JavaScript plugins should be included. At this moment,
    | only DataTables is supported as a plugin. Set the value to true
    | to include the JavaScript file from a CDN via a script tag.
    |
    */

    'plugins' => [
        'datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/adminlte/plugins/datatables/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/adminlte/plugins/datatables/dataTables.bootstrap.css',
                ],
            ],
        ],
        'select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/adminlte/plugins/select2/select2.full.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/adminlte/plugins/select2/select2.min.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Fixed Sidebar
    |--------------------------------------------------------------------------
    */

    'layout_fixed_sidebar' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout Fixed Navbar
    |--------------------------------------------------------------------------
    */

    'layout_fixed_navbar' => null,

    /*
    |--------------------------------------------------------------------------
    | Layout Fixed Footer
    |--------------------------------------------------------------------------
    */

    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Layout Boxed
    |--------------------------------------------------------------------------
    */

    'layout_boxed' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout Top Nav
    |--------------------------------------------------------------------------
    */

    'layout_topnav' => false,

    /*
    |--------------------------------------------------------------------------
    | Extra Body Classes
    |--------------------------------------------------------------------------
    |
    | Here you can set extra classes for the body tag.
    | text-sm: Reduce font size to small (recommended for AdminLTE 3)
    |
    */

    'classes_body' => 'text-sm',
];
