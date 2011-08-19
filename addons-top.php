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

		<div id="featured-plugins" class="featured-addons">
            <h2>Latest Plugins</h2>
        	<p class="featured-nav"  id="omeka-addons-plugin-featured">
        		<a id="download-plugins" href="/addons/plugins">Browse Plugins</a>
        		<a href="/get-involved/develop/">Build a Plugin</a>
        	</p>
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
		<div id="featured-themes" class="featured-addons">
            <h2>Latest Themes</h2>
        	<p class="featured-nav" id="omeka-addons-theme-featured">
            	<a id="download-themes" href="/addons/themes">Browse Themes</a>
            	<a href="/get-involved/design/">Design a Theme</a>
        	</p>
            <?php
                $args['post_type'] = 'omeka_theme';
                $recent_themes = get_posts($args);
            ?>
            <?php foreach( $recent_themes as $post ) :	setup_postdata($post); ?>
            <?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
            	<div class="omeka-addon">
                	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                	<a href="<?php the_permalink(); ?>"><img class="omeka-addons-screenshot" src="<?php omeka_addons_the_screenshot($post->ID); ?>" /></a>
                	<p class='omeka-addons-description'><?php echo $releaseData['ini_data']['description']; ?></p>
            	</div>
            <?php endforeach; ?>

        </div>
</div>
<?php get_footer(); ?>
