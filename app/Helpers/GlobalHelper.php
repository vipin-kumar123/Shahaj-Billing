<?php

use App\Models\SiteSetting;

if (!function_exists('formatDate')) {

    function formatDate($date)
    {
        // Load settings from DB
        $settings = SiteSetting::first();

        // If no format set, fallback
        $format = $settings->date_format ?? 'd-m-Y';

        return \Carbon\Carbon::parse($date)->format($format);
    }
}



if (!function_exists('moneyFormat')) {
    function moneyFormat($amount)
    {
        // $currency = SiteSetting::first()->currency_symbol ?? '₹';
        $currency = '₹';
        return $currency . number_format($amount, 2);
    }
}
