<?php
// ============================================
//  Webfeather project version 2
//  Copyright 2024-2026 Atarwn Gard 
//                       <atarwn@qwaderton.org>
//  Licensed under GNU AGPL v3 (see LICENSE)
//            https://qwaderton.org/webfeather
// ============================================

// CONFIGURATION
require_once __DIR__ . '/config.php';
$WF_VERSION = 'v2';

// AUTHENTICATION (HTTP Basic — kept for "just works" use cases)
if ($LOGIN_ENABLED && (
    !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
    || !isset($valid_users[$_SERVER['PHP_AUTH_USER']])
    || $valid_users[$_SERVER['PHP_AUTH_USER']] !== $_SERVER['PHP_AUTH_PW']
)) {
    header('WWW-Authenticate: Basic realm="' . $WF_TITLE . '"');
    header('HTTP/1.0 401 Unauthorized');
    die('401 Unauthorized');
}

// MODULE LOADING
$modules    = [];
$modulesDir = __DIR__ . '/modules';

foreach (glob("$modulesDir/*.php") as $file) {
    include $file;
    if (isset($module['route'], $module['render']) && is_callable($module['render'])) {
        $modules[$module['route']] = $module;
        unset($module); // avoid bleed-through between module files
    }
}

// ROUTING
$path   = array_key_first($_GET) ?: null;
$active = $path ? ($modules[$path] ?? null) : null;

if (!$active && isset($modules['main'])) {
    header('Location: ?main');
    exit;
}

// GROUPS (for sidebar)
$groups = [];
foreach ($modules as $k => $m) {
    if (!isset($m['show']) || $m['show']()) {
        $g = $m['group'] ?? 'Other';
        $groups[$g][$k] = $m;
    }
}

// PAGE TITLE
$pageTitle = $active
    ? htmlspecialchars($active['name']) . ' — ' . htmlspecialchars($WF_TITLE)
    : htmlspecialchars($WF_TITLE . ' ' . $WF_VERSION);

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($LANG) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="wf.css">
    <style>
        /* ── Webfeather shell ── */
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
            /* wf.css sets font & color; shell handles layout only */
        }

        #wf-sidebar {
            width: 200px;
            flex-shrink: 0;
            background: var(--bg-inset);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        #wf-sidebar-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-shrink: 0;
        }

        #wf-sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 0;
        }

        .wf-group-label {
            padding: 12px 16px 4px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-faint);
        }

        #wf-sidebar a {
            display: block;
            padding: 7px 16px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            border-left: 2px solid transparent;
            transition: color 0.1s, background 0.1s;
        }

        #wf-sidebar a:hover {
            color: var(--text);
            background: var(--bg-alt);
            text-decoration: none;
        }

        #wf-sidebar a.active {
            color: var(--text);
            border-left-color: var(--accent);
            background: var(--bg-alt);
        }

        #wf-main {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* ── Mobile bottom nav ── */
        #wf-bottom-nav {
            display: none;
        }

        #wf-bottom-nav svg {
            fill: var(--text);
        }

        @media (max-width: 700px) {
            body {
                flex-direction: column;
            }

            #wf-sidebar {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 48px;
                width: 100%;
                z-index: 100;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            #wf-sidebar.open {
                display: flex;
            }

            #wf-bottom-nav {
                display: flex;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                height: 48px;
                background: var(--bg-inset);
                border-top: 1px solid var(--border);
                z-index: 101;
            }

            #wf-bottom-nav button {
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 1;
                background: transparent;
                border: none;
                color: var(--text-muted);
                font-size: 20px;
                cursor: pointer;
                margin: 0;
                padding: 0;
                text-transform: none;
                letter-spacing: normal;
                font-weight: normal;
                border-radius: 0;
            }

            #wf-bottom-nav button:hover {
                background: var(--bg-alt);
                color: var(--text);
            }

            #wf-main {
                padding-bottom: 64px;
            }
        }
    </style>
</head>
<body>

    <div id="wf-sidebar">
        <div id="wf-sidebar-header">
            <?= htmlspecialchars($WF_TITLE . ' ' . $WF_VERSION) ?>
        </div>
        <nav id="wf-sidebar-nav" aria-label="Modules">
            <?php foreach ($groups as $gname => $mods): ?>
                <div class="wf-group-label"><?= htmlspecialchars($gname) ?></div>
                <?php foreach ($mods as $key => $mod):
                    $isActive = ($path === $key);
                ?>
                    <a href="?<?= urlencode($key) ?>"
                       <?= $isActive ? 'class="active" aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($mod['name']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
    </div>

    <main id="wf-main">
        <?php if ($active): ?>
            <?php $active['render'](); ?>
        <?php else: ?>
            <h2><?= htmlspecialchars($WF_TITLE) ?></h2>
            <p class="text-muted">Select a module from the sidebar.</p>
        <?php endif; ?>
    </main>

    <nav id="wf-bottom-nav" aria-label="Navigation">
        <button onclick="history.back()" title="Back">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="m313-440 196 196q12 12 11.5 28T508-188q-12 11-28 11.5T452-188L188-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l264-264q11-11 27.5-11t28.5 11q12 12 12 28.5T508-715L313-520h447q17 0 28.5 11.5T800-480q0 17-11.5 28.5T760-440H313Z"/></svg>
        </button>
        <?php if (isset($modules['main'])): ?>
            <button onclick="location.href='?main'" title="Home">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M160-200v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H600q-17 0-28.5-11.5T560-160v-200q0-17-11.5-28.5T520-400h-80q-17 0-28.5 11.5T400-360v200q0 17-11.5 28.5T360-120H240q-33 0-56.5-23.5T160-200Z"/></svg>
            </button>
        <?php endif; ?>
        <button onclick="document.getElementById('wf-sidebar').classList.toggle('open')" title="Menu">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M160-240q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320h640q17 0 28.5 11.5T840-280q0 17-11.5 28.5T800-240H160Zm0-200q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h640q17 0 28.5 11.5T840-480q0 17-11.5 28.5T800-440H160Zm0-200q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720h640q17 0 28.5 11.5T840-680q0 17-11.5 28.5T800-640H160Z"/></svg>
        </button>
    </nav>

    <script>
        // Close sidebar on link tap (mobile)
        if (window.innerWidth <= 700) {
            document.querySelectorAll('#wf-sidebar a').forEach(function (link) {
                link.addEventListener('click', function () {
                    document.getElementById('wf-sidebar').classList.remove('open');
                });
            });
        }
    </script>

</body>
</html>