<?php
/**
 * WS Form submission tracking.
 *
 * @package PrivatusAnalytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends one `WS Form Submit` event from the browser each time a WS Form form is sent.
 * Uses the WS Form JavaScript events. Never sends what the visitor typed, only the form id and the form name.
 */
final class Privatus_Analytics_WS_Form {

	const HANDLE = 'privatus-analytics-ws-form';
	const EVENT  = 'WS Form Submit';

	/**
	 * Hook into WordPress.
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20 );
	}

	/**
	 * Whether WS Form (LITE or PRO) is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return defined( 'WS_FORM_VERSION' );
	}

	/**
	 * The listener script. WS Form fires wsf-submit-success after an AJAX submit worked,
	 * and wsf-submit-complete when a form posts the normal way. Only one of them fires per submit.
	 *
	 * @return string
	 */
	public static function script() {
		return Privatus_Analytics_Tracker::STUB
			. '(function($){if(!$){return;}'
			. '$(document).on("wsf-submit-success wsf-submit-complete",function(e,form,formId){'
			. 'var p={form_id:parseInt(formId,10)||0};'
			. 'if(form&&typeof form.label==="string"&&form.label!==""){p.form=form.label.slice(0,200);}'
			. 'window.privatus.track(' . wp_json_encode( self::EVENT ) . ',p);'
			. '});})(window.jQuery);';
	}

	/**
	 * Print the listener after jQuery on front-end pages, when the setting is on and the request is tracked.
	 */
	public function enqueue() {
		$settings = Privatus_Analytics_Settings::get();
		if ( empty( $settings['wsform_enabled'] ) || ! self::is_active() || ! Privatus_Analytics_Tracker::should_track() ) {
			return;
		}
		wp_register_script( self::HANDLE, false, array( 'jquery' ), PRIVATUS_ANALYTICS_VERSION, true );
		wp_enqueue_script( self::HANDLE );
		wp_add_inline_script( self::HANDLE, self::script() );
	}
}
