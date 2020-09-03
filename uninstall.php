<?php

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')){
    exit;
}

if(!function_exists('my_medium_article_uninstall')){

    function my_medium_article_uninstall(){
        delete_option('my_medium_article');

        $upload_dir     = wp_upload_dir();
        $json_folder    = $upload_dir['basedir'] . '/my-medium-article';
        $json_file      = $json_folder . '/my-medium-article.json';
        unlink($json_file);
        rmdir($json_folder);
    }

}

register_uninstall_hook(__FILE__, 'my_medium_article_uninstall');