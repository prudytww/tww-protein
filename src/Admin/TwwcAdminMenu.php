<?php

namespace TwwcProtein\Admin;

use TwwcProtein\Options\TwwcOptions;

class TwwcAdminMenu {
    const PAGE_IDENTIFIER       = 'twwc-calculator';
    const PAGE_IDENTIFIER_PROTEIN = 'twwc-protein-calculator';
    const PAGE_TEMPLATE         = 'dashboard';
    const COMMON_SETTINGS_PAGE  = 'twwc-common-settings';
    const PROTEIN_CALCULATOR_SETTINGS_PAGE  = 'twwc-protein-calculator-settings';

    private $option_group = 'settings';

    private $option_name = 'settings';

    private $option_group_protein = 'protein_settings';

    private $option_name_protein = 'protein_settings';

    private $prefix;

    private $settings = [];

    private $protein_settings = [];

    private $activity_levels = [
        'Sedentary',
        'Lightly Active',
        'Moderately Active',
        'Very Active',
        'Super Active',
    ];

    /**
     * Schema, such as max character count, for our options
     *
     * @var array[]
     */
    private $fields = [
        'age_range' => [
            "max" => 24,
            'title' => "Age Range",
            "placeholder" => "Age Range",

        ],
        'height_range' => [
            "max" => 32,
            'title' => "Height Range",
            "placeholder" => "Height Range",
        ],
    ];

    public function __construct() {
        $settings = TwwcOptions::get_option($this->option_name, []);

        $this->prefix = TwwcOptions::PREFIX;
        $this->settings = $settings ?? [];

        $protein_settings = TwwcOptions::get_option($this->option_name_protein, []);
        $this->protein_settings = $protein_settings ?? [];

        $this->option_group = $this->prefix . $this->option_group;
        $this->option_name  = $this->prefix . $this->option_name;

        $this->option_group_protein = $this->prefix . $this->option_group_protein;
        $this->option_name_protein  = $this->prefix . $this->option_name_protein;
    }

    public function register_hooks()
    {
        add_action('admin_menu', [$this, 'register_pages']);
        add_action('admin_init', [$this, 'register_common_settings']);
    }

    public function register_pages() {
        $manage_capability = $this->get_manage_capability();
        $page_identifier = $this->get_page_identifier();

        $menu = add_menu_page(
            'TWW Calculator: ' . __('Dashboard', 'twwc-calculator'),
            'TWW Calculator',
            $manage_capability,
            $page_identifier,
            [$this, 'show_page'],
            'dashicons-heart',
            98
        );

        $submenu = add_submenu_page(
            $page_identifier,
            'TWW Calculator: ' . __('Settings', 'twwc-calculator'),
            __('Settings', 'twwc-calculator'),
            $manage_capability,
            self::COMMON_SETTINGS_PAGE,
            [$this, 'show_page'],
            1
        );

        $anotherSubmenu = add_submenu_page(
            $page_identifier,
            'TWW Calculator: ' . __('Protein Calculator', 'twwc-calculator'),
            __('Protein Calculator', 'twwc-calculator'),
            $manage_capability,
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            [$this, 'show_page'],
            2
        );

        add_action( 'load-' . $menu, [$this, 'do_admin_enqueue'] );
        add_action( 'load-' . $submenu, [$this, 'do_admin_enqueue'] );
        add_action( 'load-' . $anotherSubmenu, [$this, 'do_admin_enqueue'] );
    }

    public function do_admin_enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_style( 'twwc-admin-js', TWWC_PROTEIN_PLUGIN_URL . 'resources/css/twwc-admin.css', [], '1.32.2', 'all' );
        wp_enqueue_script( 'twwc-admin-js', TWWC_PROTEIN_PLUGIN_URL . 'resources/js/twwc-admin.js', [], '1.32.2', true );

        wp_enqueue_script( 'twwc-admin-js', TWWC_PROTEIN_PLUGIN_URL . 'resources/js/twwc-admin.js', [], '1.32.2', true );

        //localize settings in script
        wp_localize_script('twwc-admin-js', 'twwc_admin_object', [
            'settings' => $this->settings,
            'protein_settings' => $this->protein_settings,
        ]);
    }

    public function register_common_settings() {
        /**
         * 
         * 
         * 
         * Common settings
         */
        add_settings_section(
            'twwc-common-settings-section',
            __('TWWC Settings', 'twwc-calculator'),
            [$this, 'common_settings_output'],
            self::COMMON_SETTINGS_PAGE,
            []
        );

        add_settings_field(
            'theme_options',
            '<span class="required">*</span> '. __('Default Theme', 'twwc-calculator'),
            [$this, 'default_theme_callback'],
            self::COMMON_SETTINGS_PAGE,
            'twwc-common-settings-section',
            ['max' => 32]
        );

        /**
         * 
         * 
         * 
         * Protein Calculator Settings
         */

        

        add_settings_section(
            'twwc-protein-calculator-settings-section',
            __('TWWC Settings', 'twwc-calculator'),
            [$this, 'protein_calculator_settings_output'],
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            []
        );

        
        add_settings_field(
            'system',
            '<span class="required">*</span> '. __('Default Units & Measurement', 'twwc-calculator'),
            [$this, 'default_system_callback'],
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            'twwc-protein-calculator-settings-section',
            ['max' => 32]
        );

        add_settings_field(
            'weight',
            '<span class="required">*</span> '. __('Weight', 'twwc-calculator'),
            [$this, 'weight_callback'],
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            'twwc-protein-calculator-settings-section',
            ['max' => 32]
        );

        add_settings_field(
            'pregnant',
            '<span class="required">*</span> '. __('Pregnant/Lactating', 'twwc-calculator'),
            [$this, 'pregnant_callback'],
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            'twwc-protein-calculator-settings-section',
            ['max' => 32]
        );

        add_settings_field(
            'activity_level',
            '<span class="required">*</span> '. __('Activity Level', 'twwc-calculator'),
            [$this, 'activity_level_callback'],
            self::PROTEIN_CALCULATOR_SETTINGS_PAGE,
            'twwc-protein-calculator-settings-section',
            ['max' => 32]
        );

        register_setting('twwc-common-settings-options', $this->option_group, [$this, 'validate_options']);
        register_setting('twwc-protein-calculator-options', $this->option_group_protein, [$this, 'convert_and_update_options']);
    }

    public function convert_and_update_options(array $input) {
        $schema = $this->fields;
        $valid_input = $this->settings && is_array($this->settings) ? $this->settings : [];

        $system = isset($input['system']) ? $input['system'] : 'imperial';
        $valid_input['system'] = $system;
        $valid_input['pregnant'] = 0 != $input['pregnant'] ? $input['pregnant'] : '';
        $valid_input['pregnant']['enable'] = isset($input['pregnant']['enable']) ? $input['pregnant']['enable'] : 0;
        $valid_input['pregnant_lactating'] = (0 !=  $input['pregnant_lactating']) ?  $input['pregnant_lactating'] : '';
        $valid_input['defaults'] = $input['defaults'] ?? [];
        $valid_input['content'] = isset($input['content']) && is_array($input['content']) ? $input['content'] : [];

        if('imperial' === $system) {
            $multiplier_weight_lbs = $input['multiplier_weight_lbs'] ?? '';     
            $valid_input['multiplier_weight_lbs']   = $multiplier_weight_lbs;
            $valid_input['multiplier_weight_kg']    = $this->convert_multiplier($multiplier_weight_lbs, 'imperial');

            $multiplier_weight_high_lbs = $input['multiplier_weight_high_lbs'] ?? '';
            $valid_input['multiplier_weight_high_lbs']   = $multiplier_weight_high_lbs;
            $valid_input['multiplier_weight_high_kg']    = $this->convert_multiplier($multiplier_weight_high_lbs, 'imperial');
           

            foreach($input['activity_level'] as $activity_level => $value) {
                if(!$value || !array_key_exists('m_' . $activity_level . '_lbs', $value)) {
                    continue;
                }
                $goal = $value['goal'] ?? null;


                $valid_input['activity_level'][$activity_level]['label'] = $input['activity_level'][$activity_level]['label'] ?? '';
                $valid_input['activity_level'][$activity_level]['enable'] = $input['activity_level'][$activity_level]['enable'] ?? 0;

                $activity_level = str_replace(' ', '_', strtolower($activity_level));

                $m_activity_level_lbs = 0 != $value['m_' . $activity_level . '_lbs'] ? $value['m_' . $activity_level . '_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_lbs'] =  $m_activity_level_lbs;
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_kg'] = $this->convert_multiplier($m_activity_level_lbs, 'imperial');

                $m_activity_level_high_lbs = 0 != $value['m_' . $activity_level . '_high_lbs'] ? $value['m_' . $activity_level . '_high_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_high_lbs'] =  0 != $m_activity_level_high_lbs ? $m_activity_level_high_lbs : '';
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_high_kg'] = $this->convert_multiplier($m_activity_level_high_lbs, 'imperial');

                $goal_maintain_lbs = 0 != $goal['m_maintain_lbs'] ? $goal['m_maintain_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_lbs'] = $goal_maintain_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_kg'] = $this->convert_multiplier($goal_maintain_lbs, 'imperial');

                $goal_toning_lbs = 0 != $goal['m_toning_lbs'] ? $goal['m_toning_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_lbs'] = $goal_toning_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_kg'] = $this->convert_multiplier($goal_toning_lbs, 'imperial');

                $goal_muscle_growth_lbs = 0 != $goal['m_muscle_growth_lbs'] ? $goal['m_muscle_growth_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_lbs'] = $goal_muscle_growth_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_kg'] = $this->convert_multiplier($goal_muscle_growth_lbs, 'imperial');

                $goal_weight_loss_lbs = 0 != $goal['m_weight_loss_lbs'] ? $goal['m_weight_loss_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_lbs'] = $goal_weight_loss_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_kg'] = $this->convert_multiplier($goal_weight_loss_lbs, 'imperial');

                //Now add a high value as the previous are low values
                $goal_maintain_high_lbs = 0 != $goal['m_maintain_high_lbs'] ? $goal['m_maintain_high_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_high_lbs'] = $goal_maintain_high_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_high_kg'] = $this->convert_multiplier($goal_maintain_high_lbs, 'imperial');

                $goal_toning_lbs = 0 != $goal['m_toning_high_lbs'] ? $goal['m_toning_high_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_high_lbs'] = $goal_toning_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_high_kg'] = $this->convert_multiplier($goal_toning_lbs, 'imperial');

                $goal_muscle_growth_lbs = 0 != $goal['m_muscle_growth_high_lbs'] ? $goal['m_muscle_growth_high_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_high_lbs'] = $goal_muscle_growth_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_high_kg'] = $this->convert_multiplier($goal_muscle_growth_lbs, 'imperial');

                $goal_weight_loss_lbs = 0 != $goal['m_weight_loss_high_lbs'] ? $goal['m_weight_loss_high_lbs'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_high_lbs'] = $goal_weight_loss_lbs;
                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_high_kg'] = $this->convert_multiplier($goal_weight_loss_lbs, 'imperial');
            }
        } else {
            $multiplier_weight_kg = $input['multiplier_weight_kg'] ?? '';
            $valid_input['multiplier_weight_kg']    = $multiplier_weight_kg;
            $valid_input['multiplier_weight_lbs']   = $this->convert_multiplier($multiplier_weight_kg, 'metric');

            $multiplier_weight_high_kg = $input['multiplier_weight_high_kg'] ?? '';
            $valid_input['multiplier_weight_high_kg']    = $multiplier_weight_high_kg;
            $valid_input['multiplier_weight_high_lbs']   = $this->convert_multiplier($multiplier_weight_high_kg, 'metric');

            foreach($input['activity_level'] as $activity_level => $value) {
                if(!$value || !array_key_exists('m_' . $activity_level . '_kg', $value)) {
                    continue;
                }
                $goal = $value['goal'] ?? '';
                $valid_input['activity_level'][$activity_level]['label'] = $input['activity_level'][$activity_level]['label'] ?? '';
                $valid_input['activity_level'][$activity_level]['enable'] = $input['activity_level'][$activity_level]['enable'] ?? 0;

                $activity_level = str_replace(' ', '_', strtolower($activity_level));

                $m_activity_level_kg = 0 != $value['m_' . $activity_level . '_kg'] ? $value['m_' . $activity_level . '_kg'] : '';
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_kg'] = $m_activity_level_kg;
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_lbs'] = $this->convert_multiplier($m_activity_level_kg, 'metric');

                $m_activity_level_high_kg = 0 != $value['m_' . $activity_level . '_high_kg'] ? $value['m_' . $activity_level . '_high_kg'] : '';
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_high_kg'] = $m_activity_level_high_kg;
                $valid_input['activity_level'][$activity_level]['m_' . $activity_level . '_high_lbs'] = $this->convert_multiplier($m_activity_level_high_kg, 'metric');

                $goal_maintain_kg = 0 != $goal['m_maintain_kg'] ? $goal['m_maintain_kg'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_kg'] = $goal_maintain_kg;
                $valid_input['activity_level'][$activity_level]['goal']['m_maintain_lbs'] = $this->convert_multiplier($goal_maintain_kg, 'metric');

                $goal_toning_kg = 0 != $goal['m_toning_kg'] ? $goal['m_toning_kg'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_kg'] = $goal_toning_kg;
                $valid_input['activity_level'][$activity_level]['goal']['m_toning_lbs'] = $this->convert_multiplier($goal_toning_kg, 'metric');

                $goal_muscle_growth_kg = 0 != $goal['m_muscle_growth_kg'] ? $goal['m_muscle_growth_kg'] : '';
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_kg'] = $goal_muscle_growth_kg;
                $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_lbs'] = $this->convert_multiplier($goal_muscle_growth_kg, 'metric');

                $goal_weight_loss_kg = 0 != $goal['m_weight_loss_kg'] ? $goal['m_weight_loss_kg'] : '';

                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_kg'] = $goal_weight_loss_kg;
                $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_lbs'] = $this->convert_multiplier($goal_weight_loss_kg, 'metric');

                //Now add a high value as the previous are low values
                if(array_key_exists('m_maintain_high_kg', $goal) && 0 != $goal['m_maintain_high_kg']) {
                    $goal_maintain_high_kg = $goal['m_maintain_high_kg'] && 0 != $goal['m_maintain_high_kg'] ? $goal['m_maintain_high_kg'] : '';
                    $valid_input['activity_level'][$activity_level]['goal']['m_maintain_high_kg'] = $goal_maintain_high_kg;
                    $valid_input['activity_level'][$activity_level]['goal']['m_maintain_high_lbs'] = $this->convert_multiplier($goal_maintain_high_kg, 'metric');
                } else {
                    $goal_maintain_high_kg = '';
                }

                if(array_key_exists('m_toning_high_kg', $goal) && 0 != $goal['m_toning_high_kg']) {
                    $goal_toning_high_kg = $goal['m_toning_high_kg'] && 0 != $goal['m_toning_high_kg'] ? $goal['m_toning_high_kg'] : '';
                    $valid_input['activity_level'][$activity_level]['goal']['m_toning_high_kg'] = $goal_toning_high_kg;
                    $valid_input['activity_level'][$activity_level]['goal']['m_toning_high_lbs'] = $this->convert_multiplier($goal_toning_high_kg, 'metric');
                } else {
                    $goal_toning_high_kg = '';
                }
                
                if(array_key_exists('m_muscle_growth_high_kg', $goal) && 0 != $goal['m_muscle_growth_high_kg']) {
                    $goal_muscle_growth_high_kg = $goal['m_muscle_growth_high_kg'] && 0 != $goal['m_muscle_growth_high_kg'] ? $goal['m_muscle_growth_high_kg'] : '';
                    $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_high_kg'] = $goal_muscle_growth_high_kg;
                    $valid_input['activity_level'][$activity_level]['goal']['m_muscle_growth_high_lbs'] = $this->convert_multiplier($goal_muscle_growth_high_kg, 'metric');
                } else {
                    $goal_muscle_growth_high_kg = '';
                }

                if(array_key_exists('m_weight_loss_high_kg', $goal) && 0 != $goal['m_weight_loss_high_kg']) {
                    $goal_weight_loss_high_kg = $goal['m_weight_loss_high_kg'] && 0 != $goal['m_weight_loss_high_kg'] ? $goal['m_weight_loss_high_kg'] : '';
                    $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_high_kg'] = $goal_weight_loss_high_kg;
                    $valid_input['activity_level'][$activity_level]['goal']['m_weight_loss_high_lbs'] = $this->convert_multiplier($goal_weight_loss_high_kg, 'metric');
                } else {
                    $goal_weight_loss_high_kg = '';
                }
            }
        }


        return $valid_input;
    }

    public function get_page_identifier()
    {
        return self::PAGE_IDENTIFIER;
    }

    public function get_manage_capability()
    {
        return 'manage_options';
    }

    public function show_page()
    {
        require_once TWWC_PROTEIN_PLUGIN_PATH . 'pages/' . self::PAGE_TEMPLATE . '.php';
    }

    /*
     * Common settings
     *
     *
     *
     */

    public function protein_calculator_settings_output() {
        echo '
        <p>Embed shortcode: <span id="embed-shortcode" class="twwc_embed_shortcode" data-clipboard-text="[twwc_protein_calculator]">&lt;/&gt; [twwc_protein_calculator]</span></p>
        <p><a class="protein-calculator__default" target="_blank" href="#">Populate Demo Data</a> - <a class="protein-calculator__clear" target="_blank" href="#">Clear Data</a>';
    }

    public function default_theme_callback() {
        $options = TwwcOptions::get_option($this->option_name);
        $value = isset($options['theme_options']['default']) ? $options['theme_options']['default'] : 'compact';

        echo "
        <div class='default-system'>
            <input id='theme_options_compact' class='protein-calculator__units-measurement' type='radio' name='" . esc_attr($this->option_name) . "[theme_options][default]' value='compact' " . ('compact' === $value ? 'checked' : '') . ">Compact</input>

            <input id='theme_options_large' class='protein-calculator__units-measurement' type='radio' name='" . esc_attr($this->option_name) . "[theme_options][default]' value='large' " . ('large' ===  $value ? 'checked' : '') . ">Large</input>
        </div>
        ";
    }

    public function default_system_callback() {
        $options = TwwcOptions::get_option($this->option_name_protein);
        $value = isset($options['system']) ? $options['system'] : 'imperial';
        echo "
            <div class='default-system'>
                <input class='protein-calculator__units-measurement' type='radio' name='" . esc_attr($this->option_name_protein) . "[system]' value='imperial'" . ('imperial' === $value ? 'checked' : '') . ">Imperial</input> 
                <input class='protein-calculator__units-measurement' type='radio' name='" . esc_attr($this->option_name_protein) . "[system]' value='metric'" . ('metric' ===  $value ? 'checked' : '') . ">Metric</input>
            </div>
        ";
    }

    public function pregnant_callback() {
        $settings = TwwcOptions::get_option($this->option_name_protein, []);

        $value = isset($settings['pregnant']) ? $settings['pregnant'] : '';
        $l_value = isset($settings['pregnant_lactating']) ? $settings['pregnant_lactating'] : '';
        echo "
            <div class='pregnant'>
                <div class='protein-calculator__form-group'>
                <span>Add an additional</span>
                <input width='75' class='protein-calculator__pregnant' type='number' name='" . esc_attr($this->option_name_protein) . "[pregnant]' value='".esc_attr($value)."'> <span>grams if pregnant</span>
                </div>
                <div class='protein-calculator__form-group'>
                <span>Add an additional</span>
                <input width='75' class='protein-calculator__pregnant' type='number' name='" . esc_attr($this->option_name_protein) . "[pregnant_lactating]' value='".esc_attr($l_value)."'>
                <span>grams if breastfeeding</span>
                </div>
            </div>
        ";
    }

    public function weight_callback() {
        $options = TwwcOptions::get_option($this->option_name_protein);
        $system = isset($options['system']) ? $options['system'] : 'imprial';
        
        $value = isset($options['multiplier_weight_lbs']) ? $options['multiplier_weight_lbs'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "
        <div class='protein-calculator__form-group'>
        <input class='protein-calculator__imperial-input' style='width: 100px' class='".esc_attr($class)."' class='protein-calculator__weight' id='weight-lbs' name='".esc_attr($this->option_name_protein)."[multiplier_weight_lbs]' type='number' step='.001' value='" . esc_attr($value) . "' placeholder='' ".esc_attr($max)." /> <span>g/lb</span>
        ";
    
        $value = isset($options['multiplier_weight_kg']) ? $options['multiplier_weight_kg'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "
        <input class='protein-calculator__metric-input' style='width: 100px' class='".esc_attr($class)."' class='protein-calculator__weight' id='weight-kg' name='".esc_attr($this->option_name_protein)."[multiplier_weight_kg]' type='number' step='.001' value='" . esc_attr($value) . "' placeholder='' ".esc_attr($max)." /> <span>g/kg</span>";

        //High values
        $value = isset($options['multiplier_weight_high_lbs']) ? $options['multiplier_weight_high_lbs'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "
        <input class='protein-calculator__imperial-input' style='width: 100px' class='".esc_attr($class)."' class='protein-calculator__weight' id='weight-lbs' name='".esc_attr($this->option_name_protein)."[multiplier_weight_high_lbs]' type='number' step='.001' value='" . esc_attr($value) . "' placeholder='' ".esc_attr($max)." /> <span>g/lb (high)</span>
        ";

        $value = isset($options['multiplier_weight_high_kg']) ? $options['multiplier_weight_high_kg'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "
        <input class='protein-calculator__metric-input' style='width: 100px' class='".esc_attr($class)."' class='protein-calculator__weight' id='weight-kg' name='".esc_attr($this->option_name_protein)."[multiplier_weight_high_kg]' type='number' step='.001' value='" . esc_attr($value) . "' placeholder='' ".esc_attr($max)." /> <span>g/kg (high)</span>
        </div>
        ";
    }

    public function activity_level_callback() {
        $options = TwwcOptions::get_option($this->option_name_protein);
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $system = isset($options['system']) ? $options['system'] : 'imperial';

        $activity_level_value = $options['activity_level'] ?? [];

        /// I want to make the following echo a for each loop for the activity levels
        foreach($this->activity_levels as $activity_level) {
            $activity_level = str_replace(' ', '_', strtolower($activity_level));

            $activity_level_label = $activity_level_value[$activity_level]['label'] ?? null;

            $activity_level_enabled = isset($activity_level_value[$activity_level]['enable']) 
                ? (intval($activity_level_value[$activity_level]['enable']) === 0 ? intval($activity_level_value[$activity_level]['enable']) : 1) 
                : 0;

            $activity_level_default = $options['defaults']['activity_level'] ?? '';

            $activity_level_value_lbs = $activity_level_value[$activity_level]['m_'. $activity_level .'_lbs'] ?? '';
            $activity_level_value_kg = $activity_level_value[$activity_level]['m_'. $activity_level .'_kg'] ?? '';
            $activity_level_value_high_kg = $activity_level_value[$activity_level]['m_'. $activity_level .'_high_kg'] ?? '';
            $activity_level_value_high_lbs = $activity_level_value[$activity_level]['m_'. $activity_level .'_high_lbs'] ?? '';

            $maintain_value_lbs = $activity_level_value[$activity_level]['goal']['m_maintain_lbs'] ?? '';
            $maintain_value_kg = $activity_level_value[$activity_level]['goal']['m_maintain_kg'] ?? '';
            $toning_value_lbs = $activity_level_value[$activity_level]['goal']['m_toning_lbs'] ?? '';
            $toning_value_kg = $activity_level_value[$activity_level]['goal']['m_toning_kg'] ?? '';
            $muscle_growth_value_lbs = $activity_level_value[$activity_level]['goal']['m_muscle_growth_lbs'] ?? '';
            $muscle_growth_value_kg = $activity_level_value[$activity_level]['goal']['m_muscle_growth_kg'] ?? '';
            $weight_loss_value_lbs = $activity_level_value[$activity_level]['goal']['m_weight_loss_lbs'] ?? '';
            $weight_loss_value_kg = $activity_level_value[$activity_level]['goal']['m_weight_loss_kg'] ?? '';

            $maintain_value_high_lbs = $activity_level_value[$activity_level]['goal']['m_maintain_high_lbs'] ?? '';
            $maintain_value_high_kg = $activity_level_value[$activity_level]['goal']['m_maintain_high_kg'] ?? '';
            $toning_value_high_lbs = $activity_level_value[$activity_level]['goal']['m_toning_high_lbs'] ?? '';
            $toning_value_high_kg = $activity_level_value[$activity_level]['goal']['m_toning_high_kg'] ?? '';
            $muscle_growth_value_high_lbs = $activity_level_value[$activity_level]['goal']['m_muscle_growth_high_lbs'] ?? '';
            $muscle_growth_value_high_kg = $activity_level_value[$activity_level]['goal']['m_muscle_growth_high_kg'] ?? '';
            $weight_loss_value_high_lbs = $activity_level_value[$activity_level]['goal']['m_weight_loss_high_lbs'] ?? '';
            $weight_loss_value_high_kg = $activity_level_value[$activity_level]['goal']['m_weight_loss_high_kg'] ?? '';

            echo "
            <div class='protein-calculator__form-group'>
                <div class='protein-calculator__label'>
                    <div class='protein-calculator__enable'>
                        <label for='activity-level'>".esc_html($activity_level)."</label>
                        <input type='checkbox' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][enable]' value='1' ".(isset($activity_level_enabled) && 1 == $activity_level_enabled ? 'checked' : '')." /> <span>Enable</span>
                        <input type='radio' name='".esc_attr($this->option_name_protein)."[defaults][activity_level]' value='".esc_html($activity_level)."' ".($activity_level_default == $activity_level ? 'checked' : '')." /> <span>Set as Default</span>
                    </div>
                    <div class='protein-calculator__activity-level-label'>
                        <label>Option Name</label>
                        <input id='acitivity_level_label' class='protein-calculator__activity-level-option-name' style='width: 275px;' width='275' type='text' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][label]' value='".esc_html($activity_level_label)."' />
                    </div>  
                </div>
                <div class='protein-calculator__inputs protein-calculator__inputs--number-small'>
                    <input style='width: 100px' class='".esc_attr($class)." protein-calculator__imperial-input' class='protein-calculator__weight' id='activity-level' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][m_".esc_html($activity_level)."_lbs]' type='number' step='.001' value='" . esc_attr($activity_level_value_lbs) . "' /> <span>g/lb</span>
                    <input style='width: 100px' class='".esc_attr($class)." protein-calculator__metric-input' class='protein-calculator__weight' id='activity-level' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][m_".esc_html($activity_level)."_kg]' type='number' step='.001' value='" . esc_attr($activity_level_value_kg) . "' /> <span>g/kg</span>
                    <!-- add the high value here -->
                    <input style='width: 100px' class='".esc_attr($class)." protein-calculator__imperial-input' class='protein-calculator__weight' id='activity-level' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][m_".esc_html($activity_level)."_high_lbs]' type='number' step='.001' value='" . esc_attr($activity_level_value_high_lbs) . "' /> <span>g/lb (high)</span>
                    <input style='width: 100px' class='".esc_attr($class)." protein-calculator__metric-input' class='protein-calculator__weight' id='activity-level' name='".esc_attr($this->option_name_protein)."[activity_level][" . esc_attr($activity_level) ."][m_".esc_html($activity_level)."_high_kg]' type='number' step='.001' value='" . esc_attr($activity_level_value_high_kg) . "' /> <span>g/kg (high)</span>
                    <div class='protien-calculator__goals'>
                        <div class='protein-calculator__goal'>
                            <label>Maintain</label>
                            <div class='protein-calculator__multipliers'>
                                <input class='protein-calculator__imperial-input' width='75' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_maintain_lbs]' value='".esc_html($maintain_value_lbs)."' /> <span>g/lb</span>
                                <input class='protein-calculator__metric-input' width='75' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_maintain_kg]' value='".esc_html($maintain_value_kg)."' /> <span>g/kg</span>
                                <!-- add the high value here -->
                                <input class='protein-calculator__imperial-input  width='75' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_maintain_high_lbs]' value='".esc_html($maintain_value_high_lbs)."' /> <span>g/lb (high)</span>
                                <input class='protein-calculator__metric-input' width='75' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_maintain_high_kg]' value='".esc_html($maintain_value_high_kg)."' /> <span>g/kg (high)</span>
                            </div>
                        </div>
                        <div class='protein-calculator__goal'>
                            <label>Tone Up</label>
                            <div class='protein-calculator__multipliers'>
                                <input class='protein-calculator__imperial-input' width='75' id='sedentary-toning' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_toning_lbs]' value='" . esc_attr($toning_value_lbs) . "' /><span>g/lb</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-toning' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_toning_kg]' value='" . esc_attr($toning_value_kg) . "' /><span>g/kg</span>
                                <!-- add the high value here -->
                                <input class='protein-calculator__imperial-input  width='75' id='sedentary-toning' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_toning_high_lbs]' value='" . esc_attr($toning_value_high_lbs) . "' /><span>g/lb (high)</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-toning' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_toning_high_kg]' value='" . esc_attr($toning_value_high_kg) . "' /><span>g/kg (high)</span>
                            </div>
                        </div>
                        <div class='protein-calculator__goal'>
                            <label>Build Muscle</label>
                            <div class='protein-calculator__multipliers'>
                                <input class='protein-calculator__imperial-input' width='75' id='sedentary-maintain' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_muscle_growth_lbs]' value='" . esc_attr($muscle_growth_value_lbs) . "' /><span>g/lb</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-maintain' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_muscle_growth_kg]' value='" . esc_attr($muscle_growth_value_kg) . "' /><span>g/kg</span>
                                <!-- add the high value here -->
                                <input class='protein-calculator__imperial-input  width='75' id='sedentary-maintain' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_muscle_growth_high_lbs]' value='" . esc_attr($muscle_growth_value_high_lbs) . "' /><span>g/lb (high)</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-maintain' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_muscle_growth_high_kg]' value='" . esc_attr($muscle_growth_value_high_kg) . "' /><span>g/kg (high)</span>
                            </div>
                        </div>       
                        <div class='protein-calculator__goal protein-calculator__goal--static'>
                            <label>Lose Weight</label>
                            <div class='protein-calculator__multipliers'>
                                <input class='protein-calculator__imperial-input' width='75' id='sedentary-muscle-growth' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_weight_loss_lbs]' value='".esc_attr($weight_loss_value_lbs ) . "' /> <span>g/lb</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-muscle-growth' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_weight_loss_kg]' value='".esc_attr($weight_loss_value_kg ) . "' /> <span>g/kg</span>
                                <!-- add the high value here -->
                                <input class='protein-calculator__imperial-input  width='75' id='sedentary-muscle-growth' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_weight_loss_high_lbs]' value='".esc_attr($weight_loss_value_high_lbs ) . "' /> <span>g/lb (high)</span>
                                <input class='protein-calculator__metric-input' width='75' id='sedentary-muscle-growth' type='number' step='.001' name='".esc_attr($this->option_name_protein)."[activity_level][".esc_html($activity_level)."][goal][m_weight_loss_high_kg]' value='".esc_attr($weight_loss_value_high_kg ) . "' /> <span>g/kg (high)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ";
        }
    }

    public function convert_multiplier($multiplier, $system) {
        if(!$multiplier) {
            return '';
        }

        if ('imperial' === $system) {
            // Convert grams per kg to grams per pound
            return round($multiplier / 0.453592, 2);
        } else {
            // Convert grams per pound to grams per kg
            return round($multiplier * 0.453592, 2);
        }
    } 

    public function account_password_callback($args) {
        $options = TwwcOptions::get_option($this->option_name);
        $value = isset($options['account_password']) ? $options['account_password'] : '';
        $class = !empty($value) ? 'has-value' : 'required-value-missing-notice';
        $max = isset($args['max']) ? 'max="' . intval($args['max']) .'"': '';

        echo "<input style='width: 275px' class='".esc_attr($class)."' id='account-password' name='".esc_attr($this->option_name)."[account_password]' type='text' value='" . esc_attr($value) . "' placeholder='Client Account Password' ".esc_attr($max)." />";
    }

    public function common_settings_output() {
        echo '
        <p>Use the fields below to adjust settings used by the <a target="_blank" href="https://www.thewellnessway.com">The Wellness Way</a> protein calculator. Use shortode [twwc_protein_calculator] on any page to render the calculator.</p>
        ';
    }

    public function results_content_callback() {
        $options = TwwcOptions::get_option($this->option_name_protein, []);
        $content = isset($options['content']) && is_array($options['content']) && array_key_exists('results', $options['content']) ? $options['content']['results'] : '';
        $editor_id = 'my_plugin_editor';
        $name = $this->option_name_protein . '[content][results]';
    
        wp_editor(
            $content, // Initial content
            $editor_id, // Editor ID
            array(
                'textarea_name' => $name , // Set the name of the textarea
                'media_buttons' => true, // Show media upload buttons
                'textarea_rows' => 10, // Set the number of rows
                'teeny' => false, // Use the full TinyMCE editor
                'quicktags' => true, // Show Quicktags toolbar
            )
        );
    }

    public static function get_settings_page() {
        return self::COMMON_SETTINGS_PAGE;
    }

    public static function get_tab_two() {
        return self::PROTEIN_CALCULATOR_SETTINGS_PAGE;
    }
}
