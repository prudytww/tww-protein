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
        include TWWC_PROTEIN_PLUGIN_PATH . 'templates/protein-calculator.php';
        return ob_get_clean();
    }
}