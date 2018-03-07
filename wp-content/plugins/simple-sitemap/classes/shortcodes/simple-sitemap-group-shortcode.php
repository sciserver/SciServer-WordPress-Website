<?php
/*
 *	Class for the [simple-sitemap-group] shortcode
*/

class WPGO_Simple_Sitemap_Group_Shortcode {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;
		add_shortcode( 'simple-sitemap-group', array( &$this, 'render' ) );
	}

	/* Shortcode function. */
	public function render($atts) {

		/* Get attributes from the shortcode. */
		$atts = shortcode_atts( array(
			'tax' => 'category', // single taxonomy
			'container_tag' => 'ul',
			'term_order' => 'asc',
			'term_orderby' => 'name',
			'show_excerpt' => 'false',
			'title_tag' => '',
			'excerpt_tag' => 'div',
			'post_type_tag' => 'h2',
			'show_label' => 'true',
			'links' => 'true',
			//'page_depth' => 0,
			'order' => 'asc',
			'orderby' => 'title',
			'exclude' => ''
		), $atts );

		// escape tag names
		$tax = esc_attr( $atts['tax'] );
		$container_tag = tag_escape( $atts['container_tag'] );
		$term_order = esc_attr( $atts['term_order'] );
		$term_orderby = esc_attr( $atts['term_orderby'] );
		$show_excerpt = esc_attr( $atts['show_excerpt'] );
		$title_tag = tag_escape( $atts['title_tag'] );
		$excerpt_tag = tag_escape( $atts['excerpt_tag'] );
		$post_type_tag = tag_escape( $atts['post_type_tag'] );
		$show_label = esc_attr( $atts['show_label'] );
		$links = esc_attr( $atts['links'] );
		//$page_depth = esc_attr( intval( $atts['page_depth'] ) );
		$order = esc_attr( $atts['order'] );
		$orderby = esc_attr( $atts['orderby'] );
		$exclude = esc_attr( $atts['exclude'] );
		$post_type = 'post';

		// Format attributes as necessary

		// force 'ul' or 'ol' to be used as the container tag
		$allowed_container_tags = array('ul', 'ol');
		if(!in_array($container_tag, $allowed_container_tags)) {
			$container_tag = 'ul';
		}

		// convert comma separated strings to arrays
		$exclude = array_map( 'trim', explode( ',', $exclude) ); // must be array to work in the post query

		// get all public registered post types
		$args = array(
			'public'   => true
		);
		$registered_post_types = get_post_types($args);

		//echo "<pre>";
		//print_r($registered_post_types);
		//print_r($post_types);
		//print_r($exclude);
		//echo "</pre>";

		$taxonomy_arr = get_object_taxonomies( $post_type );

		// Start output caching (so that existing content in the [simple-sitemap] post doesn't get shoved to the bottom of the post
		ob_start();

		// sort via specified taxonomy
		if ( !empty($tax) && in_array( $tax, $taxonomy_arr ) ) {

			// conditionally show label for each post type
			if( $show_label == 'true' ) {
				$post_type_obj  = get_post_type_object( $post_type );
				$post_type_name = $post_type_obj->labels->name;
				echo '<' . $post_type_tag . '>' . esc_html($post_type_name) . '</' . $post_type_tag . '>';
			}

			$term_attr = array(
				'orderby'           => $term_orderby,
				'order'             => $term_order
			);
			$terms = get_terms( $tax, $term_attr );

			foreach($terms as $term) {

				// generate sitemap container element class
				$container_class = 'simple-sitemap-' . $post_type;

				// bail if post type isn't valid
				if( !array_key_exists( $post_type, $registered_post_types ) ) {
					break;
				}

				// set opening and closing title tag
				if( !empty($title_tag) ) {
					$title_open = '<' . $title_tag . '>';
					$title_close = '</' . $title_tag . '>';
				}
				else {
					$title_open = $title_close = '';
				}

				$query_args = array(
					'posts_per_page' => -1,
					'post_type' => $post_type,
					'order' => $order,
					'orderby' => $orderby,
					'post__not_in' => $exclude,
					'tax_query' => array(
						array(
							'taxonomy' => $tax,
							'field' => 'slug',
							'terms' => $term
						)
					)
				);

				echo '<h3>' . $term->name . '</h3>';

				//post query
				$sitemap_query = new WP_Query( $query_args );

				if ( $sitemap_query->have_posts() ) :

					echo '<' . $container_tag . ' class="' . esc_attr($container_class) . '">';

					// start of the loop
					while ( $sitemap_query->have_posts() ) : $sitemap_query->the_post();

						// title
						$title_text = get_the_title();

						if( !empty( $title_text ) ) {
							if ( $links == 'true' ) {
								$title = $title_open . '<a href="' . esc_url(get_permalink()) . '">' . esc_html($title_text) . '</a>' . $title_close;
							} else {
								$title = $title_open . esc_html($title_text) . $title_close;
							}
						}
						else {
							if ( $links == 'true' ) {
								$title = $title_open . '<a href="' . esc_url(get_permalink()) . '">' . '(no title)' . '</a>' . $title_close;
							} else {
								$title = $title_open . '(no title)' . $title_close;
							}
						}

						// excerpt
						$excerpt = $show_excerpt == 'true' ? '<' . $excerpt_tag . '>' . esc_html(get_the_excerpt()) . '</' . $excerpt_tag . '>' : '';

						// render list item
						echo '<li>';
						echo $title;
						echo $excerpt;
						echo '</li>';

					endwhile; // end of post loop -->

					echo '</' . $container_tag . '>';

					// put pagination functions here
					wp_reset_postdata();

				else:

					echo '<p>' . __( 'Sorry, no posts matched your criteria.', 'wpgo-simple-sitemap-pro' ) . '</p>';

				endif;
			}
		}
		else {
			echo "No posts found.";
		}

		// ***********
		// CONTENT END
		// ***********
		ob_start();

		$sitemap = ob_get_contents();
		ob_end_clean();

		return wp_kses_post($sitemap);
	}

} /* End class definition */