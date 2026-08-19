<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OpaSuite_Injector {

	private $option_key;

	public function __construct( $option_key ) {
		$this->option_key = $option_key;
	}

	/**
	 * Main entry point for script injection into wp_head
	 */
	public function inject_script() {
		if ( ! $this->should_inject() ) {
			return;
		}

		$options = OpaSuite_Header_Script::get_options();
		$code    = $this->generate_script( $options );

		if ( ! empty( $code ) ) {
			echo "\n<!-- OpaSuite Header Script -->\n";
			echo "<script type=\"text/javascript\">\n";
			echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "\n</script>\n";
			echo "<!-- End OpaSuite Header Script -->\n\n";
		}
	}

	/**
	 * Check if script should be injected on current page
	 */
	public function should_inject() {
		// Don't inject in admin area or feed or REST
		if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		$options = OpaSuite_Header_Script::get_options();

		// Check if enabled master toggle
		if ( empty( $options['enabled'] ) || '1' !== (string) $options['enabled'] ) {
			return false;
		}

		// Ensure domain and token are present
		if ( empty( $options['domain'] ) || empty( $options['token'] ) ) {
			return false;
		}

		$mode           = isset( $options['pages_mode'] ) ? $options['pages_mode'] : 'all';
		$selected_pages = isset( $options['selected_pages'] ) && is_array( $options['selected_pages'] )
			? array_map( 'absint', $options['selected_pages'] )
			: array();

		switch ( $mode ) {
			case 'front_page':
				return is_front_page() || is_home();

			case 'selected':
				$current_id = get_queried_object_id();
				if ( ! $current_id ) {
					return false;
				}
				return in_array( (int) $current_id, $selected_pages, true );

			case 'except':
				$current_id = get_queried_object_id();
				if ( ! $current_id ) {
					return true;
				}
				return ! in_array( (int) $current_id, $selected_pages, true );

			case 'all':
			default:
				return true;
		}
	}

	/**
	 * Generate the JavaScript snippet code string
	 */
	public static function generate_script( $options ) {
		$domain                 = isset( $options['domain'] ) ? trim( $options['domain'] ) : '';
		$token                  = isset( $options['token'] ) ? trim( $options['token'] ) : '';
		$permitir_login_anonimo = isset( $options['permitir_login_anonimo'] ) ? $options['permitir_login_anonimo'] : 'on';
		$facebook_appid         = isset( $options['facebook_appid'] ) ? $options['facebook_appid'] : '';
		$google_credential      = isset( $options['google_credential'] ) ? $options['google_credential'] : '';
		$google_oauth           = isset( $options['google_oauth'] ) ? $options['google_oauth'] : '';

		$config_data = array(
			'permitir_login_anonimo' => $permitir_login_anonimo,
			'facebook_appid'        => $facebook_appid,
			'google_credential'     => $google_credential,
			'google_oauth'          => $google_oauth,
		);

		$json_str = wp_json_encode( $config_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		// Format output script matching exact user structure
		$script = "(function(
  i, s, g, r, j, y, b, p, t, z, a
) {
  a = s.createElement(r);
  a.async = true;
  a.src = g.concat(
    b, j,
    b, y,
    p, j
  );
  s.head.appendChild(a);
  a.onload = function() {
    opa.init(g, t, z);
  };
})(
  window,
  document,
  '{$domain}',
  'script',
  'js',
  'opa',
  '/',
  '.',
  '{$token}',
  `{$json_str}`
);";

		return $script;
	}
}
