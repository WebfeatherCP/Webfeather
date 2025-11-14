<?php
// ============================================
// AUTHENTICATION
// ============================================
$LOGIN_ENABLED = false;
$LANG = 'ru';
$valid_users = ['DefaultUser' => 'DefaultPasswd'];

if ($LOGIN_ENABLED && (
    !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
    || !isset($valid_users[$_SERVER['PHP_AUTH_USER']])
    || $valid_users[$_SERVER['PHP_AUTH_USER']] !== $_SERVER['PHP_AUTH_PW']
)) {
    header('WWW-Authenticate: Basic realm="Webfeather"');
    header('HTTP/1.0 401 Unauthorized');
    die('401 Unauthorized');
}

// ============================================
// MODULE LOADING
// ============================================
$modules = [];
$modulesDir = __DIR__ . '/modules';

foreach (glob("$modulesDir/*.php") as $file) {
    include $file;
    if (isset($module['route'], $module['render']) && is_callable($module['render'])) {
        $modules[$module['route']] = $module;
    }
}

// ============================================
// ROUTING
// ============================================
$path = array_key_first($_GET);
$active = $path ? ($modules[$path] ?? null) : null;

if (!$active && isset($modules['main'])) {
    header('Location: ?main');
    exit;
}

// ============================================
// LANGUAGE
// ============================================
$lang = include __DIR__ . '/lang/'.$LANG.'.php';

?>
<!DOCTYPE html>
<?php echo "<html lang=".$LANG.">" ;?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $active ? htmlspecialchars($active['name']) : 'Webfeather' ?></title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: monospace;
            font-size: 14px;
            line-height: 1.6;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        aside {
            width: 200px;
            background: #222;
            color: #fff;
            padding: 20px;
            overflow-y: auto;
        }
        
        aside h1 {
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: normal;
        }
        
        aside a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 8px 5px;
            border-bottom: 1px solid #333;
        }
        
        aside a:hover {
            background: #333;
        }
        
        main {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        nav {
            display: none;
        }

        nav button {
            font-size: 24px;
        }

        aside h2 {
            margin-top:15px;
            font-size:14px;
            color:#bbb;
        }
        
        /* Mobile */
        @media (max-width: 700px) {
            body {
                flex-direction: column;
            }
            
            aside {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 50px;
                width: 100%;
                z-index: 100;
            }
            
            aside.open {
                display: block;
            }
            
            nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 50px;
                background: #222;
                border-top: 1px solid #444;
            }
            
            nav button {
                flex: 1;
                background: transparent;
                border: none;
                color: #fff;
                /* font-size: 16px; */
                cursor: pointer;
            }
            
            nav button:hover {
                background: #333;
            }
            
            main {
                padding-bottom: 70px;
            }
        }
    </style>
</head>
<body>
    <aside id="menu">
        <h1>Webfeather v1</h1>
       <?php
        $groups = [];
        foreach ($modules as $k => $m) {
            if (!isset($m['show']) || $m['show']()) {
                $g = $m['group'] ?? 'Other';
                $groups[$g][$k] = $m;
            }
        }
        ?>

        <?php foreach ($groups as $gname => $mods): ?>
            <h2>
                <?= htmlspecialchars($gname) ?>
            </h2>
            <?php foreach ($mods as $key => $mod): ?>
                <a href="?<?= urlencode($key) ?>"><?= htmlspecialchars($mod['name']) ?></a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </aside>
    
    <main>
        <?php if ($active): ?>
            <?php $active['render'](); ?>
        <?php else: ?>
            <h2><?= htmlspecialchars($lang['welcome_title']) ?></h2>
            <p><?= htmlspecialchars($lang['welcome_desc']) ?></p>
        <?php endif; ?>
    </main>
    
    <nav>
        <button onclick="history.back()">⇦</button>
        <?php if (isset($modules['main'])): ?>
            <button onclick="location.href='?main'">⌂</button>
        <?php endif; ?>
        <button onclick="toggleMenu()">☰</button>
    </nav>
    
    <script>
        function toggleMenu() {
            document.getElementById('menu').classList.toggle('open');
        }
        
        // Close menu on link click (mobile)
        if (window.innerWidth <= 700) {
            document.querySelectorAll('aside a').forEach(function(link) {
                link.addEventListener('click', function() {
                    document.getElementById('menu').classList.remove('open');
                });
            });
        }
    </script>
</body>
</html>