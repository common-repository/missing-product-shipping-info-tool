<?php
/**
 * Adds a tool that can help the admin detect products with missing shipping info.
 *
 * Shipping info checked are:
 * - weight
 * - dimensions (width, height, length)
 *
 * Additionally, the shipping class is displayed.
 * Virtual products are excluded from the search as they are not shippable.
 *
 * @author Alexandros Georgiou <alex.georgiou@gmail.com>
 */

// don't load directly
defined( 'ABSPATH' ) || die( -1 );

if ( 'missing-product-shipping-info-tool' == filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) {
	include_once( __DIR__ . '/class-missing-product-shipping-info-list-table.php' );
}

if ( ! class_exists( 'Missing_Product_Shipping_Info_Tool' ) ) {

	final class Missing_Product_Shipping_Info_Tool {

		public function __construct() {
			add_action( 'tool_box', array( &$this, 'intro' ) );
			add_action( 'admin_menu', array( &$this, 'bind_tool_menu' ) );
		}

		public function intro() {

			?>
			<div class="card tool-box">
				<h2><?php esc_html_e( 'Missing Product Shipping Info Tool', 'missing-product-shipping-info-tool' ); ?></h2>

				<p>
				<?php
				printf(
					// translators: %s is replaced with the admin URL for the Missing Product Shipping Info tool
					__(
						'To ensure that shipping costs are calculated accurately, <a href="%s">check here that all your products have weight and dimensions (width, height, length) assigned</a>.',
						'missing-product-shipping-info-tool'
					),
					admin_url( 'tools.php?page=missing-product-shipping-info-tool' )
				);
				?>
				</p>

			</div>
			<?php
		}

		public function bind_tool_menu() {
			if ( current_user_can( 'manage_woocommerce' ) ) {

				add_management_page(
					__( 'Missing Product Shipping Info Tool', 'missing-product-shipping-info-tool' ),
					__( 'Missing Shipping Info', 'missing-product-shipping-info-tool' ),
					'manage_woocommerce',
					'missing-product-shipping-info-tool',
					array( &$this, 'tool_page_cb' )
				);
			}
		}

		public function tool_page_cb() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'missing-product-shipping-info-tool' ) );
			}

			$products_list = new Missing_Product_Shipping_Info_List_Table();

			?>
			<h1><?php esc_html_e( 'Missing Product Shipping Info Tool', 'missing-product-shipping-info-tool' ); ?></h1>

			<p>
			<?php
			printf(
				// translators: %s is replaced with the admin URL to the WooCommerce shipping classes settings page
				__(
					'The following WooCommerce products do not have complete shipping information. Shipping information includes product weight, dimensions (width, height, length), and optionally <a href="%s">shipping class</a>. Virtual (digital) products are not listed.',
					'missing-product-shipping-info-tool'
				),
				admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' )
			);
			?>
			</p>

			<div class="wrap">
			<?php
				$products_list->prepare_items();
				$products_list->display();
			?>
			</div>

			<?php
		}
	}

	new Missing_Product_Shipping_Info_Tool();
}
