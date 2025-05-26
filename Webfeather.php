<?php
$LOGIN_ENABLED = true;
$valid_users = [
    'DefaultUser' => 'DefaultPasswd',
];
if ($LOGIN_ENABLED && (
    !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
    || !isset($valid_users[$_SERVER['PHP_AUTH_USER']])
    || $valid_users[$_SERVER['PHP_AUTH_USER']] !== $_SERVER['PHP_AUTH_PW']
    )
) {

    header('WWW-Authenticate: Basic realm="Webfeather"');
    header('HTTP/1.0 401 Unauthorized');
    echo '401 Unauthorized';
    exit;
}
?>

<?php
$modules = [];
$modulesDir = __DIR__ . '/modules';

foreach (glob("$modulesDir/*.php") as $file) {
    include $file;
    if (isset($module['route']) && isset($module['render']) && is_callable($module['render'])) {
        $modules[$module['route']] = $module;
    }
}

$path = array_key_first($_GET);
$active = $path ? ($modules[$path] ?? null) : null;
if (!$active && isset($modules['main'])) {
    header('Location: ?main');
    exit;
}
?>

<?php
$lang = include __DIR__ . '/lang/ru.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($active['name']) ?> - WebFeather</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <style>@import url(https://fonts.bunny.net/css?family=urbanist:400,600);*{box-sizing:border-box}body{overflow:hidden;margin:0;font-family:sans-serif;display:flex;height:100dvh}aside{height:100dvh;width:fit-content;background:#222;color:#fff;padding:10px}aside a{display:block;color:#fff;text-decoration:none;margin:5px 0}aside a:hover{text-decoration:underline}main{flex:1;padding:20px;overflow:auto}nav{display:none}@media (max-width:700px){[x-cloak]{display:none!important}aside{position:fixed;height:fit-content;bottom:0;right:0;padding-bottom:48px}body{flex-direction:column}nav{display:block;z-index:99}}nav{background-color:#222;color:#fff;height:48px;width:100dvw;position:sticky;display:flex}.menu-btn{background:#fff0;color:#fff;border:0;height:100%;width:100%}button,input{cursor:pointer}</style>
</head>

<body x-data="{ open: false, screen: window.innerWidth }"
    x-init="window.addEventListener('resize', () => screen = window.innerWidth)">
    <aside x-show="screen > 700 || open" x-transition @click.outside="if (screen <= 700) open = false" x-cloak>
        <svg width="188.542" height="59.059" viewBox="0 0 49.885 15.626" xmlns="http://www.w3.org/2000/svg">
            <g fill="#fff"><text style="-inkscape-font-specification:'Adwaita Sans Italic';text-align:start" x="88.482"
                    y="124.47" font-style="italic" font-size="8.467" font-family="Adwaita Sans" stroke-width=".265"
                    transform="translate(-82.374 -113.486)">
                    <tspan style="-inkscape-font-specification:Urbanist" x="88.482" y="124.47" font-style="normal"
                        font-weight="400" font-family="Urbanist">Webfeather</tspan>
                </text>
                <path
                    d="M1.13 12.124c-.24-3.569 1.476-6.906 2.6-10.22-.528 3.174-2.084 6.493-1.696 9.669C3.976 10.68 4.751 6.77 4.278 0 2.335 2.584-.467 4.968.066 10.903c0 0 .547 2.81 1.063 1.22z" />
                <path
                    d="M1.456 12.533c.522.486.309-.565.66-.727.88 1.847 1.674 2.947 2.297 3.82-1.07-1.102-1.634-1.333-2.957-3.093z" />
                <text style="-inkscape-font-specification:Urbanist;text-align:start" x="126.159" y="128.256"
                    font-size="4.233" font-family="Urbanist" stroke-width=".265"
                    transform="translate(-82.374 -113.486)">
                    <tspan style="-inkscape-font-specification:'Urbanist Semi-Bold'" x="126.159" y="128.256"
                        font-weight="600">v1</tspan>
                </text>
            </g>
        </svg>
        <?php if ($modules): ?>
            <?php foreach ($modules as $key => $mod): ?>
                <a href="?<?= urlencode($key) ?>"><?= htmlspecialchars($mod['name']) ?></a>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $lang['modules_not_found'] ?></p>
        <?php endif; ?>
    </aside>
    <main>
        <?php if ($active): ?>
            <?php $active['render'](); ?>
        <?php else: ?>
            <h2><?= $lang['welcome_title'] ?></h2>
            <p><?= $lang['welcome_desc'] ?></p>
        <?php endif; ?>
    </main>
    <nav x-show="screen <= 700" style="display: none;">
        <button class="menu-btn" @click="history.back()">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
        </button>
        <?php if (isset($modules['main'])): ?>
            <button class="menu-btn" onclick="location.href='?main'">
                <span class="material-symbols-outlined">home</span>
            </button>
        <?php endif; ?>
        <button @click="open = !open" class="menu-btn" aria-label="Меню">
            <span class="material-symbols-outlined">
                menu
            </span>
        </button>
    </nav>
</body>

</html>