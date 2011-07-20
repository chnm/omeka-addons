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
Copyright (C) 2011 Center for History and New Media, George Mason University

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
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'plugins_loaded', array ( $this, 'loaded' ) );
        add_action( 'omeka_addons_init', array($this, 'register_post_types') );
        add_action( 'omeka_addons_init', array($this, 'add_role') );
        add_action( 'omeka_addons_init', array($this, 'register_taxonomies') );
        add_action( 'omeka_addons_admin_init', array( $this, 'add_meta_boxes') );
        add_action( 'save_post', array( $this, 'save_post') );
        add_action( 'admin_head', array( $this, 'add_css') );
        add_action( 'wp_print_styles', array($this, 'enqueue_styles') );

        // activation sequence
        register_activation_hook( __FILE__, array($this, 'activation') );

        // deactivation sequence
        register_deactivation_hook( __FILE__, array($this, 'deactivation') );

        add_filter( 'the_content', array($this, 'addon_post_content') );
    }

    function init() {
        do_action( 'omeka_addons_init' );

    }

    /**
     * Adds a plugin admin initialization action.
     */
    function admin_init() {
        do_action( 'omeka_addons_admin_init');
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
 
    function enqueue_styles() {
        $styleUrl = '/plugins/omeka-addons/omeka-addons.css';
        wp_enqueue_style( 'omeka-addons-style', $styleUrl);
    }
    
    function add_css() { ?>
   <link rel="stylesheet" href="<?php echo WP_PLUGIN_URL . '/omeka-addons/omeka-addons.css'; ?>">
   <?php
    }
    
    function add_role() {
        //debug. uncomment remove_role below to work with fixing role settings
        //remove_role('omeka_addon_contributor');
        
        $result = add_role('omeka_addon_contributor', 'Addon Contributor', array(
            'edit_posts' => false,
            'delete_posts' => false,
            'edit_omeka_plugins' => true,
            'edit_omeka_plugin' => true,
            'publish_omeka_plugins' => false,
            'edit_omeka_themes' => true,
            'edit_omeka_theme' => true,
            'publish_omeka_themes' => false,
            'read' => true
        ));
        
        $role = get_role('administrator');
        $admin_caps = array(
        	'read_omeka_themes',
        	'read_omeka_plugins',
        	'edit_omeka_themes',
        	'edit_omeka_plugins',
        	'delete_omeka_themes',
        	'delete_omeka_plugins',
        	'edit_omeka_theme',
        	'edit_omeka_plugin',
            'edit_others_omeka_themes',
            'edit_others_omeka_plugins',
            'publish_omeka_themes',
        	'publish_omeka_plugins',
        );
        foreach($admin_caps as $cap) {
            $role->add_cap($cap);
        }
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
            'show_ui'               => true,
            'capability_type'       => 'omeka_plugin',
            $caps = array(
        		'publish_posts' => 'publish_omeka_plugins',
    			'edit_posts' => 'edit_omeka_plugins',
    			'edit_others_posts' => 'edit_others_omeka_plugins',
    			'read_private_posts' => 'read_private_omeka_plugins',
    			'edit_post' => 'edit_omeka_plugin',
    			'delete_post' => 'delete_omeka_plugin',
    		 	'read_post' => 'read_omeka_plugin',
            ),
			'capabilities' => $caps,
 
            'show_in_nav_menus'     => true,
            'has_archive'           => 'add-ons/plugins',
            'supports'              => array('title', 'editor', 'author'),
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
            'capability_type'       => 'omeka_theme',
            $caps = array(
        		'publish_posts' => 'publish_omeka_themes',
    			'edit_posts' => 'edit_omeka_themes',
    			'edit_others_posts' => 'edit_others_omeka_themes',
    			'read_private_posts' => 'read_private_omeka_themes',
    			'edit_post' => 'edit_omeka_theme',
    			'delete_post' => 'delete_omeka_theme',
    		 	'read_post' => 'read_omeka_theme',
            ),
            'show_in_nav_menus'     => true,
            'has_archive'           => 'add-ons/themes',
            'hierarchical'          => true,
            'supports'              => array('title', 'editor', 'author'),
            'rewrite'               => array("slug" => 'add-ons/themes')
        );

        register_post_type( 'omeka_theme', $omekaThemePostDef );

    }

    function register_taxonomies()
    {
          
          $labels = array(
            'name' => _x( 'Plugin Categories', 'plugin categories' ),
            'singular_name' => _x( 'Plugin Category', 'plugin category singular name' ),
            'search_items' =>  __( 'Search Plugin Categories' ),
            'all_items' => __( 'All Plugin Categories' ),
            'edit_item' => __( 'Edit Plugin Category' ),
            'update_item' => __( 'Update Plugin Category' ),
            'add_new_item' => __( 'Add New Plugin Category' ),
            'new_item_name' => __( 'New Genre Plugin Category' ),
            'menu_name' => __( 'Plugin Category' ),
          );
        
          register_taxonomy('omeka_plugin_types',array('omeka_plugin'), array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'plugin_categories' ),
          ));
          
        if (get_option('omeka_addons_flush_rewrite_rules') == 'true') {
            flush_rewrite_rules();
            delete_option('omeka_addons_flush_rewrite_rules');
        }
    }
    
    /**
     * Adds our post meta boxes for the 'omeka_plugin' and 'omeka_theme' post type.
     */
    function add_meta_boxes() {
        add_meta_box("omeka-addons-releases", __('Addon Releases', 'omeka-addons'), array($this, "meta_box"), "omeka_plugin", "side", "low");
        add_meta_box("omeka-addons-releases", __('Addon Releases', 'omeka-addons'), array($this, "meta_box"), "omeka_theme", "side", "low");
    }

    /**
     * Meta box for plugin information.
     */
    function meta_box(){
        global $post;

        $html = "";
        $releases = $this->get_releases($post);
        $html .= print_r($releases, true);
        if($releases) {
            foreach($releases as $release) {
                if( $release['new'] ) {
                    $html .= $this->_release_template_message_meta_box($release);
                    $updated = $release;
                    $updated['new'] = false;
                    update_post_meta($release['attachment_id'], 'omeka_addons_release', $updated, $release);
                    if($release['status'] == 'error') {
                        $html .= "<p>Release not saved. Please check the errors and warnings above</p>";
                        if( delete_post_meta($release['attachment_id'], 'omeka_addons_release', $updated ) ) {
                          $html .= "success";
                        }
                        
                    }
                }
                $html .= $this->_release_template_meta_box($release);
            }
        } else {
            $html .= "<p><strong>You have no releases yet.</strong></p>";
        }
        

  		echo $html;
  		
    }
    
    /**
     * Saves our custom post metadata. Used on the 'save_post' hook.
     */
    function save_post()
    {
        global $post;
        $releases = $this->get_releases($post);
        $args = array(
          'post_parent' => $post->ID,
          'post_type' => 'attachment',
          'post_mime_type' => 'application/zip',
          'order' => 'DESC',
          'orderby' => 'post_date',
          'numberposts' => 1
        );
        $attachments = get_children($args);
        
        if ($attachments) {
            $last_attachment = array_pop($attachments);
            $this->add_attachment_release($last_attachment);
        }
        if(!empty($_POST['omeka_addons_delete'])) {
            $this->delete_releases($_POST['omeka_addons_delete']);
        }
    }

    function add_attachment_release($attachment) {
        global $post;
        $zip_id = $attachment->ID;
        $path = get_attached_file($zip_id);
        $url = $attachment->guid;
        $iniData = $this->get_ini_data($path);

        if(is_wp_error($iniData)) {
            $releaseData = array(
                            'status' => 'error',
            				'curl_error'=> array(
            				 	'code' => $iniData->get_error_code(),
                                'message' => $iniData->get_error_message()
                                ),
                            'messages' => array()
                            );
        } else {
            $releaseData = $this->_validate_ini_data($iniData, $post->post_type);
            $releaseData['ini_data'] = $iniData;
            $releaseData['modified'] = $attachment->post_modified;
            $releaseData['file_name'] = $attachment->post_title;
            $releaseData['zip_url'] = $attachment->guid;
            $releaseData['attachment_id'] = $attachment->ID;
            
            // no idea why I couldn't pass the data along in iniData, but it never worked right
            if( !empty($post) && ($post->post_type == 'omeka_theme') ) {
                $args = array(
                    'post_parent' => $post->ID,
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image/jpeg',
                );
                $images = get_children($args);
                foreach($images as $image) {

                    if($image->post_title == 'screenshot-' . $iniData['name']  . '-' . $iniData['version'] . '.jpg') {
                        $imgData = wp_get_attachment_url($image->ID);
                        print_r($imgData);
                        $releaseData['screenshot'] = $imgData;
                    }
                }
            }
            
        }
        $releaseData['new'] = true;
        add_post_meta($zip_id, "omeka_addons_release", $releaseData);
        return $releaseData;
    }
    
    function get_ini_data($path)
    {
        global $post;
        $tempPath = '/tmp';
        $tempName = 'omeka-addon-'. md5(uniqid(rand(), true));
        $tempDir = $tempPath . '/'.$tempName;

        $shellCommand = 'cp '.$path.' '.$tempDir.'.zip'
                      . ' && unzip -d '.$tempDir .' '.$tempDir.'.zip'
                      . ' && rm '.$tempDir.'.zip';

        $return_var = null;
        exec($shellCommand, $output, $return_var);

        $tempDirContents = scandir($tempDir, 1);
        
        if (count($tempDirContents) == 3) {
            $addonFolder = $tempDirContents[0];
            $addonFolderPath = $tempDir. '/'. $addonFolder;

            if (file_exists($addonFolderPath .'/theme.ini')) {
                $iniFile = $addonFolderPath .'/theme.ini';
            }

            if (file_exists($addonFolderPath .'/plugin.ini')) {
                $iniFile = $addonFolderPath .'/plugin.ini';
            }

            if ($iniFile) {
                $ini_array = parse_ini_file($iniFile);
                
                //hacking some things until ini data is consistent
                if(!isset($ini_array['name'])) {
                    $ini_array['name'] = $ini_array['title'];
                }
                if(!isset($ini_array['link'])) {
                    $ini_array['link'] = $ini_array['website'];
                }
                
                if( !empty($post) && ($post->post_type == 'omeka_theme') ) {
                    $is_new = true;
                    //check if image is already there
                    $args = array(
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image/jpeg',
                    );
                    $images = get_children($args);

                    foreach($images as $image) {
                        if($image->post_title == 'screenshot-' . $ini_array['name']  . '-' . $ini_array['version'] . '.jpg') {
                            $is_new = false;
                        }
                    }

                    if( $is_new ) {
                        //make the theme.jpg attachment
                        $filename = $addonFolderPath . '/theme.jpg';
                        $uploadDir = wp_upload_dir();
                        $uploadTarget = $uploadDir['basedir'] . '/screenshot-' . $ini_array['name']  . '-' . $ini_array['version'] . '.jpg' ;
                        $cp = "cp $filename $uploadTarget";
                        exec($cp);
                        $attachment = array(
                             'post_mime_type' => 'image/jpeg',
                             'post_title' =>  'screenshot-' . $ini_array['name']  . '-' . $ini_array['version'] . '.jpg',
                             'post_content' => '',
                             'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment( $attachment, $uploadTarget, $post->ID );
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $uploadTarget );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                    }
                }
                
                
                $rmAddonDir = 'rm -Rf '.$tempDir;
                exec($rmAddonDir, $output, $return_var);
                return $ini_array;
            }
        }
    }
    

    function get_releases($post)
    {
        if ($post) {
            //get the attachments,
            $args = array(
              'post_parent' => $post->ID,
              'post_type' => 'attachment',
              'post_mime_type' => 'application/zip',
              'order' => 'DESC',
              'orderby' => 'post_date',
            );
            $attachments = get_children($args);
            
            $releaseArray = array();
            foreach($attachments as $attachment) {
                $releaseData = get_post_meta($attachment->ID, 'omeka_addons_release', true) ;
                if($releaseData) {
                    $releaseArray[] = $releaseData;
                } else {
                    $releaseArray[] = $this->add_attachment_release($attachment);
                }
                
            }

            usort($releaseArray, array($this, '_sort_by_version'));
            return $releaseArray;
        }
        return false;
    }

    function delete_releases($versions)
    {
       global $post;
       $releases = $this->get_releases($post);
       foreach($releases as $release) {
           if(in_array($release['ini_data']['version'], $versions)) {
               wp_delete_post($release['attachment_id']);
           }
       }
    }
    
    function release_template($release)
    {
        $html = "";
        if(!isset($release['ini_data']['minimum_omeka_version'])) {
            $release['ini_data']['minimum_omeka_version'] = 'unknown';
        }
        //$html .= "<dl class='omeka-addons-ini-data'>";
        $html .= "<tr>";
        $html .= "<td>" . $release['ini_data']['version'] . "</td>";
        $html .= "<td>" . $release['ini_data']['minimum_omeka_version'] . "</td>";
        $html .= "<td><a href='" . $release['zip_url']  . "'>Download</a></td>";
        $html .= "</tr>";

            
        return $html;
    }

    function _release_template_meta_box($release)
    {
        $html = "<div class='omeka-addons-release'>";
        if ($release && isset($release['ini_data'])) {
            $version = $release['ini_data']['version'];
            $html .= "<h3>Version " . $version;
            $html .= "<span class='omeka-addons-delete'><input type='checkbox' name='omeka_addons_delete[]' value='$version' />";
            $html .= "<label for='omeka_addons_delete'>Delete?</label></span></h3>";
            $html .= "<dt>Zip URL:</dt><dd>" . $release['zip_url'] . "</dd><br />";
            if( isset($release['ini_data']) && !empty($release['ini_data'])) {
                foreach($release['ini_data'] as $key=>$value) {
                    $html .= "<dt>$key:</dt><dd>$value</dd><br />";
                }
            }
            $html .= "</dl>";
        }
        $html .= "</div>";
        return $html;
    }
    
    function _release_template_message_meta_box($release)
    {
        global $post;
        $html = "";
        if($release) {
            $status = $release['status'];
            $html = "<div id='omeka-addons-messages' class='omeka-addons-messages omeka-addons-upload-$status'>";
            
            switch ($status) {
                case 'ok' :
                    $html .= "<p class='omeka-addons-ok'>Zip processing successful</p>";
                    break;
                case 'warning':
                    foreach($release['messages'] as $message) {
                        $html .= "<p class='omeka-addons-warning'>$message</p>";
                    }
                    break;
                    
                case 'error':
                    foreach($release['messages'] as $message) {
                        $html .= "<p class='omeka-addons-error'>$message</p>";
                    }
                    break;
            }
            $html .= "</div>";
        }
        return $html;
    }
    
    function _validate_ini_data($iniData, $addon_type)
    {
        
        //common ini fields
        $releaseData = array();
        $releaseData['status'] = 'ok';
        $releaseData['messages'] = array();
        if(!isset($iniData['name'])) {
          $releaseData['status'] = 'error';
          $releaseData['messages'][] = __('name must be set');
          
        }
        if(!isset($iniData['description'])) {
          $releaseData['status'] = 'error';
          $releaseData['messages'][] = __('description must be set');
        }
        if(!isset($iniData['version'])) {
          $releaseData['status'] = 'error';
          $releaseData['messages'][] = __('version must be set');
        }
        //link vs. website in plugins vs themes
        if(!isset($iniData['link'])) {
          $releaseData['status'] = 'error';
          $releaseData['messages'][] = __('link must be set');
        }
        if(!isset($iniData['omeka_minimum_version'])) {
          if($releaseData['status'] != 'error') {
              $releaseData['status'] = 'warning';
          }
          $releaseData['messages'][] = __('omeka_minimum_version should be set');
        }
        if(!isset($iniData['omeka_tested_up_to'])) {
          if($releaseData['status'] != 'error') {
              $releaseData['status'] = 'warning';
          }
          //@TODO: it'd be awesome to check whether it's tested up to the current version
          $releaseData['messages'][] = __('omeka_tested_up_to should be set');
        }
        
        //plugin-specific data
        if($addon_type == 'omeka_plugin') {
            
        }
        
        //theme-specific data
        
        if($addon_type == 'omeka_theme') {
            
        }
        return $releaseData;
    }
    
    function _sort_by_version($a, $b)
    {
        if( !isset($a['ini_data']) || !isset($a['ini_data']) ) {
            return 0;
        }
        
        return version_compare($b['ini_data']['version'], $a['ini_data']['version'] );
    }
    
    function addon_post_content($content)
    {
        global $post;

        $postType = get_query_var('post_type');
        if ($postType == 'omeka_plugin' || $postType == 'omeka_theme') {
            $releases = $this->get_releases($post);
            $content = "<div class='omeka-addons-content'>"  . "<p class='omeka-addons-author'>" . $releases[0]['ini_data']['author'] . "</p>" . $content . "</div>";
            $html = "";
            if ($releases) {
                $html .= "<div class='omeka-addons-addon-info'>";
                $html .= "<a class='omeka-addons-button' href='" . $releases[0]['zip_url'] . "'>Latest Release: Version " . $releases[0]['ini_data']['version'] . "</a></p>";
                $html .= "<p class='omeka-addons-description'>" . $releases[0]['ini_data']['description'] . "</p>";
                $html .= "<p class='omeka-addons-link'><a href='" . $releases[0]['ini_data']['link'] . "'>Web page</a></p>";
                $html .= "<p class='omeka-addons-latest-release'>";
                
                $html .= "<h3>All Versions</h3>";
                $html .= "<table width='100%'>
                    <thead>
                        <tr>
                            <th>Available Versions</th>
                            <th>Minimum Omeka Version</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    ";
                foreach($releases as $release) {
                    $html .= $this->release_template($release);
                }
                $html .= "</tbody></table></div>";
            $content = $content . $html;
            
            }
        }
        return $content;
    }
}

endif;

$omeka_addons = new Omeka_Addons();