<?php
/**
 * Plugin Name: TWW Protein
 * Author: The Wellness Way
 * Description: A plugin to calculate protein intake.
 * Version: 1.0.0
 */
if(!defined('ABSPATH')) {
    exit;
}

if(!defined('TWWC_ASSETS_VERSION')) {
    define('TWWC_ASSETS_VERSION', '1.0.1');
}

if(!defined('TWWC_PROTEIN_PLUGIN_PATH')) {
    define('TWWC_PROTEIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

iF(!defined('TWWC_PROTEIN_PLUGIN_URL')) {
    define('TWWC_PROTEIN_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once 'vendor/autoload.php';

 class TwwcProtein {
 }
 
 use TwwcProtein\Shortcodes\TwwcProteinCalculatorShortcode;

 use TwwcProtein\Admin\TwwcAdminMenu;
 use TwwcProtein\Options\TwwcOptions;

 add_action('init', function() {
        $twwcroteinCalculatorShortcode = new TwwcProteinCalculatorShortcode();
        $twwcrotein = new TwwcProtein();
        $twwAdminMenu = new TwwcAdminMenu();
        $twwAdminMenu->register_hooks();

        add_action('wp_enqueue_scripts', 'twwc_register_styles');
        add_action('wp_enqueue_scripts', 'twwc_register_scripts');

        function twwc_register_styles() {
            wp_register_style('tww-protein', TWWC_PROTEIN_PLUGIN_URL . 'resources/css/tww-protein.css', [], null, 'all');
            wp_enqueue_style('tww-protein');
        }

        function twwc_register_scripts() {
            wp_register_script('twwc-protein-object', TWWC_PROTEIN_PLUGIN_URL . 'resources/js/vars.js',  [], null, true);
            wp_enqueue_script('twwc-protein-object');

            $settings = TwwcOptions::get_option('settings', null);

            if($settings && is_array($settings) && count($settings)) {
                wp_localize_script('twwc-protein-object', 'twwc_object' , $settings );
            }

            wp_register_script('tww-protein', TWWC_PROTEIN_PLUGIN_URL . 'resources/build/dist/bundle.js', [], null, true);
            wp_enqueue_script('tww-protein');

            wp_register_script('tww-protein-main', TWWC_PROTEIN_PLUGIN_URL . 'resources/js/main.js', [], null, true);
            wp_enqueue_script('tww-protein-main');
        }
 });

 