<?php

class My_Medium_Article {

        public $options;

        public function __construct() {
            $this->options = get_option('my_medium_article');

            // Mandatory Info for Plugin Work
            if($this->options['medium_id'] != ""){

                // Filters
                add_filter('the_content', array($this, 'add_articles_list_in_single_content'));

                // Actions
                add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

            }
        }

        public function add_articles_list_in_single_content($content) {

            if(is_single()){
                if($this->options['display_all']){
                    $content .=  $this->build_html_articles_list();
                }
                return $content;
            }

        }

        private function build_html_articles_list() {

            $limit = $this->options['limit'];
            $custom_css = $this->options['custom_css'];

            $custom_css = strip_tags($custom_css);
            $custom_css = htmlspecialchars($custom_css, ENT_HTML5 | ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');

            $language = get_locale();

            $container_id = 'my-medium-articles-container';
            if($custom_css != "")
                $content .= "<style>$custom_css</style>";
                $content .= "<div id='$container_id'>".__('Loading...' , 'my-medium-articles')."</div>";
                $script   = "<script>
                                MyMediumArticles.listCallbacks.push({
                                container: '$container_id',
                                limit: $limit,
                                lang: '$language',
                                callback: MyMediumArticles.buildList
                                });
                            </script>";
            return $content . $script;

        }

        public function enqueue_assets() {

            wp_enqueue_style('my-medium-article-style', plugin_dir_url( __DIR__ ) . 'public/css/style.css');
            wp_enqueue_script('my-medium-article-scripts', plugin_dir_url( __DIR__ ) . 'public/js/scripts.js', array( 'jquery' ), '', false);
            wp_enqueue_script('my-medium-article-loader', plugin_dir_url( __DIR__ ) . 'public/js/loader.js', array( 'jquery' ), '', true);
            wp_localize_script('my-medium-article-scripts', 'my_medium_article_ajax', array('url' => network_admin_url('admin-ajax.php')));

        }

}