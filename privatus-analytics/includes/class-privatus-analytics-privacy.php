<?php
/**
 * Suggested privacy policy text (Settings → Privacy → Policy guide).
 *
 * @package PrivatusAnalytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Privacy policy helper.
 */
final class Privatus_Analytics_Privacy {

	/**
	 * Hook into WordPress.
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'add_policy_content' ) );
	}

	/**
	 * Register the suggested policy text.
	 */
	public function add_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}
		$content  = '<h2>' . esc_html__( 'Analytics', 'privatus-analytics' ) . '</h2>';
		$content .= '<p class="privacy-policy-tutorial">' . esc_html__( 'Suggested text, added by the Privatus Analytics plugin. Adjust it to match your site.', 'privatus-analytics' ) . '</p>';
		$content .= '<p><strong class="privacy-policy-tutorial">' . esc_html__( 'Suggested text:', 'privatus-analytics' ) . ' </strong>';
		$content .= esc_html__( 'We use Privatus Analytics, a privacy-first analytics service, to count visits and understand which pages are popular. It does not use cookies, does not store anything on your device, and does not collect personal data. Your IP address is used only in memory, to derive an approximate country and to count unique visits. It is never stored. We record the page address, referrer, browser, operating system, device type, screen size bucket and country.', 'privatus-analytics' ) . '</p>';
		$content .= '<p>' . esc_html__( 'If our shop is enabled, we record anonymous purchase totals (amount, currency, number of items and whether a coupon was used). No order numbers, names, email or postal addresses are sent to Privatus Analytics.', 'privatus-analytics' ) . '</p>';
		$content .= '<p>' . esc_html__( 'You can opt out at any time with the opt-out button on this site. This stores a single "privatus_optout" flag in your browser\'s local storage and nothing else.', 'privatus-analytics' ) . '</p>';

		wp_add_privacy_policy_content( __( 'Privatus Analytics', 'privatus-analytics' ), wp_kses_post( $content ) );
	}
}
