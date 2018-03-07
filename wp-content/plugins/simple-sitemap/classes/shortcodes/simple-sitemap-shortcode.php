<?php
/*
 *	Class for the [simple-sitemap] shortcode
*/

class WPGO_Simple_Sitemap_Shortcode {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;
		add_shortcode( 'simple-sitemap', array( &$this, 'render' ) );

		// Allow shortcodes to be used in widgets (the callbacks are WordPress functions)
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/* Shortcode function. */
	public function render($atts) {

		/* Get attributes from the shortcode. */
		$atts = shortcode_atts( array(
			'types' => 'page',
			'show_label' => 'true',
			'show_excerpt' => 'false',
			'container_tag' => 'ul',
			'title_tag' => '',
			'post_type_tag' => 'h2',
			'excerpt_tag' => 'div',
			'links' => 'true',
			'page_depth' => 0,
			'order' => 'asc',
			'orderby' => 'title',
			'exclude' => ''
		), $atts, 'simple-sitemap' );

		// Setup more readable attribute vars

		$post_types = esc_attr( $atts['types'] );
		$show_label = esc_attr( $atts['show_label'] );
		$show_excerpt = esc_attr( $atts['show_excerpt'] );
		$container_tag = tag_escape( $atts['container_tag'] );
		$title_tag = tag_escape( $atts['title_tag'] );
		$post_type_tag = tag_escape( $atts['post_type_tag'] );
		$excerpt_tag = tag_escape( $atts['excerpt_tag'] );
		$links = esc_attr( $atts['links'] );
		$page_depth = esc_attr( intval( $atts['page_depth'] ) );
		$order = esc_attr( $atts['order'] );
		$orderby = esc_attr( $atts['orderby'] );
		$exclude = esc_attr( $atts['exclude'] );

		// Format attributes as necessary

		// force 'ul' or 'ol' to be used as the container tag
		$allowed_container_tags = array('ul', 'ol');
		if(!in_array($container_tag, $allowed_container_tags)) {
			$container_tag = 'ul';
		}

		//echo "<pre>";
		//print_r($registered_post_types);
		//print_r($post_types);
		//print_r($exclude);
		//print_r($container_tag);
		//echo "</pre>";

		// convert comma separated strings to arrays
		$post_types = array_map( 'trim', explode( ',', $post_types ) );
		$exclude = array_map( 'trim', explode( ',', $exclude) );

		// get all public registered post types
		$args = array(
			'public'   => true
		);
		$registered_post_types = get_post_types($args);

		// Start output caching (so that existing content in the [simple-sitemap] post doesn't get shoved to the bottom of the post
		ob_start();

		foreach( $post_types as $post_type ) :

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

			// conditionally show label for each post type
			if( $show_label == 'true' ) {
				$post_type_obj  = get_post_type_object( $post_type );
				$post_type_name = $post_type_obj->labels->name;
				echo '<' . $post_type_tag . '>' . esc_html($post_type_name) . '</' . $post_type_tag . '>';
			}

			$query_args = array(
				'posts_per_page' => -1,
				'post_type' => $post_type,
				'order' => $order,
				'orderby' => $orderby,
				'post__not_in' => $exclude
			);

			// use custom rendering for 'page' post type to properly render sub pages
			if( $post_type == 'page' ) {
				$arr = array(
					'title_tag' => $title_tag,
					'links' => $links,
					'title_open' => $title_open,
					'title_close' => $title_close,
					'page_depth' => $page_depth,
					'exclude' => $exclude
				);
				echo '<' . $container_tag . ' class="' . esc_attr($container_class) . '">';
				$this->list_pages($arr, $query_args);
				echo '</' . $container_tag . '>';
				continue;
			}

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

		endforeach;

		// ***********
		// CONTENT END
		// ***********

		$sitemap = ob_get_contents();
		ob_end_clean();

		return wp_kses_post($sitemap);
	}

	public function list_pages( $arr, $query_args ) {

		$map_args = array(
			'title' => 'post_title',
			'date' => 'post_date',
			'author' => 'post_author',
			'modified' => 'post_modified'
		);

		// modify the query args for get_pages() if necessary
		$orderby = array_key_exists( $query_args['orderby'], $map_args ) ? $map_args[$query_args['orderby']] : $query_args['orderby'];

		$r = array(
			'depth' => $arr['page_depth'],
			'show_date' => '',
			'date_format' => get_option( 'date_format' ),
			'child_of' => 0,
			'exclude' => $arr['exclude'],
			'echo' => 1,
			'authors' => '',
			'sort_column' => $orderby,
			'sort_order' => $query_args['order'],
			'link_before' => '',
			'link_after' => '',
			'item_spacing' => '',
			//'walker' => '',
		);

		$output = '';
		$current_page = 0;
		$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] ); // sanitize, mostly to keep spaces out

		// Query pages.
		$r['hierarchical'] = 0;
		$pages = get_pages( $r );

		if ( ! empty( $pages ) ) {
			global $wp_query;
			if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
				$current_page = get_queried_object_id();
			} elseif ( is_singular() ) {
				$queried_object = get_queried_object();
				if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
					$current_page = $queried_object->ID;
				}
			}

			$output .= walk_page_tree( $pages, $r['depth'], $current_page, $r );
		}

		// remove links
		if( $arr['links'] != 'true' )
			$output = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $output);

		if ( $r['echo'] ) {
			echo $output;
		} else {
			return $output;
		}
	}

} /* End class definition */