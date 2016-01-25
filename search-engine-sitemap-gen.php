<?php

/*
Plugin Name: Search Engine Sitemap Gen By ML3426
Plugin URI: http://product.ml3426.me/sitemap-gen/
Description: This pulgin generates a XML-Sitemap for WordPress Blog. Also Build a real Static Sitemap-Page for all Search Engine. | 生成 Sitemap XML 文件。提交给搜索引擎，进而为您的网站带来潜在的流量。同时生成一个静态的站点地图页面，对所有的搜索引擎都有利。
Version: 1.0
Author: ml3426
Domain Path: /lang
Author URI: http://ml3426.me
License: GPL v2
*/

/*  Copyright 2016  ML3426  (email : fml3426@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define('NOTICE_NOTICE', 'updated');
define('NOTICE_WARNING', 'update-nag');
define('NOTICE_ERROR', 'error');

/*======== Function for adding menu and submenu ========*/
if (!function_exists('add_se_sitemap_menu')) {
    function add_se_sitemap_menu() {
        /** Add a page to the options section of the website **/
        if (current_user_can('manage_options')) {
            add_options_page('SE-Sitemap-Gen', 'SE-Sitemap-Gen', 'manage_options', __FILE__, 'se_sitemap_gen');
        }
    }
}

/*======== SE-Sitemap-Gen page ========*/
if (!function_exists('se_sitemap_gen')) {
    function se_sitemap_gen() {
        global $plugin_basename, $err_message, $notice_message;
        if (isset($_POST['se_sitemap_submit']) && 'submit' == $_POST['se_sitemap_submit']) {
            if (!isset($_POST['se_sitemap_nonce']) || wp_verify_nonce($_POST['se_sitemap_nonce'], $plugin_basename)) {
                $err_message = __('Hidden Field Verify Faild', 'se-sitemap-gen-plugin');
            } else {
                if (isset($_POST['se_sitemap_create']) && '1' == $_POST['se_sitemap_create']) {
                    se_sitemap_create();
                }
            }
        }
        se_sitemap_gen_basic_page();
    }
}

/*======== Init SE-Sitemap-Gen Setting ========*/
if (!function_exists('se_sitemap_init')) {
    function se_sitemap_init() {
        global $se_sitemap_plugin_info, $plugin_basename;
        $plugin_basename = plugin_basename(__FILE__);

        if (empty($se_sitemap_plugin_info)) {
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $se_sitemap_plugin_info = get_plugin_data(__FILE__);
        }

        if (isset($_GET['page']) && "search-engine-sitemap-gen/search-engine-sitemap-gen.php" == $_GET['page']) {
            se_sitemap_register_setting();
        }
    }
}

/*======== Register SE-Sitemap-Gen Setting ========*/
if (!function_exists('se_sitemap_register_setting')) {
    function se_sitemap_register_setting() {
        global $se_sitemap_settings, $se_sitemap_plugin_info, $se_sitemap_default_settings;

        if (!get_option('se_sitemap_settings')) {
            $se_sitemap_default_settings = array(
                'sitemap_file_prefix' => 'sitemap',
                'first_install' => strtotime('now'),
                'display_welcome_message' => true
            );
            add_option('se_sitemap_settings', $se_sitemap_default_settings);
        }


    }
}

/*======== SE-Sitemap-Gen Notice ========*/
if (!function_exists('se_sitemap_notice')) {
    function se_sitemap_notice($type = NOTICE_NOTICE, $msgs = '') { ?>
        <div id="se_sitemap_notice" class=<?php echo $type ?>><p><?php echo $msgs ?>
                <span href="#" onclick="document.getElementById('se_sitemap_notice').style.display = 'none'"
                      id="se_sitemap_notice_ignore" style="text-decoration: none; cursor: pointer">&nbsp;</span></p>
        </div>
    <?php }
}

/*======== SE-Sitemap-Gen Basic Page ========*/
if (!function_exists('se_sitemap_gen_basic_page')) {
    function se_sitemap_gen_basic_page() {
        global $plugin_basename, $err_message, $notice_message;

        if (is_multisite()) {
            $home_url = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace('http://', '', str_replace('https://', '', home_url())));
            $se_sitemap_url = ABSPATH . "sitemap_" . $home_url . ".xml";
        } else {
            $se_sitemap_url = ABSPATH . "sitemap.xml";
        }
        ?>
        <div class="wrap">
            <h1 style="line-height: normal;"><?php _e("SearchEngine Sitemap Generator", 'se-sitemap-gen-plugin'); ?>
                <span class="title-author">By ML3426</span></h1>
            <?php
            if (isset($err_message) && $err_message != '') se_sitemap_notice(NOTICE_ERROR, $err_message);
            $err_message = '' ?>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab<?php if (!isset($_GET['action'])) echo ' nav-tab-active'; ?>"
                   href="options-general.php?page=search-engine-sitemap-gen%2Fsearch-engine-sitemap-gen.php"><?php _e('Settings', 'se-sitemap-gen-plugin'); ?></a>
                <a class="nav-tab<?php if (isset($_GET['action']) && 'extra' == $_GET['action']) echo ' nav-tab-active'; ?>"
                   href="options-general.php?page=search-engine-sitemap-gen%2Fsearch-engine-sitemap-gen.php&amp;action=extra"><?php _e('Extra settings', 'se-sitemap-gen-plugin'); ?></a>
            </h2>
            <form action="options-general.php?page=search-engine-sitemap-gen%2Fsearch-engine-sitemap-gen.php"
                  method="post">
                <?php wp_nonce_field($plugin_basename, 'se_sitemap_nonce') ?>
                <table class="form-table">
                    <tr valign="top">
                        <td colspan="2">
                            <label><input type='checkbox' name='se_sitemap_create' value="1"/>
                                <?php _e("Create or Replace the sitemap file while save the settings", 'se-sitemap-gen-plugin'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="se_sitemap_submit" value="submit"/>
                <p class="submit">
                    <input id="se-submit-button" type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'se-sitemap-gen-plugin'); ?>"/>
                    <input id="se-submit-button" type="button" class="button"
                           value="<?php _e('Reset Changes', 'se-sitemap-gen-plugin'); ?>"/>
                </p>
            </form>
        </div>
        <?php
    }
}

/*======== SE-Sitemap-Gen Sitemap Creator ========*/
if (!function_exists('se_sitemap_create')) {
    function se_sitemap_create() {
        $sitemap = new DOMDocument('1.0', 'utf-8');

        $sitemap_stylesheet_path = (defined('WP_CONTENT_DIR')) ? home_url('/') . basename(WP_CONTENT_DIR) : home_url('/') . 'wp-content';
        $sitemap_stylesheet_path .= (defined('WP_PLUGIN_DIR')) ? '/' . basename(WP_PLUGIN_DIR) . '/search-engine-sitemap-gen/sitemap.xsl' : '/plugins/search-engine-sitemap-gen/sitemap.xsl';

        $xslt = $sitemap->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"$sitemap_stylesheet_path\"");
        $sitemap->appendChild($xslt);
        $urlset = $sitemap->appendChild($sitemap->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset'));

        // Add Home Page
        $homepage_url = $urlset->appendChild($sitemap->createElement('url'));
        $homepage_loc = $homepage_url->appendChild($sitemap->createElement('loc'));
        $homepage_loc->appendChild($sitemap->createTextNode(home_url()));
        $homepage_lastmod = $homepage_url->appendChild($sitemap->createElement('lastmod'));
        $homepage_lastmod->appendChild($sitemap->createTextNode(date('Y-m-d\TH:i:sP')));
        $homepage_changefreq = $homepage_url->appendChild($sitemap->createElement('changefreq'));
        $homepage_changefreq->appendChild($sitemap->createTextNode('daily'));
        $homepage_priority = $homepage_url->appendChild($sitemap->createElement('priority'));
        $homepage_priority->appendChild($sitemap->createTextNode('1'));

        // Add Post
        $posts_args = array('numberposts' => 45000, 'post_status' => 'publish');
        $posts_array = get_posts($posts_args);
        foreach ($posts_array as $post) {
            $node_url = $urlset->appendChild($sitemap->createElement('url'));
            // Set Loc
            $node_loc = $node_url->appendChild($sitemap->createElement('loc'));
            $post_permalink = get_permalink($post);
            $node_loc->appendChild($sitemap->createTextNode($post_permalink));
            // Set Lastmod
            $node_lastmod = $node_url->appendChild($sitemap->createElement('lastmod'));
            $post_lastmod = date('Y-m-d\TH:i:sP', strtotime($post->post_modified));
            $node_lastmod->appendChild($sitemap->createTextNode($post_lastmod));
            // Set Changefreq
            $node_changefreq = $node_url->appendChild($sitemap->createElement('changefreq'));
            $node_changefreq->appendChild($sitemap->createTextNode('monthly'));
            // Set Priority
            $node_priority = $node_url->appendChild($sitemap->createElement('priority'));
            $node_priority->appendChild($sitemap->createTextNode('1'));
        }

        //Add Pages
        $pages_args = array(
            'sort_order' => 'desc',
            'sort_column' => 'post_date',
            'post_status' => 'publish'
        );
        $pages_array = get_pages($pages_args);
        foreach ($pages_array as $page) {
            $node_url = $urlset->appendChild($sitemap->createElement('url'));
            // Set Loc
            $node_loc = $node_url->appendChild($sitemap->createElement('loc'));
            $page_permalink = get_page_link($page->ID);
            $node_loc->appendChild($sitemap->createTextNode($page_permalink));
            // Set Lastmod
            $node_lastmod = $node_url->appendChild($sitemap->createElement('lastmod'));
            $page_lastmod = date('Y-m-d\TH:i:sP', strtotime($page->post_modified));
            $node_lastmod->appendChild($sitemap->createTextNode($page_lastmod));
            // Set Changefreq
            $node_changefreq = $node_url->appendChild($sitemap->createElement('changefreq'));
            $node_changefreq->appendChild($sitemap->createTextNode('weekly'));
            // Set Priority
            $node_priority = $node_url->appendChild($sitemap->createElement('priority'));
            $node_priority->appendChild($sitemap->createTextNode('1'));
        }

        // Add Cates
        $cates_args = array(
            'orderby' => 'name',
            'order' => 'ASC'
        );
        $cates_array = get_categories($cates_args);
        foreach ($cates_array as $cate) {
            $node_url = $urlset->appendChild($sitemap->createElement('url'));
            // Set Loc
            $node_loc = $node_url->appendChild($sitemap->createElement('loc'));
            $cate_permalink = get_category_link($cate->term_id);
            $node_loc->appendChild($sitemap->createTextNode($cate_permalink));
            // Set Lastmod
            $node_lastmod = $node_url->appendChild($sitemap->createElement('lastmod'));
            $cate_lastmod = date('Y-m-d\TH:i:sP');
            $node_lastmod->appendChild($sitemap->createTextNode($cate_lastmod));
            // Set Changefreq
            $node_changefreq = $node_url->appendChild($sitemap->createElement('changefreq'));
            $node_changefreq->appendChild($sitemap->createTextNode('weekly'));
            // Set Priority
            $node_priority = $node_url->appendChild($sitemap->createElement('priority'));
            $node_priority->appendChild($sitemap->createTextNode('1'));
        }

        // Add Tags
        $tags_array = get_tags();
        foreach ($tags_array as $tag) {
            $node_url = $urlset->appendChild($sitemap->createElement('url'));
            // Set Loc
            $node_loc = $node_url->appendChild($sitemap->createElement('loc'));
            $tag_permalink = get_tag_link($tag->term_id);
            $node_loc->appendChild($sitemap->createTextNode($tag_permalink));
            // Set Lastmod
            $node_lastmod = $node_url->appendChild($sitemap->createElement('lastmod'));
            $tag_lastmod = date('Y-m-d\TH:i:sP');
            $node_lastmod->appendChild($sitemap->createTextNode($tag_lastmod));
            // Set Changefreq
            $node_changefreq = $node_url->appendChild($sitemap->createElement('changefreq'));
            $node_changefreq->appendChild($sitemap->createTextNode('weekly'));
            // Set Priority
            $node_priority = $node_url->appendChild($sitemap->createElement('priority'));
            $node_priority->appendChild($sitemap->createTextNode('1'));
        }

        $sitemap->formatOutput = true;
        if (is_multisite()) {
            $home_url = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace('http://', '', str_replace('https://', '', home_url())));
            $sitemap->save(ABSPATH . 'sitemap_' . $home_url . '.xml');
        } else {
            $sitemap->save(ABSPATH . 'sitemap.xml');
        }
    }
}


/*======== SE-Sitemap-Gen StyleSheets Loader ========*/
if (!function_exists('se_sitemap_add_stylesheet')) {
    function se_sitemap_add_stylesheet() {
        if (isset($_GET['page']) && "search-engine-sitemap-gen/search-engine-sitemap-gen.php" == $_GET['page']) {
            wp_enqueue_style('se_sitemap_stylesheet', plugins_url('css/style.css', __FILE__));
        }
    }
}

/*======== SE-Sitemap-Gen Languages Files Loader ========*/
if (!function_exists('se_sitemap_lang_loader')) {
    function se_sitemap_lang_loader() {
        /* Internationalization */
        load_plugin_textdomain('se-sitemap-gen-plugin', false, dirname(plugin_basename(__FILE__)) . '/lang/');
    }
}

/** Tie the language files into Wordpress **/
add_action('plugins_loaded', 'se_sitemap_lang_loader');

/** Tie the stylesheets into Wordpress **/
add_action('admin_enqueue_scripts', 'se_sitemap_add_stylesheet');

/** Tie the module into Wordpress **/
add_action('admin_menu', 'add_se_sitemap_menu');

