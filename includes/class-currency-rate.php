<?php

namespace CurrencyRatePlugin;

if (!defined('ABSPATH')) {
    exit;
}

class CurrencyRate {

    const API_ENDPOINT = 'https://api.exchangerate-api.com/v4/latest/';

    public function __construct() {
        $this->run();
    }

    public function run() {
        add_shortcode('currency_rate', [$this, 'handle_shortcode']);
    }

    public function handle_shortcode($atts) {
        $atts = shortcode_atts(
            [
                'currency_a' => 'USD',
                'currency_b' => 'EUR',
            ],
            $atts,
            'currency_rate'
        );

        $rate = $this->get_currency_rate($atts['currency_a'], $atts['currency_b']);

        if (is_string($rate)) {
            return __('Ошибка получения данных.', 'currency-rate');
        }

        return sprintf(
            '<div class="currency-rate-container">
                Курс %s/%s: %s
            </div>',
            esc_html($atts['currency_a']),
            esc_html($atts['currency_b']),
            esc_html($rate)
        );
    }

    private function get_currency_rate($currency_a, $currency_b) {
        $response = wp_remote_get(self::API_ENDPOINT . strtoupper($currency_a));

        if (is_wp_error($response)) {
            return __('Ошибка запроса.', 'currency-rate');
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data['rates'][$currency_b] ?? __('Неверная валюта.', 'currency-rate');
    }
}
