<?php get_header(); ?>

	<div id="primary">
<h1>Themes</h1>
		<?php query_posts($query_string . '&orderby=title&order=ASC&posts_per_page=-1'); ?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
			<div class="omeka-addon" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
        				
		        <?php the_content(); ?>

			</div>
		<?php endwhile; endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
