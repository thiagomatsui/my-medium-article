<?php

if(!class_exists('My_Medium_Article_Json')){

    class My_Medium_Article_Json {

            private $medium_id;
            private $expiration; // in hours
            private $filename;
            private $dirname;
            private $path;

            public function __construct($medium_id, $expiration = 1, $dirname, $filename) {

                $this->medium_id  = $medium_id;
                $this->expiration = $expiration;
                $this->dirname    = $dirname;
                $this->filename   = $filename;
                $this->path       = $this->create_folder_path();

                //Registro da action do Ajax no Wordpress
                $ajax_action = 'my_medium_article_posts';
                add_action("wp_ajax_$ajax_action", array($this, 'write_content'));
                add_action("wp_ajax_nopriv_$ajax_action", array($this, 'write_content'));

            }

            private function get_filename_full_path() {

                return $this->path . '/' . $this->filename;

            }

            private function create_folder_path() {

                $upload_dir = wp_upload_dir();
                if(!empty($upload_dir['basedir'])){
                    $dirname = $upload_dir['basedir'] . '/' . $this->dirname;
                    if(!file_exists($dirname)){
                        wp_mkdir_p($dirname);
                    }
                    return $dirname;
                }

            }

            public function from_medium_feed() {

                $medium_id  = $this->medium_id;

                $response    = wp_remote_get("https://api.rss2json.com/v1/api.json?rss_url=https://medium.com/feed/{$medium_id}");
                $json        = wp_remote_retrieve_body($response);
                $medium_info = json_decode($json);

                $medium_posts = array();

                // Medium info
                $medium_posts['feed']['title']       = (string)$medium_info->feed->title;
                $medium_posts['feed']['description'] = (string)$medium_info->feed->description;
                $medium_posts['feed']['url']         = (string)$medium_info->feed->url;
                $medium_posts['feed']['image']       = (string)$medium_info->feed->image;

                $i = 0;
                foreach($medium_info->items as $item) {

                    $day = new DateTime($item->pubDate);

                    // Medium Posts list
                    $medium_posts['posts'][$i]['link']    = (string)$item->guid;
                    $medium_posts['posts'][$i]['title']   = (string)$item->title;
                    $medium_posts['posts'][$i]['date']    = $day->format('Y-m-d');
                    $medium_posts['posts'][$i]['image']   = (string)$item->thumbnail;
                    $medium_posts['posts'][$i]['content'] = (string)$item->description;

                    $i++;
                }

                return json_encode($medium_posts);

            }

            private function from_file() {

                $json_path = wp_upload_dir()['baseurl'] . '/' . MY_MEDIUM_ARTICLE_PLUGIN_SLUG . '/' . MY_MEDIUM_ARTICLE_JSON_FILENAME;
                $response  = wp_remote_get($json_path);
                $json      = wp_remote_retrieve_body($response);

                return $json;

            }

            private function save_file($json_content){

                $json_path = $this->get_filename_full_path();
                $fp = fopen($json_path, 'w');
                fwrite($fp, $json_content);
                fclose($fp);

            }

            private function is_expired() {

                $file_expiration_in_hours = $this->expiration;

                $json_file          = $this->get_filename_full_path();
                $json_file_expired  = (time()-filemtime($json_file) > ($file_expiration_in_hours * 3600));

                return  ( $json_file_expired );

            }

            public function get_content() {

                if($this->is_expired()){
                    $json_content = $this->from_medium_feed();
                    $this->save_file($json_content);
                } else {
                    $json_content = $this->from_file();
                }

                return $json_content;
            }

            public function write_content() {

                echo $this->get_content();
                wp_die();

            }

    }

} // !class_exists