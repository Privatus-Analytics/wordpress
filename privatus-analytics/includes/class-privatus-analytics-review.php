<?php
/**
 * A polite, one-time request for a review on WordPress.org.
 *
 * Shown only on Settings, Privatus Analytics, only to administrators, and only
 * once the plugin has been active for 14 days. "Don't show this again" hides it
 * for good. Nothing is shown anywhere else in the admin area.
 *
 * @package PrivatusAnalytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores when the plugin was first active and renders the review request.
 */
final class Privatus_Analytics_Review {

	const OPTION     = 'privatus_analytics_review';
	const ACTION     = 'privatus_analytics_dismiss_review';
	const DELAY      = 14 * DAY_IN_SECONDS;
	const REVIEW_URL = 'https://wordpress.org/support/plugin/privatus-analytics/reviews/#new-post';

	/**
	 * Hook into WordPress.
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'remember_start' ) );
		add_action( 'admin_post_' . self::ACTION, array( $this, 'dismiss' ) );
	}

	/**
	 * Saved state merged with defaults.
	 *
	 * @return array{since: int, dismissed: bool}
	 */
	public static function state() {
		$saved = get_option( self::OPTION, array() );
		$saved = is_array( $saved ) ? $saved : array();
		return array(
			'since'     => isset( $saved['since'] ) ? absint( $saved['since'] ) : 0,
			'dismissed' => ! empty( $saved['dismissed'] ),
		);
	}

	/**
	 * Record the first time the plugin runs in the admin area.
	 *
	 * Sites that update from an older version start counting from the update.
	 */
	public function remember_start() {
		if ( false === get_option( self::OPTION ) ) {
			add_option(
				self::OPTION,
				array(
					'since'     => time(),
					'dismissed' => false,
				),
				'',
				false
			);
		}
	}

	/**
	 * Whether the request should show for the current user right now.
	 *
	 * @return bool
	 */
	public static function should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$state = self::state();
		if ( $state['dismissed'] || 0 === $state['since'] ) {
			return false;
		}
		return ( time() - $state['since'] ) >= self::DELAY;
	}

	/**
	 * Print the request. Called from the settings page only.
	 */
	public static function render() {
		if ( ! self::should_show() ) {
			return;
		}
		$dismiss_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=' . self::ACTION ),
			self::ACTION
		);
		echo '<div class="notice notice-info inline privatus-analytics-review"><p>';
		esc_html_e( 'Thanks for using Privatus Analytics for two weeks! If you like it, a short review on WordPress.org helps other site owners find it. It only takes a minute.', 'privatus-analytics' );
		echo '</p><p>';
		printf(
			'<a class="button button-primary" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a> <a class="button-link" href="%3$s">%4$s</a>',
			esc_url( self::REVIEW_URL ),
			esc_html__( 'Leave a review', 'privatus-analytics' ),
			esc_url( $dismiss_url ),
			esc_html__( "Don't show this again", 'privatus-analytics' )
		);
		echo '</p></div>';
	}

	/**
	 * Hide the request for good, then go back to the settings page.
	 */
	public function dismiss() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'privatus-analytics' ), 403 );
		}
		check_admin_referer( self::ACTION );

		$state              = self::state();
		$state['dismissed'] = true;
		update_option( self::OPTION, $state, false );

		wp_safe_redirect( admin_url( 'options-general.php?page=' . Privatus_Analytics_Settings::PAGE ) );
		exit;
	}
}
