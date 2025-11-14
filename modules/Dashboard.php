<?php
// Не рекомендуемый подход
// Устаревший модуль
// Используйте на свой страх и риск
$module = [
    'route' => 'dashboard',
    'name' => 'Статистика сервера',
    'render' => function () {
        $loadAverages = sys_getloadavg();
        $roundedLoads = array_map(function ($load) {
            return round($load, 2);
        }, $loadAverages);

        $data = [
            'Uptime' => shell_exec("uptime | awk '{ print $1 }'"),
            'Load Average' => implode(', ', $roundedLoads),
            'Memory Usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'Disk Free' => round(disk_free_space("/") / 1024 / 1024 / 1024, 2) . ' GB',
            'Disk Total' => round(disk_total_space("/") / 1024 / 1024 / 1024, 2) . ' GB',
            'PHP Version' => phpversion(),
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'
        ];

        $html = <<<HTML
        <div class="wf-module-status">
            <h2>Статистика сервера</h2>
            <table class="wf-table">
                <tbody>
        HTML;

        foreach ($data as $label => $value) {
            $html .= <<<HTML
                    <tr>
                        <td><strong>{$label}</strong></td>
                        <td>{$value}</td>
                    </tr>
            HTML;
        }

        $html .= <<<HTML
                </tbody>
            </table>
        </div>
        HTML;

        echo $html;
    }
];
