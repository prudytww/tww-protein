<?php
namespace TwwcProtein\Shortcodes;

use TwwcProtein\Options\TwwcOptions;
/**
 * Shortcode for the protein calculator form
 */
class TwwcProteinCalculatorShortcode {
    public function __construct() {
        add_shortcode('twwc_protein_calculator', [$this, 'render_shortcode']);
    }

    public function render_shortcode($atts, $content = null) {
        ob_start();
        $settings = TwwcOptions::get_option('settings', null);
        $protein_settings = TwwcOptions::get_option('protein_settings', null);

        $theme = $settings['theme_options']['default'] ?? 'compact';

        if($protein_settings && is_array($protein_settings) && count($protein_settings)) {
            if('compact' === $theme) {
                require_once TWWC_PROTEIN_PLUGIN_PATH . 'templates/protein-calculator-compact.php';
            } elseif('large' === $theme) {
                require_once TWWC_PROTEIN_PLUGIN_PATH . 'templates/protein-calculator-large.php';
            }
        } else {
            echo 'Calculator settings are empty. Please check the settings page.';
        }
        
        return ob_get_clean();
    }
}