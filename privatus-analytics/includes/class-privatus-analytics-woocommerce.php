<?php
/**
 * WooCommerce purchase tracking.
 *
 * @package PrivatusAnalytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends one `purchase` event per order from the browser, through the tracker.
 * Never sends order ids, emails, names, addresses or any other personal data.
 */
final class Privatus_Analytics_WooCommerce {

	const META_KEY = '_privatus_tracked';

	/**
	 * Hook into WordPress.
	 */
	public function register() {
		add_action( 'woocommerce_thankyou', array( $this, 'on_thankyou' ), 20 );
	}

	/**
	 * Handle the thank-you page for an order.
	 *
	 * @param int $order_id Order id (used only to load the order; never sent).
	 */
	public function on_thankyou( $order_id ) {
		$settings = Privatus_Analytics_Settings::get();
		if ( empty( $settings['woo_enabled'] ) || '' === $settings['site_id'] || ! function_exists( 'wc_get_order' ) ) {
			return;
		}
		$order = wc_get_order( absint( $order_id ) );
		if ( ! $order || $order->has_status( 'failed' ) || $order->get_meta( self::META_KEY ) ) {
			return;
		}
		if ( ! Privatus_Analytics_Tracker::should_track() ) {
			return;
		}

		if ( $this->print_js_event( $order ) ) {
			$order->update_meta_data( self::META_KEY, time() );
			$order->save();
		}
	}

	/**
	 * Anonymous purchase properties.
	 *
	 * @param WC_Order $order Order.
	 * @return array<string, mixed>
	 */
	public static function purchase_props( $order ) {
		return array(
			'revenue'     => round( (float) $order->get_total(), wc_get_price_decimals() ),
			'currency'    => (string) $order->get_currency(),
			'items'       => (int) $order->get_item_count(),
			'coupon_used' => count( $order->get_coupon_codes() ) > 0,
		);
	}

	/**
	 * Queue privatus.track('purchase', …). Respects the visitor opt-out and pa.js rules.
	 *
	 * @param WC_Order $order Order.
	 * @return bool
	 */
	private function print_js_event( $order ) {
		$props = self::purchase_props( $order );
		wp_print_inline_script_tag(
			Privatus_Analytics_Tracker::STUB . 'window.privatus.track("purchase",' . wp_json_encode( $props ) . ');',
			array( 'id' => 'privatus-analytics-purchase' )
		);
		return true;
	}
}
