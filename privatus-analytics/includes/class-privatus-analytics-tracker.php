<?php
/**
 * Prints the tracker snippet in wp_head.
 *
 * @package PrivatusAnalytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end snippet injection.
 */
final class Privatus_Analytics_Tracker {

	/**
	 * Queue stub so privatus.track() can be called before pa.js has loaded.
	 * Calls are replayed by pa.js. Pure JavaScript, no user input.
	 */
	const STUB = "window.privatus=window.privatus||{q:[]};['track','pageview','props','optOut','optIn','ready'].forEach(function(m){window.privatus[m]=window.privatus[m]||function(){window.privatus.q.push([m,Array.prototype.slice.call(arguments)])}});";

	/**
	 * Hook into WordPress.
	 */
	public function register() {
		add_action( 'wp_head', array( $this, 'print_snippet' ), 1 );
	}

	/**
	 * Whether the current request should be tracked.
	 *
	 * @return bool
	 */
	public static function should_track() {
		$settings = Privatus_Analytics_Settings::get();
		if ( '' === $settings['site_id'] ) {
			return false;
		}
		if ( is_admin() || is_preview() || is_customize_preview() || wp_doing_ajax() || is_feed() || is_robots() ) {
			return false;
		}
		if ( is_user_logged_in() && ! empty( $settings['exclude_roles'] ) ) {
			$user = wp_get_current_user();
			if ( array_intersect( (array) $user->roles, (array) $settings['exclude_roles'] ) ) {
				return false;
			}
		}
		/**
		 * Filters whether the Privatus Analytics snippet is printed for this request.
		 *
		 * @param bool $track Whether to track.
		 */
		return (bool) apply_filters( 'privatus_analytics_should_track', true );
	}

	/**
	 * Build the script tag attributes.
	 *
	 * @return array<string, string|bool>
	 */
	public static function script_attributes() {
		$settings = Privatus_Analytics_Settings::get();
		$attrs    = array(
			'src'       => Privatus_Analytics_Settings::script_host() . '/js/pa.js',
			'defer'     => true,
			'data-site' => $settings['site_id'],
		);
		if ( 'auto' !== $settings['spa'] ) {
			$attrs['data-spa'] = $settings['spa'];
		}
		if ( $settings['manual'] ) {
			$attrs['data-manual'] = 'true';
		}
		$modules = array_values( (array) $settings['modules'] );
		if ( array( 'engage' ) !== $modules ) {
			// An empty string is meaningful: it disables all modules.
			$attrs['data-modules'] = implode( ',', $modules );
		}
		$lists = array(
			'domains'    => 'data-domains',
			'paths_skip' => 'data-exclude',
			'params'     => 'data-params',
		);
		foreach ( $lists as $key => $attribute ) {
			if ( '' !== $settings[ $key ] ) {
				$attrs[ $attribute ] = $settings[ $key ];
			}
		}
		$masks = array_filter( array_merge( explode( ',', $settings['mask'] ), self::woocommerce_masks() ) );
		if ( $masks ) {
			$attrs['data-mask'] = implode( ',', array_unique( $masks ) );
		}
		if ( $settings['canonical'] ) {
			$attrs['data-canonical'] = 'true';
		}
		if ( $settings['hash'] ) {
			$attrs['data-hash'] = 'true';
		}

		/**
		 * Filters the attributes of the Privatus Analytics script tag.
		 *
		 * @param array $attrs Attribute name => value (true for boolean attributes).
		 */
		return (array) apply_filters( 'privatus_analytics_script_attributes', $attrs );
	}

	/**
	 * Mask WooCommerce URLs that contain order ids so they never reach analytics.
	 *
	 * @return string[]
	 */
	private static function woocommerce_masks() {
		if ( ! function_exists( 'wc_get_checkout_url' ) || ! function_exists( 'wc_get_page_permalink' ) ) {
			return array();
		}
		$masks = array();
		$pairs = array(
			array( wc_get_checkout_url(), 'order-received' ),
			array( wc_get_checkout_url(), 'order-pay' ),
			array( wc_get_page_permalink( 'myaccount' ), 'view-order' ),
		);
		foreach ( $pairs as $pair ) {
			$base = wp_parse_url( wc_get_endpoint_url( $pair[1], '', $pair[0] ), PHP_URL_PATH );
			if ( $base ) {
				$base    = trailingslashit( $base );
				$masks[] = $base . '*->' . $base;
			}
		}
		return $masks;
	}

	/**
	 * Print the queue stub and the script tag.
	 */
	public function print_snippet() {
		if ( ! self::should_track() ) {
			return;
		}
		wp_print_inline_script_tag( self::STUB, array( 'id' => 'privatus-analytics-stub' ) );
		wp_print_script_tag( self::script_attributes() );
	}
}
