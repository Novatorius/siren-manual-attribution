<?php
/**
 * Plugin Name: Siren Affiliates - Manual Affiliate Payouts
 * Description: Award manual payouts to affiliates for offline conversions.
 * Version: 1.0.0
 * Author: Novatorius, LLC
 * License: GPLv2 or later
 * Requires PHP: 8.1
 * Requires at least: 6.0
 * Tested up to: 6.9
 */

// Exit if accessed directly.
use Novatorius\Siren\ManualAttribution\Initializer;
use Siren\Extensions\Core\Facades\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('siren_ready', function(){
    require_once(plugin_dir_path(__FILE__) . 'autoload.php');

    Extensions::add('ManualAffiliatePayouts',fn() => new Initializer(__FILE__));
});