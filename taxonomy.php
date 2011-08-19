<?php get_header(); ?>

	<div id="primary" class="omeka-addons-taxonomy">
        <?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
		<h1>Plugins for <?php echo $term->name; ?></h1>

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
			<div id="post-<?php the_ID(); ?>">
			<div class="omeka-addons-download-block">
                <p class='omeka-addons-latest-release'>
                	<a class='omeka-addons-button' href='<?php echo $releaseData["zip_url"]; ?>'>Download Latest</a>
                </p>
    			<?php $license = isset($releaseData['ini_data']['license']) ? $releaseData['ini_data']['license'] : 'unknown'; ?>
                <p class='omeka-addons-license'><span>License</span>: <?php echo $license; ?></p>
            </div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
        				
        		<div class="omeka-addons-cats">
        		<?php
        		    $terms = wp_get_post_terms( get_the_ID(), 'omeka_plugin_types');
        		    $html = '';
        		    $html .= "<ul class='omeka-addons-term-list'>";
        		    foreach($terms as $term) {
        		        $link = get_term_link($term);
        		        $html .= "<li><a href='$link'>$term->name</a></li>";
        		    }
        		    $html .= "</ul>";
        		    echo $html;
        		?>
        		</div>
		        <?php the_content(); ?>
			</div>

		<?php endwhile; endif; ?>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
