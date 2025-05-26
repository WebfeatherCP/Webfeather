<?php
$module = [
    'route' => 'MyFirstModule',
    'name' => 'Мой первый модуль',
    'render' => function () {
        renderModuleContents();
    }
];

function renderModuleContents() {
    echo <<<HTML
    <h3>Мой первый модуль!</h3>
    <p>Вся панель как на ладони</p>
    HTML;
}