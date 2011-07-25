<?php get_header(); ?>

	<div id="primary">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
		<div class="omeka-addon">
		<?php the_content(); ?>
		</div>
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>