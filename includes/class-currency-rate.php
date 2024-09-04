<?php

namespace CurrencyRatePlugin;

if (!defined('ABSPATH')) {
    exit;
}

class CurrencyRate {

    const API_ENDPOINT = 'https://api.exchangerate-api.com/v4/latest/';

    public function run() {
        add_shortcode('currency_rate', [$this, 'handle_shortcode']);
    }

    public function handle_shortcode($atts) {
        $atts = shortcode_atts(
            [
                'currency' => 'RUB',
            ],
            $atts,
            'currency_rate'
        );

        $usd_rate = $this->get_currency_rate('USD', $atts['currency']);
        $eur_rate = $this->get_currency_rate('EUR', $atts['currency']);

        if (is_string($usd_rate) || is_string($eur_rate)) {
            return __('Ошибка получения данных.', 'currency-rate');
        }

        return sprintf(
            '<div class="currency-rate-container">
                %s<br>
                %s
            </div>',
            sprintf(
                __('Курс USD/%s: %s', 'currency-rate'),
                esc_html($atts['currency']),
                esc_html($usd_rate)
            ),
            sprintf(
                __('Курс EUR/%s: %s', 'currency-rate'),
                esc_html($atts['currency']),
                esc_html($eur_rate)
            )
        );
    }

    private function get_currency_rate($currency_a, $currency_b) {
        $response = wp_remote_get(self::API_ENDPOINT . strtoupper($currency_a));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data['rates'][$currency_b] ?? __('Неверная валюта.', 'currency-rate');
    }
}
