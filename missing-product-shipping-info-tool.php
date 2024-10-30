<?php
/*
 * Plugin Name: Missing Product Shipping Info tool
 * Description: Find WooCommerce products that do not have any shipping information (dimensions and/or weight)
 * Version: 1.0.1
 * Plugin URI: https://www.shipping-calculators.gr/product/missing-product-shipping-info-tool
 * Author: Shipping Calculators Greece <info@shipping-calculators.gr>
 * Author URI: https://shipping-calculators.gr
 * Text Domain: missing-product-shipping-info-tool
 * Domain Path: /languages/
 * Requires at least: 4.5
 * Tested up to: 5.7
 * Requires PHP: 5.4
 * WC requires at least: 3.2.0
 * WC tested up to: 3.5.6
 * License: GPLv2 or later
 *
 * @package missing-product-shipping-info-tool
 * @since 1.0.0
 * @author Alexandros Georgiou <alex.georgiou@gmail.com>
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright Alexandros Georgiou <alex.georgiou@gmail.com>
*/

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

include_once dirname( __FILE__ ) . '/includes/class-missing-product-shipping-info-tool.php';

if ( ! class_exists( 'Missing_Product_Info' ) ) {

	class Missing_Product_Info {

		public function __construct() {
			// language domain
			add_action( 'plugins_loaded', array( &$this, 'load_language' ) );

			// display extra info in plugins list
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'plugin_action_links_filter' ) );
		}

		public function load_language() {
			// load locale
			load_plugin_textdomain( 'missing-product-shipping-info-tool', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		public function plugin_action_links_filter( $links ) {
			if ( class_exists( 'Missing_Product_Info' ) ) {
				$links[] = '<a href="' . admin_url( 'tools.php?page=missing-product-shipping-info-tool' ) . '">'
					. __( 'Missing Product Shipping Info tool', 'missing-product-shipping-info-tool' ) . '</a>';
			}
			$links[] = '<a href="https://wordpress.org/support/plugin/missing-product-shipping-info-tool" style="color: red;">' . __( 'Support', 'missing-product-shipping-info-tool' ) . '</a>';
			return $links;
		}


	} // end class Missing_Product_Info
} // end if class exists

// Instantiate the plugin class
new Missing_Product_Info();
