<?php
/**
 * WooCommerce Payment Gateway Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Apple-Pay
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Sets up the Apple Pay settings screen.
 *
 * @since 4.6.0-dev
 */
class SV_WC_Payment_Gateway_Apple_Pay_Admin {


	/** @var \SV_WC_Payment_Gateway_Apple_Pay the Apple Pay handler instance */
	protected $handler;


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $handler ) {

		$this->handler = $handler;

		// add Apple Pay to the checkout settings sections
		add_filter( 'woocommerce_get_sections_checkout', array( $this, 'add_settings_section' ), 99 );

		// output the settings
		add_action( 'woocommerce_settings_checkout', array( $this, 'add_settings' ) );

		// render the special "static" gateway select
		add_action( 'woocommerce_admin_field_static', array( $this, 'render_static_setting' ) );

		// save the settings
		add_action( 'woocommerce_settings_save_checkout', array( $this, 'save_settings' ) );

		// add admin notices for configuration options that need attention
		add_action( 'admin_footer', array( $this, 'add_admin_notices' ), 10 );
	}


	/**
	 * Adds Apple Pay to the checkout settings sections.
	 *
	 * @since 4.6.0-dev
	 * @param array $sections the existing sections
	 * @return array
	 */
	public function add_settings_section( $sections ) {

		$sections['apple-pay'] = __( 'Apple Pay', 'woocommerce-plugin-framework' );

		return $sections;
	}


	/**
	 * Gets all of the combined settings.
	 *
	 * @since 1.0.0
	 * @return array $settings The combined settings.
	 */
	public function get_settings() {

		$settings = array(

			array(
				'title' => __( 'Apple Pay', 'woocommerce-plugin-framework' ),
				'type'  => 'title',
			),

			array(
				'id'              => 'sv_wc_apple_pay_enabled',
				'title'           => __( 'Enable / Disable', 'woocommerce-plugin-framework' ),
				'desc'            => __( 'Accept Apple Pay', 'woocommerce-plugin-framework' ),
				'type'            => 'checkbox',
				'default'         => 'no',
			),

			array(
				'id'      => 'sv_wc_apple_pay_display_locations',
				'title'   => __( 'Allow Apple Pay on', 'woocommerce-plugin-framework' ),
				'type'    => 'multiselect',
				'class'   => 'wc-enhanced-select',
				'css'     => 'width: 350px;',
				'options' => $this->get_display_location_options(),
				'default' => array_keys( $this->get_display_location_options() ),
			),

			array(
				'type' => 'sectionend',
			),
		);

		if ( wc_tax_enabled() || SV_WC_Plugin_Compatibility::wc_shipping_enabled() ) {

			$buy_settings = array(
				array(
					'title' => __( 'Buy Now', 'woocommerce-plugin-framework' ),
					'type'  => 'title',
					'desc'  => sprintf(
						__( 'The %1$sBuy Now with Apple Pay%2$s button is displayed on single product pages, and is only available for simple products. Use these settings to set an optional tax rate and shipping cost for customers who use Buy Now.', 'woocommerce-plugin-framework' ),
						'<strong>', '</strong>'
					),
				),
			);

			if ( wc_tax_enabled() ) {

				$buy_settings[] = array(
					'id'       => 'sv_wc_apple_pay_buy_now_tax_rate',
					'title'    => __( 'Tax Rate', 'woocommerce-plugin-framework' ),
					'type'     => 'text',
					'desc_tip' => __( 'The optional tax rate percentage to apply to Buy Now orders.', 'woocommerce-plugin-framework' ),
				);
			}

			if ( SV_WC_Plugin_Compatibility::wc_shipping_enabled() ) {

				$buy_settings[] = array(
					'id'       => 'sv_wc_apple_pay_buy_now_shipping_cost',
					'title'    => __( 'Shipping Cost', 'woocommerce-plugin-framework' ),
					'type'     => 'text',
					'desc_tip' => __( 'The optional flat-rate shipping cost to add to Buy Now orders.', 'woocommerce-plugin-framework' ),
				);
			}

			$buy_settings[] = array(
				'type' => 'sectionend',
			);

			$settings = array_merge( $settings, $buy_settings );
		}

		$connection_settings = array(
			array(
				'title' => __( 'Connection Settings', 'woocommerce-plugin-framework' ),
				'type'  => 'title',
			),

			array(
				'id'      => 'sv_wc_apple_pay_merchant_id',
				'title'   => __( 'Apple Merchant ID', 'woocommerce-plugin-framework' ),
				'type'    => 'text',
				'desc'  => sprintf(
					/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
					__( 'This is found in your %1$sApple developer account%2$s', 'woocommerce-plugin-framework' ),
					'<a href="https://developer.apple.com" target="_blank">', '</a>'
				),
			),

			array(
				'id'       => 'sv_wc_apple_pay_cert_path',
				'title'    => __( 'Certificate Path', 'woocommerce-plugin-framework' ),
				'type'     => 'text',
				'desc_tip' => 'The full system path to your certificate file from Apple. For security reasons you should store this outside of your web root.',
				'desc'     => sprintf(
					/* translators: Placeholders: %s - the server's web root path */
					__( 'For reference, your current web root path is: %s', 'woocommerce-plugin-framework' ),
					'<code>' . ABSPATH . '</code>'
				),
			),
		);

		$gateway_setting_id = 'sv_wc_apple_pay_payment_gateway';
		$gateway_options    = $this->get_gateway_options();

		if ( 1 === count( $gateway_options ) ) {

			$connection_settings[] = array(
				'id'    => $gateway_setting_id,
				'title' => __( 'Processing Gateway', 'woocommerce-plugin-framework' ),
				'type'  => 'static',
				'value' => key( $gateway_options ),
				'label' => current( $gateway_options ),
			);

		} else {

			$connection_settings[] = array(
				'id'      => $gateway_setting_id,
				'title'   => __( 'Processing Gateway', 'woocommerce-plugin-framework' ),
				'type'    => 'select',
				'options' => $this->get_gateway_options(),
			);
		}

		$connection_settings[] = array(
			'type' => 'sectionend',
		);

		$settings = array_merge( $settings, $connection_settings );

		/**
		 * Filter the combined settings.
		 *
		 * @since 1.0.0
		 * @param array $settings The combined settings.
		 */
		return apply_filters( 'woocommerce_get_settings_apple_pay', $settings );
	}


	/**
	 * Replace core Tax settings with our own when the AvaTax section is being viewed.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function add_settings() {
		global $current_section;

		if ( 'apple-pay' === $current_section ) {

			WC_Admin_Settings::output_fields( $this->get_settings() );

			// add inline javascript
			ob_start();
			?>
				$( '#sv_wc_apple_pay_display_locations' ).change( function() {

					var locations      = $( this ).val();
					var hidden_section = $( '#sv_wc_apple_pay_buy_now_tax_rate, #sv_wc_apple_pay_buy_now_shipping_cost' ).closest( 'table' );
					var hidden_header  = $( hidden_section ).prevUntil( 'table' );

					if ( $.inArray( 'product', locations ) !== -1 ) {
						$( hidden_header ).show();
						$( hidden_section ).show();
					} else {
						$( hidden_header ).hide();
						$( hidden_section ).hide();
					}

				} ).change();
			<?php

			wc_enqueue_js( ob_get_clean() );
		}
	}


	/**
	 * Save the settings.
	 *
	 * @since 1.0.0
	 * @global string $current_section The current settings section.
	 */
	public function save_settings() {

		global $current_section;

		// Output the general settings
		if ( 'apple-pay' == $current_section ) {

			WC_Admin_Settings::save_fields( $this->get_settings() );
		}
	}


	/**
	 * Renders a static setting.
	 *
	 * This "setting" just displays simple text instead of a <select> with only
	 * one option.
	 *
	 * @since 4.6.0-dev
	 * @param array $setting
	 */
	public function render_static_setting( $setting ) {

		?>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $setting['id'] ); ?>"><?php echo esc_html( $setting['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $setting['type'] ) ?>">
				<?php echo esc_html( $setting['label'] ); ?>
				<input
					name="<?php echo esc_attr( $setting['id'] ); ?>"
					id="<?php echo esc_attr( $setting['id'] ); ?>"
					value="<?php echo esc_html( $setting['value'] ); ?>"
					type="hidden"
					>
			</td>
		</tr><?php
	}


	/**
	 * Adds admin notices for configuration options that need attention.
	 *
	 * @since 4.6.0-dev
	 */
	public function add_admin_notices() {

		// if the feature is not enabled, bail
		if ( ! $this->handler->is_enabled() ) {
			return;
		}

		// if not on the settings screen, bail
		if ( ! $this->is_settings_screen() ) {
			return;
		}

		$errors = array();

		// HTTPS notice
		if ( ! wc_site_is_https() ) {
			$errors[] = __( 'Your site must be served over HTTPS with a valid SSL certificate.', 'woocommerce-plugin-framework' );
		}

		// Currency notice
		if ( ! in_array( get_woocommerce_currency(), $this->handler->get_accepted_currencies(), true ) ) {

			$accepted_currencies = $this->handler->get_accepted_currencies();

			$errors[] = sprintf(
				/* translators: Placeholders: %1$s - plugin name, %2$s - a currency/comma-separated list of currencies, %3$s - <a> tag, %4$s - </a> tag */
				_n(
					'Accepts payment in %1$s only. %2$sConfigure%3$s WooCommerce to accept %1$s to enable Apple Pay.',
					'Accepts payment in one of %1$s only. %2$sConfigure%3$s WooCommerce to accept one of %1$s to enable Apple Pay.',
					count( $accepted_currencies ),
					'woocommerce-plugin-framework'
				),
				'<strong>' . implode( ', ', $accepted_currencies ) . '</strong>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '">',
				'</a>'
			);
		}

		// bad cert config notice
		// this first checks if the option has been set so the notice is not
		// displayed without the user having the chance to set it.
		if ( false !== $this->handler->get_cert_path() && ! $this->handler->is_cert_configured() ) {

			$errors[] = sprintf(
				/** translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
				__( 'Your %1$sMerchant Identity Certificate%2$s cannot be found. Please check your path configuration.', 'woocommerce-plugin-framework' ),
				'<strong>', '</strong>'
			);
		}

		if ( ! empty( $errors ) ) {

			$message = '<strong>' . __( 'Apple Pay is disabled.', 'woocommerce-plugin-framework' ) . '</strong>';

			if ( 1 === count( $errors ) ) {
				$message .= ' ' . current( $errors );
			} else {
				$message .= '<ul><li>' . implode( '</li><li>', $errors ) . '</li></ul>';
			}

			$this->handler->get_plugin()->get_admin_notice_handler()->add_admin_notice( $message, 'apple-pay-https-required', array(
				'notice_class' => 'error',
				'dismissible'  => false,
			) );
		}
	}


	/**
	 * Determines if the user is currently on the settings screen.
	 *
	 * @since 4.6.0-dev
	 * @return bool
	 */
	protected function is_settings_screen() {

		return 'wc-settings' === SV_WC_Helper::get_request( 'page' ) && 'apple-pay' === SV_WC_Helper::get_request( 'section' );
	}


	/**
	 * Gets the available display location options.
	 *
	 * @since 4.6.0-dev
	 * @return array
	 */
	protected function get_display_location_options() {

		return array(
			'product'  => __( 'Single products', 'woocommerce-plugin-framework' ),
			'cart'     => __( 'Cart', 'woocommerce-plugin-framework' ),
			'checkout' => __( 'Checkout', 'woocommerce-plugin-framework' ),
		);
	}


	/**
	 * Gets the available gateway options.
	 *
	 * @since 4.6.0-dev
	 * @return array
	 */
	protected function get_gateway_options() {

		$gateways = $this->handler->get_supporting_gateways();

		foreach ( $gateways as $id => $gateway ) {
			$gateways[ $id ] = $gateway->get_method_title();
		}

		return $gateways;
	}


}
