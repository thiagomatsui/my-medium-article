<?php

if(!class_exists('My_Medium_Article_Admin')){

    class My_Medium_Article_Admin {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;
        private $plugin_basename;
        private $plugin_slug;
        private $json_filename;

        /**
         * Start up
         */
        public function __construct($basename, $slug, $json_filename) {

            $this->options = get_option('my_medium_article');

            $this->plugin_basename = $basename;
            $this->plugin_slug = $slug;
            $this->json_filename = $json_filename;

            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));
            add_action('admin_footer_text', array($this, 'page_footer'));
            add_action('admin_notices', array($this, 'show_notices'));

            add_filter("plugin_action_links_" . $this->plugin_basename, array($this, 'add_settings_link'));

        }

        /**
         * Add options page
         */
        public function add_plugin_page() {
            // This page will be under "Settings"
            add_options_page(
                __('Settings' ,'my-medium-article'),
                __('My Medium Article' ,'my-medium-article'),
                'manage_options',
                $this->plugin_slug,
                array($this, 'create_admin_page')
            );
        }

        /**
         * Add settings link on plugins page
         */
        public function add_settings_link($links){
            $settings_link = '<a href="options-general.php?page=' . $this->plugin_slug .'">' . __('Settings') . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Show notices on admin dashboard
         */
        public function show_notices() {
            $value = isset($this->options['medium_id']) ? esc_attr($this->options['medium_id']) : '';
            if($value == ''){
                ?>
                <div class="error notice">
                <?php echo $medium_id ?>
                    <p><strong><?php echo __( 'My Medium Article', 'my-medium-article' ); ?></strong></p>
                    <p><?php echo __( 'Fill your Medium ID (User ID)', 'my-medium-article' ); ?></p>
                </div>
                <?php
            }
        }

        /**
         * Options page callback
         */
        public function create_admin_page() {
            // Set class property
            ?>
            <div class="wrap">
                <h1><?php echo __('My Medium Article', 'my-medium-article'); ?></h1>
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields('my_medium_article_options');
                    do_settings_sections('my-medium-article-admin');
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }


        /**
         * Register and add settings
         */
        public function page_init() {

            register_setting(
                'my_medium_article_options', // Option group
                'my_medium_article', // Option name
                array($this, 'sanitize') // Sanitize
            );

            add_settings_section(
                'setting_section_id_1', // ID
                __('General Settings', 'my-medium-article'), // Title
                null, // Callback
                'my-medium-article-admin' // Page
            );

            add_settings_field(
                'medium_id', // ID
                __('Medium Id', 'my-medium-article'), // Title
                array($this, 'medium_id_callback'), // Callback
                'my-medium-article-admin', // Page
                'setting_section_id_1' // Section
            );

            add_settings_field(
                'cache_expiration',
                __('Cache Expiration', 'my-medium-article'),
                array($this, 'cache_expiration_callback'),
                'my-medium-article-admin',
                'setting_section_id_1'
            );

            add_settings_section(
                'setting_section_id_2',
                __('Post Settings', 'my-medium-article'),
                null,
                'my-medium-article-admin'
            );

            add_settings_field(
                'limit',
                __('Article Display', 'my-medium-article'),
                array($this, 'limit_callback'),
                'my-medium-article-admin',
                'setting_section_id_2'
            );

            add_settings_field(
                'display_all',
                __('Article Display in All Page', 'my-medium-article'),
                array($this, 'display_all_callback'),
                'my-medium-article-admin',
                'setting_section_id_2'
            );

            add_settings_section(
                'setting_section_id_3',
                __('Customize Style', 'my-medium-article'),
                null,
                'my-medium-article-admin'
            );

            add_settings_field(
                'custom_css',
                __('Your CSS', 'my-medium-article'),
                array($this, 'custom_css_callback'),
                'my-medium-article-admin',
                'setting_section_id_3'
            );
        }

        public function page_footer(){
            return __("Plugin Version") . " " . MY_MEDIUM_ARTICLE_VERSION;
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize($input){

            $new_input = array();

            if(isset($input['medium_id']))
                $new_input['medium_id'] = sanitize_text_field($input['medium_id']);

            if(isset($input['cache_expiration']))
                $new_input['cache_expiration'] = absint($input['cache_expiration']);

            if(isset($input['limit']))
                $new_input['limit'] = absint($input['limit']);

            if(isset($input['display_all']))
                $new_input['display_all'] = absint($input['display_all']);

            if(isset($input['custom_css']))
                $new_input['custom_css'] = sanitize_text_field($input['custom_css']);

            return $new_input;
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function medium_id_callback() {
            $value = isset($this->options['medium_id']) ? esc_attr($this->options['medium_id']) : '';
            ?>
            <input type="text" id="medium_id" name="my_medium_article[medium_id]" value="<?php echo $value ?>" class="regular-text" />
                <p class="description"><?php echo __('sample', 'my-medium-article') ?>: https://medium.com/<span style="color: #f00;">@○○○○○○○</span></p>
            <?php
        }

        public function cache_expiration_callback() {
            $upload_dir = wp_upload_dir();
            $json_url = $upload_dir['baseurl'] . '/' . $this->plugin_slug . '/' . $this->json_filename;

            $value = isset($this->options['cache_expiration']) ? esc_attr($this->options['cache_expiration']) : '1';
            ?>
                <input type="number" id="cache_expiration" min="1" name="my_medium_article[cache_expiration]" value="<?php echo $value ?>" class="small-text" />
                <?php echo __('hours is the expiration time for cached data', 'my-medium-article') ?>.
                <p class="description"><a href="<?php echo $json_url?>" target="_blank"><?php echo __('Test here', 'my-medium-article') ?></a>.
            <?php
        }

        public function limit_callback() {
            $value = isset($this->options['limit']) ? esc_attr($this->options['limit']) : '3';
            ?>
            <input type="number" id="limit" min="0" max="15" name="my_medium_article[limit]" value="<?php echo $value ?>" class="small-text" />
            <p class="description"><?php echo __('Max', 'my-medium-article') ?> 15</p>
            <?php
        }

        public function display_all_callback() {
            $value = isset($this->options['display_all']) ? esc_attr($this->options['display_all']) : 'true';
            ?>
            <fieldset>
                <label><input type="radio" name="my_medium_article[display_all]" value="1" <?php echo ( $value == '1' ) ? 'checked="checked"' : '' ?>> <?php echo __('true', 'my-medium-article') ?></label><br>
                <label><input type="radio" name="my_medium_article[display_all]" value="0" <?php echo ( $value == '0' ) ? 'checked="checked"' : '' ?>> <?php echo __('false', 'my-medium-article') ?></label><br>
            </fieldset>
            <?php
        }

        public function custom_css_callback() {
            $value = isset($this->options['custom_css']) ? esc_attr($this->options['custom_css']) : '';
            ?>
            <textarea id="custom_css" name="my_medium_article[custom_css]" rows="10" cols="50" class="large-text code"><?php echo $value ?></textarea>
            <?php
        }
    }
}