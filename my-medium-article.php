<?php
/**
 * @link              https://github.com/thiagomatsui/my_medium_article
 * @since             1.0.0
 * @package           My_Medium_Article
 *
 * @wordpress-plugin
 * Plugin Name:       My Medium Article
 * Plugin URI:        https://github.com/thiagomatsui/my_medium_article
 * Description:       Display the article list from a Medium using Medium feed and keep always updated even for cached posts.
 * Version:           1.0.0
 * Author:            THIAGO MATSUI
 * Author URI:        https://github.com/thiagomatsui
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       my-medium-article
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if(!defined('WPINC')){
    wp_die();
}

// Plugin Version
if(!defined('MY_MEDIUM_ARTICLE_VERSION')){
    define( 'MY_MEDIUM_ARTICLE_VERSION', '1.0.0');
}

// Plugin Name
if(!defined('MY_MEDIUM_ARTICLE_NAME')){
    define('MY_MEDIUM_ARTICLE_NAME', 'My Medium Article');
}

// Plugin Slug
if(!defined('MY_MEDIUM_ARTICLE_PLUGIN_SLUG')){
    define('MY_MEDIUM_ARTICLE_PLUGIN_SLUG', 'my-medium-article');
}

// Plugin Basename
if(!defined('MY_MEDIUM_ARTICLE_BASENAME')){
    define('MY_MEDIUM_ARTICLE_BASENAME', plugin_basename(__FILE__));
}

// Plugin Folder
if(!defined('MY_MEDIUM_ARTICLE_PLUGIN_DIR')){
    define('MY_MEDIUM_ARTICLE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// JSON File Name
if (!defined('MY_MEDIUM_ARTICLE_JSON_FILENAME')){
    define('MY_MEDIUM_ARTICLE_JSON_FILENAME', 'my-medium-article.json');
}

// Load the plugin's translated strings.
load_plugin_textdomain(MY_MEDIUM_ARTICLE_PLUGIN_SLUG, false, MY_MEDIUM_ARTICLE_PLUGIN_SLUG.'/languages/');

// Dependencies
require_once MY_MEDIUM_ARTICLE_PLUGIN_DIR . 'includes/class-my-medium-article.php';
require_once MY_MEDIUM_ARTICLE_PLUGIN_DIR . 'includes/class-my-medium-article-json.php';
require_once MY_MEDIUM_ARTICLE_PLUGIN_DIR . 'includes/class-my-medium-article-widget.php';
require_once MY_MEDIUM_ARTICLE_PLUGIN_DIR . 'includes/class-my-medium-article-shortcode.php';

if(is_admin())
    require_once MY_MEDIUM_ARTICLE_PLUGIN_DIR . 'includes/class-my-medium-article-admin.php';

// Plugin Instance
$my_medium_article_plugin = new My_Medium_Article();

$medium_id = $my_medium_article_plugin->options['medium_id'];

if($medium_id != ""){
    $expiration = $my_medium_article_plugin->options['cache_expiration'];
    $my_medium_article_json = new My_Medium_Article_Json(
        $medium_id,
        $expiration,
        MY_MEDIUM_ARTICLE_PLUGIN_SLUG,
        MY_MEDIUM_ARTICLE_JSON_FILENAME
    );
}

// Widget Instance
$my_medium_article_widget = new My_Medium_Article_Widget();

// Shortcode Instance
$my_medium_article_shortcode = new My_Medium_Article_Shortcode();

// Admin Instance
if(is_admin()){
    $my_medium_article_admin_page = new My_Medium_Article_Admin(
        MY_MEDIUM_ARTICLE_BASENAME,
        MY_MEDIUM_ARTICLE_PLUGIN_SLUG,
        MY_MEDIUM_ARTICLE_JSON_FILENAME
    );
}
