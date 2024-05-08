<div class="wrap" id="tww-admin">

<div class="twwc--page-title-wrapper">
    <div class="twwc--page-header">
    <?php
        use TwwcProtein\Admin\TwwcAdminMenu;

        $logo = TWWC_PROTEIN_PLUGIN_URL . 'resources/images/twwlogo70.webp';
        echo '<h1>TWWC- Settings</h1>';
        settings_errors();
    ?>
    </div>
</div>

<div class="bluefield_content_wrapper">
    <?php
        $page_indentifier = 'twwc-calculator';
        $settings_slug = TwwcAdminMenu::get_settings_page();
        $active_tab = isset( $_GET[ 'page' ] ) ? sanitize_text_field(wp_unslash($_GET[ 'page' ])) : $settings_slug;
    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=" class="nav-tab <?php echo ($active_tab == $settings_slug || $active_tab == $page_indentifier) ? 'nav-tab-active' : ''; ?>">Settings</a>
        <!-- <a href="?page=" class="nav-tab <?php echo $active_tab == 'bluefield_identity-client-vars' ? 'nav-tab-active' : ''; ?>">Additional Variables (Advanced)</a> -->
    </h2>

    <form method="post" action="options.php">
        <?php
            if( $active_tab === $settings_slug || $active_tab === $page_indentifier ) {
                settings_fields('twwc-common-settings-options');
                do_settings_sections($settings_slug);
                submit_button();
            }
        ?>
    </form>
</div>
</div>
