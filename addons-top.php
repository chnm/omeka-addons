<?php
/*
Template Name: Addons Top
*/
?>

<?php get_header(); ?>
<div id="primary">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>
		<div class="addons-wrapper">
			<?php the_content(); ?>
		</div>
	</div>

	<?php endwhile; endif; ?>
	<div id="omeka-addons-top">
		<div class="omeka-addons-top-plugins">
            
        	<div class="omeka-addons-col-head">
        		<h2>Plugins</h2>
            	<p>
            		<a id="download-plugins" href="/addons/plugins">All Plugins</a> &#x25CA;
            		<a href="/get-involved/develop/">Build a new plugin</a>
            	</p>
        	</div>
        	<h3>Most recently updated plugins</h3>
            <?php
                $args = array('post_type'=>'omeka_plugin', 'numberposts'=>2, 'orderby'=>'modified' , 'order'=>'DESC');
                $recent_plugins = get_posts($args);
            ?>
            <?php foreach( $recent_plugins as $post ) :	setup_postdata($post); ?>
            <?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
            	<div class="omeka-addon">
                	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                	<p class='omeka-addons-description'><?php echo $releaseData['ini_data']['description']; ?></p>
            	</div>
            <?php endforeach; ?>


        	<div class="omeka-addons-cats">
        		<h3>You can also browse plugins by category:</h3>
            	<?php
            	    $terms = get_terms( 'omeka_plugin_types', array('orderby'=>'name' ));
            	    $html = '';
            	    $html .= "<ul>";
            	    foreach($terms as $term) {
            	        $link = get_term_link($term);
            	        $html .= "<li><a href='$link'>$term->name</a></li>";
            	    }
            	    $html .= "</ul>";
            	    echo $html;
            	?>
        	</div>
        </div>
		<div  class="omeka-addons-top-themes">
			<div class="omeka-addons-col-head">
    			<h2>Themes</h2>
            	<p>
                	<a id="download-themes" href="/addons/themes">All themes</a> &#x25CA;
                	<a href="/get-involved/design/">Design a new theme</a>
            	</p>
			</div>
            <h3>Most recently updated themes</h3>

            <?php
                $args['post_type'] = 'omeka_theme';
                $recent_themes = get_posts($args);
            ?>
            <?php foreach( $recent_themes as $post ) :	setup_postdata($post); ?>
            <?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
            	<div class="omeka-addon">
                	<a href="<?php the_permalink(); ?>"><img class="omeka-addons-screenshot" src="<?php omeka_addons_the_screenshot($post->ID); ?>" /></a>
                	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                	<p class='omeka-addons-description'><?php echo $releaseData['ini_data']['description']; ?></p>
            	</div>
            	<div class="clear"> </div>
            <?php endforeach; ?>

        </div>
    </div>
        
</div>
<?php get_footer(); ?>
