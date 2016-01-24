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

/*======== Function for adding menu and submenu ========*/
if (!function_exists('add_se_sitemap_menu')) {
    function add_se_sitemap_menu() {
        /** Add a page to the options section of the website **/
        if (current_user_can('manage_options')) {
            add_options_page('SE-Sitemap-Gen', 'SE-Sitemap-Gen', 8, __FILE__, 'se_sitemap_gen');
        }
    }
}

/*======== SE-Sitemap-Gen page ========*/
if (!function_exists('se_sitemap_gen')) {
    function se_sitemap_gen() {
        ?>
        <div class="wrap">
            <h1 style="line-height: normal;"><?php _e("SearchEngine Sitemap Generator", 'se-sitemap-gen-plugin'); ?>
                <span class="title-author">By ML3426</span></h1>
        </div>
        <?php
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
add_action( 'plugins_loaded', 'se_sitemap_lang_loader' );

/** Tie the stylesheets into Wordpress **/
add_action('admin_enqueue_scripts', 'se_sitemap_add_stylesheet');

/** Tie the module into Wordpress **/
add_action('admin_menu', 'add_se_sitemap_menu');

