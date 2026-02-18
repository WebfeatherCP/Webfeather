<?php
namespace Webfeather\Modules\ServerStats;

$module = [
    'route' => 'server_stats',
    'name'  => 'Server stats',
    'group' => 'Examples',
    'show'  => __NAMESPACE__ . '\showInMenu',
    'render' => __NAMESPACE__ . '\renderServerStats'
];

function showInMenu(): bool {
    return true;
}

function renderServerStats(): void {
    $load = sys_getloadavg();
    $cpuLoad = round($load[0], 2);

    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", preg_replace("/\s+/", " ", $free_arr[1]));
    $memTotal = round($mem[1] / 1024 / 1024, 2); // GB
    $memUsed = round($mem[2] / 1024 / 1024, 2);  // GB
    $memPercent = round(($memUsed / $memTotal) * 100, 1);

    $diskTotal = round(disk_total_space("/") / 1024 / 1024 / 1024, 2); // GB
    $diskFree = round(disk_free_space("/") / 1024 / 1024 / 1024, 2);   // GB
    $diskUsed = $diskTotal - $diskFree;
    $diskPercent = round(($diskUsed / $diskTotal) * 100, 1);

    $uptime = trim(shell_exec("uptime -p | cut -d ' ' -f 2-"));
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'];

    $html = <<<HTML
    <div class="card">
        <h2>Server stats</h2>
        
        <p><strong>CPU Usage (1m):</strong> {$cpuLoad}%</p>
        
        <p>
            <strong>RAM Usage:</strong> {$memUsed} GB / {$memTotal} GB ({$memPercent}%)
            <br>
            <div style="width: 300px; background: var(--bg-inset);">
                <div style="width: {$memPercent}%; background: #4caf50; height: 10px;"></div>
            </div>
        </p>

        <p>
            <strong>Disk usage:</strong> {$diskUsed} GB / {$diskTotal} GB ({$diskPercent}%)
            <div style="width: 300px; background: var(--bg-inset);">
                <div style="width: {$diskPercent}%; background: #2196f3; height: 10px;"></div>
            </div>
        </p>
        
        <br>
        <p>
            <small>Server up for {$uptime}</small>
            <br>
            <small>Powered by {$serverSoftware}</small>
        </p>
    </div>
    HTML;

    echo $html;
}