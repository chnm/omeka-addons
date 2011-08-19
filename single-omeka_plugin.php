<?php get_header(); ?>

	<div id="primary">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>">
			<h1><?php the_title();  omeka_addons_by($post->ID);  ?></h1>
		<div class="omeka-addon">

		
		    <?php the_content(); ?>
		</div>
		<div class="omeka-addons-cats">
		<h3>Plugin categories:</h3>
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
	<?php endwhile; else: ?>
		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>
</div>
<?php get_footer(); ?>