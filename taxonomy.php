<?php get_header(); ?>

		<div id="container">
			<div id="content" >
				<div class="padding">
					<div id="primary">
        <?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>
		<h1>Plugins for <?php echo $term->name; ?></h1>

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
			<div class="post" id="post-<?php the_ID(); ?>">
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
		        <?php
				    the_content();
				    
				?>

			</div>

		<?php endwhile; endif; ?>
				</div><!-- #primary -->
				</div><!-- .padding -->
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
