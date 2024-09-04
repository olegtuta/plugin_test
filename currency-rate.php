<?php
/*
Plugin Name: Currency Rate Display
Description: Плагин для вывода текущего курса валютной пары.
Version: 1.0
Author: Oleg Kyzlasov
*/

use CurrencyRatePlugin\CurrencyRate;

/*
 *
 * Запрещаем доступ по прямой ссылке к плагину
*/
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-currency-rate.php';

$actions = [
    [
        'hook' => 'wp_enqueue_scripts',
        'trigger' => function () {
            wp_enqueue_style('currency-rate-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        },
    ],
    [
        'hook' => 'plugins_loaded',
        'trigger' => function () {
            $plugin = new CurrencyRate();
            $plugin->run();
        },
    ],
];

foreach ($actions as $action) {
    add_action($action['hook'], $action['trigger']);
}
