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
        add_action( 'post_edit_form_tag' , array($this, 'post_edit_form_tag') );


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
    
    function post_edit_form_tag( ) {
        echo ' enctype="multipart/form-data"';
    }
    
    function add_role() {
        //debug. uncomment remove_role below to work with fixing role settings
        remove_role('omeka_addon_contributor');
        
        $result = add_role('omeka_addon_contributor', 'Addon Contributor', array(
            'edit_posts' => false,
            'delete_posts' => false,
            'edit_omeka_plugins' => true,
            'edit_omeka_plugin' => true,
            'edit_omeka_themes' => true,
            'edit_omeka_theme' => true,
            'delete_omeka_plugins' => true,
            'delete_omeka_plugin' => true,
            'delete_omeka_themes' => true,
            'delete_omeka_theme' => true,
        	'publish_omeka_plugins' => false,
            'publish_omeka_themes' => false,
            'upload_files' => true,
            'read' => true
        ));
        
        $admin = get_role('administrator');
        $admin_caps = array(
        	'read_omeka_themes',
        	'read_omeka_plugins',
        	'edit_omeka_themes',
        	'edit_omeka_plugins',
        	'delete_omeka_theme',
        	'delete_omeka_plugin',
        	'edit_omeka_theme',
        	'edit_omeka_plugin',
            'edit_others_omeka_themes',
            'edit_others_omeka_plugins',
            'delete_others_omeka_themes',
            'delete_others_omeka_plugins',
            'publish_omeka_themes',
        	'publish_omeka_plugins',
        );
        foreach($admin_caps as $cap) {
            $admin->add_cap($cap);
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
                'delete_posts' => 'delete_omeka_plugins',
            	'delete_others_posts' => 'delete_others_omeka_plugins',
    			'read_private_posts' => 'read_private_omeka_plugins',
    			'edit_post' => 'edit_omeka_plugin',
    			'delete_post' => 'delete_omeka_plugin',
    		 	'read_post' => 'read_omeka_plugin',
            ),
			'capabilities' => $caps,
 			//'map_meta_cap' => true,
            'show_in_nav_menus'     => true,
            'has_archive'           => 'add-ons/plugins',
            'supports'              => array('title', 'editor', 'author'),
            'rewrite'               => array("slug" => 'add-ons/plugins', 'with_front'=>false)
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
            	'delete_others_posts' => 'delete_others_omeka_themes',
    			'read_private_posts' => 'read_private_omeka_themes',
    			'edit_post' => 'edit_omeka_theme',
    			'delete_post' => 'delete_omeka_theme',
    		 	'read_post' => 'read_omeka_theme',
            ),
            'show_in_nav_menus'     => true,
            //'map_meta_cap' => true,
            'has_archive'           => 'add-ons/themes',
            'hierarchical'          => true,
            'supports'              => array('title', 'editor', 'author'),
            'rewrite'               => array("slug" => 'add-ons/themes', 'with_front'=>false)
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

        $html = "<div id='omeka-addons-upload'>";
        $html .= '<form enctype="multipart/form-data" action="__URL__" method="POST" >';
        $html .= "<label for='omeka-addons-file'>Add:</label><input id='omeka-addons-file' type='file' name='omeka_addons_file' />";
        $html .= "</form>";
        $html .= "</div>";

        $messages_html = "";
        $releases_html = "";
        $releases = $this->get_releases($post);
        if($releases) {
            foreach($releases as $release) {
                if( $release['new'] ) {
                    $messages_html .= $this->_release_template_message_meta_box($release);
                    if($release['status'] == 'error') {
                       wp_delete_attachment($release['attachment_id']);
                    } else {
                        $updated = $release;
                        $updated['new'] = false;
                        update_post_meta($release['attachment_id'], 'omeka_addons_release', $updated, $release);
                    }
                }
                if($release['status'] != 'error') {
                    $releases_html .= $this->_release_template_meta_box($release);
                }
            }
            $html .= $messages_html . $releases_html;
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
    
        if(!empty($_POST['omeka_addons_delete'])) {
            $this->delete_releases($_POST['omeka_addons_delete']);
        }
        if(isset($_FILES['omeka_addons_file']) && $_FILES['omeka_addons_file']['size'] !=0 ) {
            
            $attachment_data = $_FILES['omeka_addons_file'];

            if($this->_create_attachment($attachment_data) ) {

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
            }
        }

    }

    function add_attachment_release($attachment) {

        global $post;

        $uploads = wp_upload_dir();
        $zip_id = $attachment->ID;
        $path = get_attached_file($zip_id);
        $url = $uploads['baseurl'] . "/" . $attachment->post_title;
        $iniData = $this->get_ini_data($path);
        if($iniData) {
            $releaseData = $this->_validate_ini_data($iniData);
            $releaseData['ini_data'] = $iniData;
            $releaseData['modified'] = $attachment->post_modified;
            $releaseData['file_name'] = $attachment->post_title;
            $releaseData['zip_url'] = $url;
            $releaseData['attachment_id'] = $attachment->ID;

            // no idea why I couldn't pass the data along in iniData, but it never worked right
            if( !empty($post) && ($post->post_type == 'omeka_theme') ) {
                $args = array(
                    'post_parent' => $post->ID,
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image/jpeg',
                );
                $images = get_children($args);
                $sanitized_name = sanitize_file_name($iniData['name']);
                foreach($images as $image) {
                    if($image->post_title == 'screenshot-' . $sanitized_name  . '-' . $iniData['version'] . '.jpg') {
                        $imgData = wp_get_attachment_url($image->ID);
                        $releaseData['screenshot'] = $imgData;
                    }
                }
            }
        } else {
            $releaseData = array(
                           		'status' => 'error',
                                'messages' => array('Problem reading the zip file contents. Please check that it follows the correct structure'),
                                'ini_data' => array('version' => 'error'), //to make deleting work
                                'attachment_id' => $attachment->ID
                            );
            //@TODO: check if it is a Mac zip
        }
        
        if($attachment->post_title == 'fail') {
            $releaseData = array(
                                'status' => 'error',
                                'messages' => array('There was a conflict with the version number and filename'),
                                'ini_data' => array('version' => 'error'), //to make deleting work
                                'attachment_id' => $attachment->ID
                            );
        }
        
        $releaseData['new'] = true;
        
        add_post_meta($zip_id, "omeka_addons_release", $releaseData);
    }
    
    function get_ini_data($path, $data_only = false)
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

                $this->_normalize_ini($ini_array);
                if($data_only) {
                    //skip the file stuff and just give a basic data
                                        
                    $rmAddonDir = 'rm -Rf '.$tempDir;
                    exec($rmAddonDir, $output, $return_var);
                    return $ini_array;
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
                    $sanitized_name = sanitize_file_name($ini_array['name']);
                    foreach($images as $image) {
                        if($image->post_title == 'screenshot-' . $sanitized_name  . '-' . $ini_array['version'] . '.jpg') {
                            $is_new = false;
                        }
                    }

                    if( $is_new ) {
                        //make the theme.jpg attachment
                        $filename = $addonFolderPath . '/theme.jpg';
                        $uploadDir = wp_upload_dir();
                        $uploadTarget = $uploadDir['basedir'] . '/screenshot-' . $sanitized_name . '-' . $ini_array['version'] . '.jpg' ;
                        $cp = "cp $filename $uploadTarget";
                        exec($cp);
                        $attachment = array(
                             'post_mime_type' => 'image/jpeg',
                             'post_title' =>  'screenshot-' . $sanitized_name  . '-' . $ini_array['version'] . '.jpg',
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
            return false;
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
        $downloadTitleText = "Download version " . $release['ini_data']['version'];
        $html .= "<tr>";
        $html .= "<td><a title='$downloadTitleText' href='" . $release['zip_url']  . "'>" . $release['ini_data']['version'] . "</a></td>";
        $html .= "<td>" . $release['ini_data']['omeka_minimum_version'] . "</td>";
        $html .= "<td>" . $release['ini_data']['omeka_target_version'] . "</td>";
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
            $html = "<div id='omeka-addons-messages'>";
            
            $html .= "<p class='omeka-addons-upload-$status'>";
            switch ($status) {
                case 'ok' :
                    $html .= "Zip processing successful</p>";
                    break;
                case 'warning':
                    $html .= "Some recommended information is missing:</p>";
                    foreach($release['messages'] as $message) {
                        $html .= "<p class='omeka-addons-warning'>$message</p>";
                    }
                    break;
                    
                case 'error':
                    $html .= "There was a problem with the addon data. Please check the messages below and try again.</p>";
                    foreach($release['messages'] as $message) {
                        
                        $html .= "<p class='omeka-addons-error'>$message</p>";
                    }
                    break;
            }
            $html .= "</div>";
        }
        return $html;
    }
    
    function _normalize_file_name($filename, $version)
    {
        //want in the form of name-#.# , e.g. MyAddon-1.2 with 1.2 matching the version
        $name_parts = pathinfo($filename);
        $exploded_name = explode('-', $name_parts['filename']);
        if(isset($exploded_name[1]) && (version_compare($exploded_name[1], $version) === 0) ) {
            return $filename;
        }
        
        if(!isset($exploded_name[1])) {
            return $exploded_name[0] . '-' . $version . '.' . $name_parts['extension'];
        }
        //makes an error downstream
        return 'fail';
        
    }
    
    function _validate_ini_data($iniData)
    {
        
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
        if(!isset($iniData['link'])) {
          $releaseData['status'] = 'warning';
          $releaseData['messages'][] = __('link must be set, even if it goes to the addons page');
        }
        if(!isset($iniData['support_link'])) {
          $releaseData['status'] = 'error';
          $releaseData['messages'][] = __('support_link must be set');
        }
                
        if(!isset($iniData['license'])) {
          if($releaseData['status'] != 'error') {
              $releaseData['status'] = 'warning';
          }
          $releaseData['messages'][] = __('license is not set');
        }
        if(!isset($iniData['omeka_minimum_version'])) {
          if($releaseData['status'] != 'error') {
              $releaseData['status'] = 'warning';
          }
          $releaseData['messages'][] = __('omeka_minimum_version is not set');
        }

        if(!isset($iniData['omeka_target_version'])) {
          if($releaseData['status'] != 'error') {
              $releaseData['status'] = 'warning';
          }
          $releaseData['messages'][] = __('omeka_target_version is not set');
        } else {
            //check omeka latest version
            $omeka_version = $this->_fetch_latest_omeka_version();

            if (version_compare($omeka_version['major'] , $iniData['omeka_target_version'], '>') === 1 ) {
                if($releaseData['status'] != 'error') {
                    $releaseData['status'] = 'warning';
                }
                $releaseData['messages'][] = __("There is a more recent omeka version than the target version");
            }
        }

        if($this->_version_exists($iniData['version'])) {
            $releaseData['status'] = 'error';
            $releaseData['messages'][] = __('The version you uploaded already exists.');
        }
        return $releaseData;
    }
    
    function _version_exists($version)
    {
        global $post;
        $releases = $this->get_releases($post);
        foreach($releases as $release) {
            if($release['ini_data']['version'] == $version) {
                return true;
            }
        }
        return false;
    }
    
    function _sort_by_version($a, $b)
    {
        if( !isset($a['ini_data']) || !isset($b['ini_data']) ) {
            return 0;
        }
        return version_compare($b['ini_data']['version'], $a['ini_data']['version'] );
    }
    
    function _normalize_ini(&$ini_array)
    {
        if(!isset($ini_array['name']) && isset($ini_array['title']) ) {
            $ini_array['name'] = $ini_array['title'];
        }
        if(!isset($ini_array['link']) && isset($ini_array['website'])) {
            $ini_array['link'] = $ini_array['website'];
        }
        if(!isset($ini_array['omeka_target_version']) && isset($ini_array['omeka_tested_up_to']) ) {
            $ini_array['omeka_target_version'] = $ini_array['omeka_tested_up_to'];
        }
        if(!isset($ini_array['omeka_minimum_version'])) {
            $ini_array['omeka_minimum_version'] = 'unknown';
        }
        if(!isset($ini_array['omeka_target_version'])) {
            $ini_array['omeka_target_version'] = 'unknown';
        }
            
        if(!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $ini_array['link'])) {
            $ini_array['link'] = 'http://' . $ini_array['link'];
        }
        if(!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $ini_array['support_link'])) {
           $ini_array['support_link'] = 'http://' . $ini_array['support_link'];
        }
    }
    
    function _fetch_latest_omeka_version()
    {
        $version = file_get_contents('http://api.omeka.org/latest-version');
        return array(
        	'major' => substr(0, 2),
            'full' => $version
            );
    }
    
    /*
     * Manually creates a new attachment from a directly uploaded file
     * bypasses the unneeded interface of the media attachment button
     */
    
    function _create_attachment($data)
    {
        global $post;
        $name = sanitize_file_name($data['name']);

        $uploads = wp_upload_dir();
        $uploads_dir = $uploads['basedir'];
        
        $iniData = $this->get_ini_data($data['tmp_name'], true);
        $name = $this->_normalize_file_name($name, $iniData['version']);
        $filename = "$uploads_dir/$name";
        if(file_exists($filename)) {

            //check the .ini data to see if it is a new version
            //if so, add the version as a suffix
                       
            $fileParts = pathinfo($filename);

            if($this->_version_exists($iniData['version'])) {
                //temporarily give it a distinct name
                $name = $fileParts['filename'] . "-duplicate";
            }
            $filename = "$uploads_dir/$name." . $fileParts['extension'];
        }

        move_uploaded_file($data['tmp_name'], $filename);
        $attachment = array(
             'post_mime_type' => $data['type'],
             'post_title' =>  $name,
             'post_content' => '',
             'post_status' => 'inherit'
        );
        //WP documentation says to use this pattern, but also says that generate
        //attachment_metadata only works for images.
        $attach_id = wp_insert_attachment( $attachment,  $filename, $post->ID );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        //$attach_data = wp_generate_attachment_metadata( $attach_id, "$uploads_dir/$name" );
        $attach_data = array('guid' => $uploads['baseurl'] . "/$name");
        
        wp_update_attachment_metadata( $attach_id, $attach_data );
        return true;
    }
    
    function addon_post_content($content)
    {
        global $post;

        $postType = get_query_var('post_type');
        if ($postType == 'omeka_plugin' || $postType == 'omeka_theme') {
            $releases = $this->get_releases($post);
            $html = "";
            if ($releases) {
                $pre_content = "<div class='omeka-addons-content'>"  . "<p class='omeka-addons-author'>";
                $pre_content .= $releases[0]['ini_data']['author'] . "</p>";
                $pre_content .= "<p class='omeka-addons-description'>" . $releases[0]['ini_data']['description'] . "</p>";
                $content = $pre_content . $content . "</div>";

                $html .= "<div class='omeka-addons-addon-info'>";
                
                if(isset($releases[0]['screenshot'])) {
                    $screenshot = $releases[0]['screenshot'];
                    $html .= "<img class='omeka-addons-screenshot' src='$screenshot' />";
                }
                //links
                $html .= "<p class='omeka-addons-links'>";
                
                if(get_permalink($post->ID) != $releases[0]['ini_data']['link']) {
                    $html .= "<span class='omeka-addons-link'><a href='" . $releases[0]['ini_data']['link'] . "'>More Info</a></span>";
                }

                if(isset($releases[0]['ini_data']['support_link'])) {
                    $html .= "<span class='omeka-addons-support-link'><a href='" . $releases[0]['ini_data']['support_link'] . "'>Get Support</a></span>";
                }
                
                $html .= "</p>";
                $license = isset($releases[0]['ini_data']['license']) ? $releases[0]['ini_data']['license'] : 'unknown';
                $html .= "<p class='omeka-addons-license'><span>License</span>: $license</p>";
                $html .= "<p class='omeka-addons-latest-release'>";
                $html .= "<a class='omeka-addons-button' href='" . $releases[0]['zip_url'] . "'>Download Latest: Ver. " . $releases[0]['ini_data']['version'] . "</a>";
                $html .= "</p>";
                $html .= "<h3>All Versions</h3>";
                $html .= "<table width='100%'>
                    <thead>
                        <tr>
                            <th>Available Versions</th>
                            <th>Minimum Omeka Version</th>
                            <th>Target Omeka Version</th>
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

function omeka_addons_the_screenshot($theme_id) {
    $releaseData = omeka_addons_get_latest_release_data($theme_id);
    echo $releaseData['screenshot'];
}

function omeka_addons_get_latest_release_data($post_id) {
    $args = array(
      'post_parent' => $post_id,
      'post_type' => 'attachment',
      'post_mime_type' => 'application/zip',
      'order' => 'DESC',
      'orderby' => 'post_date',
      'numberposts' => 1
    );
    $attachment = array_pop(get_children($args));
    $releaseData = get_post_meta($attachment->ID, 'omeka_addons_release', true) ;
    return $releaseData;
}
