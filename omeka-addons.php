<?php
/*
Plugin Name: Omeka Addons
Plugin URI: http://omeka.org
Description: Manage addons on Omeka.org
Version: 1.0-alpha
Author: Center for History and New Media
Author URI: http://chnm.gmu.edu
*/

/*
Copyright (C) 2010 Center for History and New Media, George Mason University

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !class_exists( 'Omeka_Addons' ) ) :

class Omeka_Addons {

    function omeka_addons() {
        add_action( 'init', array ( $this, 'init' ) );
        add_action( 'plugins_loaded', array ( $this, 'loaded' ) );
        add_action( 'omeka_addons_init', array($this, 'register_post_types') );

        // activation sequence
        register_activation_hook( __FILE__, array($this, 'activation') );

        // deactivation sequence
        register_deactivation_hook( __FILE__, array($this, 'deactivation') );
    }

    function init() {
        do_action( 'omeka_addons_init' );
    }

    function loaded() {
        do_action( 'omeka_addons_loaded' );
    }

    function activation() {
        add_option('omeka_addons_flush_rewrite_rules', 'true');
    }

    function deactivation() {
        delete_option('omeka_addons_flush_rewrite_rules');
    }

    /**
     * Registers our 'omeka_plugin' and 'omeka_theme' custom post types.
     */
    function register_post_types() {

        $omekaPluginLabels = array(
            'name' => _x('Omeka Plugins', 'Omeka plugins general name', 'omeka-addons'),
            'singular_name' => _x('Omeka Plugin', 'single Omeka plugin entry', 'omeka-addons'),
            'add_new' => _x('Add New', 'Omeka Plugin'),
            'add_new_item' => __('Add New Omeka Plugin', 'omeka-addons'),
            'edit_item' => __('Edit Omeka Plugin', 'omeka-addons'),
            'new_item' => __('New Omeka Plugin', 'omeka-addons'),
            'view_item' => __('View Omeka Plugin', 'omeka-addons'),
            'search_items' => __('Search Omeka Plugins', 'omeka-addons'),
            'not_found' =>  __('No entries found'),
            'not_found_in_trash' => __('No entries found in Trash'),
            'parent_item_colon' => ''
        );

        $omekaPluginPostDef = array(
            'label'                 => __('plugin', 'omeka-addons'),
            'labels'                => $omekaPluginLabels,
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'capability_type'       => 'page',
            'show_in_nav_menus'     => true,
            'has_archive'           => 'add-ons/plugins',
            'hierarchical'          => true,
            'supports'              => array('title', 'editor'),
            'rewrite'               => array("slug" => 'add-ons/plugins')
        );

        register_post_type( 'omeka_plugin', $omekaPluginPostDef );

        $omekaThemeLabels = array(
            'name' => _x('Omeka Themes', 'Omeka themes general name', 'omeka-addons'),
            'singular_name' => _x('Omeka Theme', 'single Omeka Theme entry', 'omeka-addons'),
            'add_new' => _x('Add New', 'Omeka Plugin'),
            'add_new_item' => __('Add New Omeka Theme', 'omeka-addons'),
            'edit_item' => __('Edit Omeka Theme', 'omeka-addons'),
            'new_item' => __('New Omeka Theme', 'omeka-addons'),
            'view_item' => __('View Omeka Theme', 'omeka-addons'),
            'search_items' => __('Search Omeka Themes', 'omeka-addons'),
            'not_found' =>  __('No entries found'),
            'not_found_in_trash' => __('No entries found in Trash'),
            'parent_item_colon' => ''
        );

        $omekaThemePostDef = array(
            'label'                 => __('theme', 'omeka-addons'),
            'labels'                => $omekaThemeLabels,
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'capability_type'       => 'page',
            'show_in_nav_menus'     => true,
            'has_archive'           => 'add-ons/themes',
            'hierarchical'          => true,
            'supports'              => array('title', 'editor'),
            'rewrite'               => array("slug" => 'add-ons/themes')
        );

        register_post_type( 'omeka_theme', $omekaThemePostDef );

        if (get_option('omeka_addons_flush_rewrite_rules') == 'true') {
            flush_rewrite_rules();
            delete_option('omeka_addons_flush_rewrite_rules');
        }
    }
}

endif;

$omeka_addons = new Omeka_Addons();