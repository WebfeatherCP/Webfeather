<?php
$module = [
    'route' => 'github',
    'name' => 'Github',
    'group' => 'Links',
    'show' => function(): bool {
        return true;
    },
    'render' => function(): void {
        echo '<script>window.location.href = "https://github.com/WebfeatherCP/Webfeather";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0; url=https://github.com/WebfeatherCP/Webfeather"></noscript>';
        echo '<p>Перенаправление на <a href="https://github.com/WebfeatherCP/Webfeather">Webfeather</a>...</p>';
    }
];