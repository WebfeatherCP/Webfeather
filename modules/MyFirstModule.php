<?php
$module = [
    'route' => 'main',
    'name' => 'Example Module',
    'group' => 'Examples',
    
    // Control visibility in menu
    'showInMenu' => function() {
        // Show only for logged in users, or always, etc.
        return true;
    },
    
    // Main render function
    // You can just place function name here
    // Like 'render' => 'renderExample'
    'render' => function() {
        ?>
        <h1>Webfeather</h1>
        <div class="card">
            <h2><i class="fas fa-info-circle"></i> Welcome to Example Module</h2>
            <p>This is a demonstration of how to create modules for Webfeather. Each module is a self-contained PHP file that defines its routing, rendering, and behavior.</p>
        </div>
        
        <div class="grid grid-3">
            <div class="card">
                <h2><i class="fas fa-puzzle-piece"></i> Modular</h2>
                <p>Each module is independent and can be added or removed without affecting the core system.</p>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-eye"></i> Dynamic Menu</h2>
                <p>The menu automatically updates based on available modules and user permissions.</p>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-mobile-alt"></i> Responsive</h2>
                <p>Built-in mobile support with adaptive navigation.</p>
            </div>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-code"></i> Getting Started</h2>
            <p>To create a new module, create a PHP file in the <code>/modules/</code> directory with the following structure:</p>
            <pre style="background: var(--bg-dark); padding: 15px; border-radius: 5px; overflow-x: auto; color: var(--text-primary);">
&lt;?php
namespace MyName\MyProject\MyModule

$module = [
    'route' => 'mymodule',
    'name' => 'My Module',
    'group' => 'My Group',
    'show' => __NAMESPACE__ . '\showInMenu',
    'render' => __NAMESPACE__ . '\renderMyModule'
];

function showInMenu(): bool {
    return true;
}

funtion renderMyModule(): void {
    echo '&lt;h1&gt;Hello from my module!&lt;/h1&gt;';
}</pre>
        </div>
        <?php
    }
];