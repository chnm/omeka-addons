<?php get_header(); ?>

	<div id="primary" class="omeka-addons-archive">
<h1>Themes</h1>
		<?php query_posts($query_string . '&orderby=title&order=ASC&posts_per_page=-1'); ?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
			<div class="omeka-addon" id="post-<?php the_ID(); ?>">
		        <img class='omeka-addons-screenshot omeka-addons-archive' src="<?php omeka_addons_the_screenshot($post->ID)?>" />
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
        		<?php $releaseData = omeka_addons_get_latest_release_data($post->ID); ?>
		        <p class='omeka-addons-description'><?php echo $releaseData['ini_data']['description']; ?></p>
                <?php $license = isset($releaseData['ini_data']['license']) ? $releaseData['ini_data']['license'] : 'unknown'; ?>
                <p class='omeka-addons-license'><span>License</span>: <?php echo $license; ?></p>
                <p class='omeka-addons-latest-release'>
                	<a class='omeka-addons-button' href='<?php echo $releaseData["zip_url"]; ?>'>Download Latest: Ver. <?php echo $releaseData['ini_data']['version']; ?></a>
                </p>


			</div>
		<?php endwhile; endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
