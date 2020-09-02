<?php

if(!class_exists('My_Medium_Article_Shortcode')){

    class My_Medium_Article_Shortcode {

        public function __construct() {
            add_shortcode('my_medium', array($this, 'shortcode'));
        }

        public function shortcode($args, $content = null) {
            extract($args);

            $shortcode_unique_id = 'my_medium_article_shortcode_' . wp_rand(1, 1000);

            // Check the widget options
            $limit      = isset($limit) ? $limit : 1;
            $language 	= get_locale();

            $content    = "
                        <div id='$shortcode_unique_id'>" . __('Loading...', 'my-medium-article') . "</div>
                        <script>
                        MyMediumArticles.listCallbacks.push({
                            container: '$shortcode_unique_id',
                            limit: $limit,
                            lang: '$language',
                            callback: MyMediumArticles.buildList
                        });
                        </script>
                        ";

            return $content;
        }

    }

} // !class_exists