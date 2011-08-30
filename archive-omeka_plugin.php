<?php get_header(); ?>

	<div id="primary" class="omeka-addons-plugin-archive">
<h1>Plugins</h1>
<p>The following plugins may be downloaded separately and installed to work with <strong>Omeka 1.0 or higher</strong>. Helpful instructions for installing plugins are on the <a href="http://omeka.org/codex/Managing_Plugins">codex</a>. If you are looking for plugins compatible with earlier versions of Omeka, see the list of <a href="/add-ons/010-plugins/">plugins compatible with Omeka 0.10</a> and <a href="/add-ons/09-plugins/">plugins compatible with Omeka 0.9</a>.</p>
		<?php query_posts($query_string . '&orderby=title&order=ASC&posts_per_page=-1'); ?>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
			<div class="omeka-addon" id="post-<?php the_ID(); ?>">
			<div class="omeka-addons-download-block">
                <p class='omeka-addons-latest-release'>
                	<a class='omeka-addons-button' href='<?php echo $releaseData["zip_url"]; ?>'>Download Latest</a>
                </p>
    			<?php $license = isset($releaseData['ini_data']['license']) ? $releaseData['ini_data']['license'] : 'unknown'; ?>
                <p class='omeka-addons-license'><span>License</span>: <?php echo $license; ?></p>
            </div>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>

		        <p class='omeka-addons-description'><?php echo $releaseData['ini_data']['description']; ?></p>
        				
        		<div class="omeka-addons-cats">
        		
        		<?php
        		    $terms = wp_get_post_terms( get_the_ID(), 'omeka_plugin_types');
        		    $html = '';
        		    if(!empty($terms)) {
        		        $html .= "<h3>Categories:</h3>";
        		    }
        		    $html .= "<ul class='omeka-addons-term-list'>";
        		    foreach($terms as $term) {
        		        $link = get_term_link($term);
        		        $html .= "<li><a href='$link'>$term->name</a></li>";
        		    }
        		    $html .= "</ul>";
        		    echo $html;
        		?>
        		</div>
			</div>
		<?php endwhile; endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
