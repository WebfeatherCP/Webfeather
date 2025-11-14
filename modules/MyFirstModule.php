<?php
$module = [
    'route' => 'example',
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
        <style>
            .card {
                background: var(--bg-darker);
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .card h2 {
                font-size: 20px;
                margin-bottom: 10px;
                color: var(--accent);
            }
            
            .card p {
                color: var(--text-secondary);
                line-height: 1.6;
            }
            
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: var(--accent);
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
                transition: background 0.2s;
            }
            
            .btn:hover {
                background: var(--accent-hover);
            }
            
            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
        </style>
        
        <div class="card">
            <h2><i class="fas fa-info-circle"></i> Welcome to Example Module</h2>
            <p>This is a demonstration of how to create modules for Webfeather. Each module is a self-contained PHP file that defines its routing, rendering, and behavior.</p>
        </div>
        
        <div class="grid">
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
$module = [
    'route' => 'mymodule',
    'name' => 'My Module',
    'group' => 'My Group',
    'show' => 'showInMenu'
    'render' => 'renderMyModule'
];

function showInMenu() {
    return true;
}

funtion renderMyModule() {
    echo '&lt;h1&gt;Hello from my module!&lt;/h1&gt;';
}
            </pre>
        </div>
        <?php
    }
];