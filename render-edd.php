<?php

/*
Plugin Name: Render EDD
Description: Integrates Easy Digital Downloads with Render for improved shortcode capabilities.
Version: 0.1
Author: Kyle Maurer
Author URI: http://renderwp.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once( 'includes/helper-functions.php' );

/**
 * Class Render_EDD
 */
class Render_EDD {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'plugins_loaded', array( $this, 'render_shortcodes' ) );
	}

	/**
	 * Display a notice in the admin if EDD and Render are not both active
	 */
	static function notice() {
		if ( ! class_exists( 'Render' ) || ! class_exists( 'Easy_Digital_Downloads' ) ) {
			?>
			<div class="error">
				<p>You have activated a plugin that requires <a href="http://renderwp.com/?utm_source=Render%20EDD&utm_medium=Notice&utm_campaign=Render%20EDD%20Notice
">Render</a>
					and <a
						href="http://easydigitaldownloads.com/?utm_source=Render%20EDD&utm_medium=Notice&utm_campaign=Render%20EDD%20Notice">Easy
						Digital Downloads</a>.
					Please install and activate both to continue using.</p>
			</div>
		<?php
		}
	}

	/**
	 * Add data and inputs for all EDD shortcodes and pass them through Render's function
	 */
	static function render_shortcodes() {
		$_shortcodes = array(
			// Download Cart
			array(
				'code'        => 'download_cart',
				'function'    => 'edd_cart_shortcode',
				'title'       => __( 'Download Cart', 'Render' ),
				'description' => __( 'Lists items in cart.', 'Render' ),
				'tags'        => 'cart edd ecommerce downloads digital products',
			),
			// Download Checkout
			array(
				'code'        => 'download_checkout',
				'function'    => 'edd_checkout_form_shortcode',
				'title'       => __( 'Download Checkout', 'Render' ),
				'description' => __( 'Displays the checkout form.', 'Render' ),
				'tags'        => 'cart edd ecommerce downloads digital products form',
			),
			// Download History
			array(
				'code'        => 'download_history',
				'function'    => 'edd_download_history',
				'title'       => __( 'Download History', 'Render' ),
				'description' => __( 'Displays all the products a user has purchased with links to the files.', 'Render' ),
				'tags'        => 'edd ecommerce downloads digital products history files purchase',
			),
			// Purchase History
			array(
				'code'        => 'purchase_history',
				'function'    => 'edd_purchase_history',
				'title'       => __( 'Purchase History', 'Render' ),
				'description' => __( 'Displays the complete purchase history for a user.', 'Render' ),
				'tags'        => 'edd ecommerce downloads digital products history purchase',
			),
			// Download Discounts
			array(
				'code'        => 'download_discounts',
				'function'    => 'edd_discounts_shortcode',
				'title'       => __( 'Download Discounts', 'Render' ),
				'description' => __( 'Lists all the currently available discount codes on your site.', 'Render' ),
				'tags'        => 'edd ecommerce downloads digital products coupon discount code',
			),
			// Profile Editor
			array(
				'code'        => 'edd_profile_editor',
				'function'    => 'edd_profile_editor_shortcode',
				'title'       => __( 'Profile Editor', 'Render' ),
				'description' => __( 'Presents users with a form for updating their profile.', 'Render' ),
				'tags'        => 'edd ecommerce downloads digital user profile account',
			),
			// Login
			array(
				'code'        => 'edd_login',
				'function'    => 'edd_login_form_shortcode',
				'title'       => __( 'Login', 'Render' ),
				'description' => __( 'Displays a simple login form for non-logged in users.', 'Render' ),
				'tags'        => 'edd ecommerce downloads login users form',
				'atts'        => array(
					'redirect' => array(
						'label'       => __( 'URL', 'Render' ),
						'description' => __( 'Optional. Will redirect users to this URL upon successful login.', 'Render' ),
					),
				)
			),
			// Register
			array(
				'code'        => 'edd_register',
				'function'    => 'edd_register_form_shortcode',
				'title'       => __( 'Register', 'Render' ),
				'description' => __( 'Displays a registration form for non-logged in users.', 'Render' ),
				'tags'        => 'edd ecommerce downloads login users form register signup',
				'atts'        => array(
					'redirect' => array(
						'label'       => __( 'URL', 'Render' ),
						'description' => __( 'Optional. Will redirect users to this URL upon successful login.', 'Render' ),
					),
				)
			),
			// Price
			array(
				'code'        => 'edd_price',
				'function'    => 'edd_download_price_shortcode',
				'title'       => __( 'Price', 'Render' ),
				'description' => __( 'Displays the price of a specific download.', 'Render' ),
				'tags'        => 'edd ecommerce downloads product price',
				'atts'        => array(
					'id' => array(
						'label'       => __( 'URL', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'placeholder' => __( 'Download', 'Render' ),
							'callback'    => array(
								'function' => 'render_edd_get_downloads',
							),
						),
					),
					'price_id' => array(
						'label'       => __( 'Price ID', 'Render' ),
						'description' => __( 'Optional. For variable pricing.', 'Render' ),
					),
				)
			),
			// Receipt
			array(
				'code'        => 'edd_receipt',
				'function'    => 'edd_receipt_shortcode',
				'title'       => __( 'Receipt', 'Render' ),
				'description' => __( 'Displays a the complete details of a completed purchase.', 'Render' ),
				'tags'        => 'edd ecommerce downloads purchase receipt confirmation order payment complete checkout',
				'atts'        => array(
					'error'       => array(
						'label' => __( 'Error message', 'Render' ),
					),
					'price'       => array(
						'label'      => __( 'Hide price', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
					'discount'    => array(
						'label'      => __( 'Hide discounts', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
					'products'    => array(
						'label'      => __( 'Hide products', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
					'date'        => array(
						'label'      => __( 'Hide purchase date', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
					'payment_key' => array(
						'label'      => __( 'Hide payment key', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
					'payment_id'  => array(
						'label'      => __( 'Hide order number', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 0,
						),
					),
				),
			),
			// Purchase Link
			array(
				'code'        => 'purchase_link',
				'function'    => 'edd_download_shortcode',
				'title'       => __( 'Purchase Link', 'Render' ),
				'description' => __( 'Displays a button which adds a specific product to the cart.', 'Render' ),
				'tags'        => 'edd ecommerce downloads purchase product buy button pay link checkout',
				'atts'        => array(
					'id'       => array(
						'label' => __( 'Download ID', 'Render' ),
					),
					'sku'      => array(
						'label' => __( 'Hide price', 'Render' ),
					),
					'price'    => array(
						'label'      => __( 'Show price', 'Render' ),
						'type'       => 'checkbox',
						'properties' => array(
							'value' => 1,
						),
					),
					'text'     => array(
						'label' => __( 'Button text', 'Render' ),
					),
					array(
						'type'  => 'section_break',
						'label' => __( 'Style', 'Render' ),
					),
					'style'    => array(
						'label'      => __( 'Style', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'button' => __( 'Button', 'Render' ),
								'text'   => __( 'Text', 'Render' ),
							),
						),
					),
					'color'    => array(
						'label'      => __( 'Button color', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'gray'      => __( 'Gray', 'Render' ),
								'blue'      => __( 'Blue', 'Render' ),
								'green'     => __( 'Green', 'Render' ),
								'dark gray' => __( 'Dark gray', 'Render' ),
								'yellow'    => __( 'Yellow', 'Render' ),
							),
						),
					),
					'class'    => array(
						'label' => __( 'CSS class', 'Render' ),
					),
					'price_id' => array(
						'label' => __( 'Price ID', 'Render' ),
					),
					'direct'   => array(
						'label'      => __( 'Direct purchase', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'true'  => __( 'Yes', 'Render' ),
								'false' => __( 'No', 'Render' ),
							),
						),
					),
				),
			),
			// Purchase Collection
			array(
				'code'        => 'purchase_collection',
				'function'    => 'edd_purchase_collection_shortcode',
				'title'       => __( 'Purchase Collection', 'Render' ),
				'description' => __( 'Displays a button which adds all products in a specific taxonomy term to the cart.', 'Render' ),
				'tags'        => 'edd ecommerce downloads purchase product buy button pay link checkout',
				'atts'        => array(
					'taxonomy'    => array(
						'label'      => __( 'Taxonomy', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'download_category' => __( 'Category', 'Render' ),
								'download_tag'   => __( 'Tag', 'Render' ),
							),
						),
					),
					'terms'    => array(
						'label'      => __( 'Terms', 'Render' ),
						'description' => __( 'Enter a comma separated list of terms for the selected taxonomy.', 'Render' ),
					),
					'text'     => array(
						'label' => __( 'Button text', 'Render' ),
					),
					array(
						'type'  => 'section_break',
						'label' => __( 'Style', 'Render' ),
					),
					'style'    => array(
						'label'      => __( 'Style', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'button' => __( 'Button', 'Render' ),
								'text'   => __( 'Text', 'Render' ),
							),
						),
					),
					'color'    => array(
						'label'      => __( 'Button color', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'gray'      => __( 'Gray', 'Render' ),
								'blue'      => __( 'Blue', 'Render' ),
								'green'     => __( 'Green', 'Render' ),
								'dark gray' => __( 'Dark gray', 'Render' ),
								'yellow'    => __( 'Yellow', 'Render' ),
							),
						),
					),
					'class'    => array(
						'label' => __( 'CSS class', 'Render' ),
					),
				),
			),
			// Downloads
			array(
				'code'        => 'downloads',
				'function'    => 'edd_downloads_query',
				'title'       => __( 'Downloads', 'Render' ),
				'description' => __( 'Outputs a list or grid of downloadable products.', 'Render' ),
				'tags'        => 'edd ecommerce downloads purchase product list',
				'atts'        => array(
					'category'         => array(
						'label'      => __( 'Category', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'placeholder' => __( 'Download category', 'Render' ),
							'callback'    => array(
								'function' => 'render_edd_get_categories',
							),
						),
					),
					'exclude_category' => array(
						'label'      => __( 'Exclude category', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'placeholder' => __( 'Download category', 'Render' ),
							'callback'    => array(
								'function' => 'render_edd_get_categories',
							),
						),
					),
					'tags'             => array(
						'label'      => __( 'Tags', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'placeholder' => __( 'Download tag', 'Render' ),
							'callback'    => array(
								'function' => 'render_edd_get_tags',
							),
						),
					),
					'exclude_tags'     => array(
						'label'      => __( 'Exclude tags', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'placeholder' => __( 'Download tag', 'Render' ),
							'callback'    => array(
								'function' => 'render_edd_get_tags',
							),
						),
					),
					'relation'         => array(
						'label'      => __( 'Relation', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'AND' => __( 'And', 'Render' ),
								'OR'  => __( 'Or', 'Render' ),
							),
						),
					),
					'number'           => array(
						'label'      => __( 'Number', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'gray'      => __( 'Gray', 'Render' ),
								'blue'      => __( 'Blue', 'Render' ),
								'green'     => __( 'Green', 'Render' ),
								'dark gray' => __( 'Dark gray', 'Render' ),
								'yellow'    => __( 'Yellow', 'Render' ),
							),
						),
					),
					'price'            => array(
						'label'      => __( 'Price', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'yes' => __( 'Yes', 'Render' ),
								'no'  => __( 'No', 'Render' ),
							),
						),
					),
					'excerpt'          => array(
						'label'      => __( 'Excerpt', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'yes' => __( 'Yes', 'Render' ),
								'no'  => __( 'No', 'Render' ),
							),
						),
					),
					'full_content'     => array(
						'label'      => __( 'Full content', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'yes' => __( 'Yes', 'Render' ),
								'no'  => __( 'No', 'Render' ),
							),
						),
					),
					'buy_button'       => array(
						'label'      => __( 'Buy button', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'yes' => __( 'Yes', 'Render' ),
								'no'  => __( 'No', 'Render' ),
							),
						),
					),
					'columns'          => array(
						'label'      => __( 'Columns', 'Render' ),
						'type'       => 'counter',
						'properties' => array(
							'max' => 8,
						),
					),
					'thumbnails'       => array(
						'label'      => __( 'Show thumbnails', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'true'  => __( 'Yes', 'Render' ),
								'false' => __( 'No', 'Render' ),
							),
						),
					),
					'orderby'          => array(
						'label'      => __( 'Order by', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'price'     => __( 'Price', 'Render' ),
								'id'        => __( 'ID', 'Render' ),
								'random'    => __( 'Random', 'Render' ),
								'post_date' => __( 'Published date', 'Render' ),
								'title'     => __( 'Title', 'Render' ),
							),
						),
					),
					'order'            => array(
						'label'      => __( 'Order', 'Render' ),
						'type'       => 'selectbox',
						'properties' => array(
							'options' => array(
								'ASC'  => __( 'Ascending', 'Render' ),
								'DESC' => __( 'Descending', 'Render' ),
							),
						),
					),
					'ids'              => array(
						'label' => __( 'IDs', 'Render' ),
					),
				),
			),
		);

		foreach ( $_shortcodes as $shortcode ) {
			$shortcode['category'] = 'Ecommerce';
			$shortcode['source']   = 'Easy Digital Downloads';
			render_add_shortcode( $shortcode );
		}

	}
}

$render_edd = new Render_EDD();