<?php

/**
 * A paginated product list with:
 * - Product Name
 * - Shipping Class
 * - Weight
 * - Dimensions
 *
 * @author Alexandros Georgiou <alex.georgiou@gmail.com>
 */

// don't load directly
defined( 'ABSPATH' ) || die( -1 );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'Missing_Product_Shipping_Info_List_Table' ) ) {

	class Missing_Product_Shipping_Info_List_Table extends WP_List_Table {

		const PER_PAGE = 20;

		private $order;
		private $orderby;
		private $weight_unit;
		private $dimension_unit;

		public function __construct( $args = array() ) {
			parent::__construct( $args );

			// sorting vars
			$this->order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING );
			$this->orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );

			// get units
			$this->weight_unit    = get_option( 'woocommerce_weight_unit' );
			$this->dimension_unit = get_option( 'woocommerce_dimension_unit' );
		}

		public function get_columns() {
			$columns = array(
				'name'           => esc_html__( 'Product name',                       'missing-product-shipping-info-tool' ),
				'shipping_class' => esc_html__( 'Shipping Class',                     'missing-product-shipping-info-tool' ),
				'weight'         => esc_html__( 'Weight',                             'missing-product-shipping-info-tool' ),
				'dimensions'     =>         __( 'Dimensions (W &times; H &times; L)', 'missing-product-shipping-info-tool' ),
			);

			return $columns;
		}

		public function get_hidden_columns() {
			return array();
		}

		public function get_sortable_columns() {
			return array(
				'name' => array( 'name', true ),
			);
		}

		public function prepare_items() {

			$this->_column_headers = array(
				$this->get_columns(),
				$this->get_hidden_columns(),
				$this->get_sortable_columns(),
			);

			$meta_query_args = array(
				'relation' => 'AND',
				array(
					'key'   => '_virtual',
					'value' => 'no',
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => '_weight',
						'compare' => '<=',
						'type'    => 'DECIMAL(10,10)',
						'value'   => 0,
					),
					array(
						'key'     => '_weight',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_height',
						'compare' => '<=',
						'type'    => 'DECIMAL(10,10)',
						'value'   => 0,
					),
					array(
						'key'     => '_height',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_width',
						'compare' => '<=',
						'type'    => 'DECIMAL(10,10)',
						'value'   => 0,
					),
					array(
						'key'     => '_width',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_length',
						'compare' => '<=',
						'type'    => 'DECIMAL(10,10)',
						'value'   => 0,
					),
					array(
						'key'     => '_length',
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$query_args = array(
				'post_type'      => 'product',
				'posts_per_page' => self::PER_PAGE,
				'paged'          => $this->get_pagenum(),
				'post_status'    => 'publish',
				'meta_query'     => $meta_query_args,
				'orderby'        => 'title',
				'order'          => $this->order,
			);

			$results = new WP_Query( $query_args );

			$this->items = array();

			global $post;

			while ( $results->have_posts() ) {
				$results->the_post();
				$product = wc_get_product( $post->ID );

				$data = array(
					'name'           => get_the_title(),
					'shipping_class' => $product->get_shipping_class(),
					'weight'         => $product->get_weight(),
					'w'              => $product->get_width(),
					'h'              => $product->get_height(),
					'l'              => $product->get_length(),
					'edit_link'      => get_edit_post_link( $post->ID ),
				);

				$this->items[] = $data;
				wp_reset_postdata();
			}

			$this->set_pagination_args(
				array(
					'total_items' => $results->found_posts,
					'per_page'    => self::PER_PAGE,
				)
			);
		}

		public function column_default( $item, $column_name ) {
			if ( ! $item[ $column_name ] ) {
				return '&mdash;';
			}
			return esc_html( $item[ $column_name ] );
		}

		public function column_name( $item ) {
			return sprintf(
				'<a href="%s">%s</a>',
				$item['edit_link'],
				$item['name']
			);
		}

		public function column_weight( $item ) {
			if ( ! $item['weight'] ) {
				return '&mdash;';
			}
			return esc_html( "$item[weight] $this->weight_unit" );
		}

		public function column_dimensions( $item ) {
			if ( ! $item['w'] && ! $item['h'] && ! $item['l'] ) {
				return '&mdash;';
			}

			$u = $this->dimension_unit;

			$dims = array();
			foreach ( array( 'w', 'h', 'l' ) as $d ) {
				$dims[] = $item[ $d ] ? "$item[$d] $u" : '&mdash;';
			}

			return implode( ' &times; ', $dims );
		}

	} // end Missing_Product_Shipping_Info_List_Table
} // end if class not exists
