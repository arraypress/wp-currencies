<?php
/**
 * Global Currency Helper Functions
 *
 * @package ArrayPress\Currencies
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

use ArrayPress\Currencies\Currency;

if ( ! function_exists( 'format_currency' ) ) {
	/**
	 * Format currency amount for display
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents)
	 * @param string $currency Currency code
	 * @param bool   $plain    Without symbol (default: false)
	 *
	 * @return string Formatted currency
	 */
	function format_currency( $amount, string $currency, bool $plain = false ): string {
		$amount = (int) $amount;

		if ( $plain ) {
			return Currency::format_plain( $amount, $currency );
		}

		return Currency::format( $amount, $currency );
	}
}

if ( ! function_exists( 'format_price_interval' ) ) {
	/**
	 * Format price with recurring interval
	 *
	 * @param mixed       $amount         Amount in cents
	 * @param string      $currency       Currency code
	 * @param string|null $interval       Recurring interval
	 * @param int         $interval_count Interval count
	 *
	 * @return string Formatted price with interval
	 */
	function format_price_interval( $amount, string $currency, ?string $interval = null, int $interval_count = 1 ): string {
		return Currency::format_with_interval( (int) $amount, $currency, $interval, $interval_count );
	}
}

if ( ! function_exists( 'esc_currency' ) ) {
	/**
	 * Escape and format currency for safe output
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents)
	 * @param string $currency Currency code
	 * @param bool   $plain    Without symbol (default: false)
	 *
	 * @return string Escaped formatted currency
	 */
	function esc_currency( $amount, string $currency, bool $plain = false ): string {
		return esc_html( format_currency( $amount, $currency, $plain ) );
	}
}

if ( ! function_exists( 'esc_currency_e' ) ) {
	/**
	 * Escape, format and echo currency
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents)
	 * @param string $currency Currency code
	 * @param bool   $plain    Without symbol (default: false)
	 *
	 * @return void
	 */
	function esc_currency_e( $amount, string $currency, bool $plain = false ): void {
		echo esc_currency( $amount, $currency, $plain );
	}
}

if ( ! function_exists( 'get_currency_options' ) ) {
	/**
	 * Get currency options for select fields
	 *
	 * @return array Options array with value/label pairs
	 */
	function get_currency_options(): array {
		return Currency::get_options();
	}
}

if ( ! function_exists( 'to_currency_cents' ) ) {
	/**
	 * Convert decimal to smallest unit for Stripe
	 *
	 * @param mixed  $amount   Decimal amount (e.g., 19.99)
	 * @param string $currency Currency code
	 *
	 * @return int Amount in cents
	 *
	 * @example to_currency_cents( 19.99, 'USD' ) returns 1999
	 * @example to_currency_cents( 100, 'JPY' ) returns 100 (zero decimal)
	 * @example to_currency_cents( 5.975, 'KWD' ) returns 5975 (3 decimals)
	 */
	function to_currency_cents( $amount, string $currency ): int {
		return Currency::to_smallest_unit( (float) $amount, $currency );
	}
}

if ( ! function_exists( 'from_currency_cents' ) ) {
	/**
	 * Convert from the smallest unit to decimal
	 *
	 * @param mixed  $amount   Amount in the smallest unit
	 * @param string $currency Currency code
	 *
	 * @return float Decimal amount
	 *
	 * @example from_currency_cents( 1999, 'USD' ) returns 19.99
	 * @example from_currency_cents( 100, 'JPY' ) returns 100.0
	 * @example from_currency_cents( 5975, 'KWD' ) returns 5.975
	 */
	function from_currency_cents( $amount, string $currency ): float {
		return Currency::from_smallest_unit( (int) $amount, $currency );
	}
}