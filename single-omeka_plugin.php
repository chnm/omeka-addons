<?php get_header(); ?>

	<div id="primary">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
		<div class="omeka-addon">
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
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>
</div>
<?php get_footer(); ?>