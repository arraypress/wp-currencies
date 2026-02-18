<?php
/**
 * Global Currency Helper Functions
 *
 * Provides convenient global functions for common currency operations.
 * These functions are wrappers around the ArrayPress\Currencies\Currency class.
 *
 * @package ArrayPress\Currencies
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

use ArrayPress\Currencies\Currency;

if ( ! function_exists( 'format_currency' ) ) {
	/**
	 * Format a currency amount with symbol.
	 *
	 * Pass a locale string to use locale-aware formatting suitable for storefront
	 * display. Omit locale for simple symbol-prefix formatting suited to admin contexts.
	 * Locale-aware formatting requires the PHP intl extension and falls back to
	 * simple formatting if unavailable.
	 *
	 * @param int    $amount   Amount in the smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 * @param string $locale   Optional locale override (e.g., 'de_DE').
	 *
	 * @return string Formatted amount with symbol (e.g., "$19.99").
	 * @since 1.0.0
	 */
	function format_currency( int $amount, string $currency, string $locale = '' ): string {
		if ( ! empty( $locale ) ) {
			return Currency::format_localized( $amount, $currency, $locale );
		}

		return Currency::format( $amount, $currency );
	}
}

if ( ! function_exists( 'format_currency_plain' ) ) {
	/**
	 * Format a currency amount without symbol.
	 *
	 * @param int    $amount   Amount in the smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 *
	 * @return string Formatted amount without symbol (e.g., "19.99").
	 * @since 1.0.0
	 */
	function format_currency_plain( int $amount, string $currency ): string {
		return Currency::format_plain( $amount, $currency );
	}
}

if ( ! function_exists( 'esc_currency_e' ) ) {
	/**
	 * Echo an escaped, formatted currency amount.
	 *
	 * @param int    $amount   Amount in the smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function esc_currency_e( int $amount, string $currency ): void {
		echo esc_html( Currency::format( $amount, $currency ) );
	}
}

if ( ! function_exists( 'render_currency' ) ) {
	/**
	 * Render a formatted currency amount as HTML.
	 *
	 * @param int    $amount   Amount in the smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 *
	 * @return string HTML span with formatted amount (e.g., '<span class="price">$19.99</span>').
	 * @since 1.0.0
	 */
	function render_currency( int $amount, string $currency ): string {
		return Currency::render( $amount, $currency );
	}
}

if ( ! function_exists( 'to_currency_cents' ) ) {
	/**
	 * Convert a decimal amount to the smallest unit for Stripe.
	 *
	 * @param float  $amount   Decimal amount (e.g., 19.99).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 *
	 * @return int Amount in the smallest unit (e.g., 1999).
	 * @since 1.0.0
	 */
	function to_currency_cents( float $amount, string $currency ): int {
		return Currency::to_smallest_unit( $amount, $currency );
	}
}

if ( ! function_exists( 'from_currency_cents' ) ) {
	/**
	 * Convert from the smallest unit to a decimal amount.
	 *
	 * @param int    $amount   Amount in the smallest unit (e.g., 1999).
	 * @param string $currency Currency code (e.g., 'USD', 'GBP').
	 *
	 * @return float Decimal amount (e.g., 19.99).
	 * @since 1.0.0
	 */
	function from_currency_cents( int $amount, string $currency ): float {
		return Currency::from_smallest_unit( $amount, $currency );
	}
}

if ( ! function_exists( 'get_currency_options' ) ) {
	/**
	 * Get all supported currencies as an array of configurations.
	 *
	 * @return array Currency configurations keyed by currency code.
	 * @since 1.0.0
	 */
	function get_currency_options(): array {
		return Currency::all();
	}
}