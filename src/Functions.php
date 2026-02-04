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
	 * Format currency amount for display.
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents).
	 * @param string $currency Currency code.
	 * @param bool   $plain    Without symbol (default: false).
	 *
	 * @return string Formatted currency.
	 */
	function format_currency( $amount, string $currency, bool $plain = false ): string {
		$amount = (int) $amount;

		if ( $plain ) {
			return Currency::format_plain( $amount, $currency );
		}

		return Currency::format( $amount, $currency );
	}
}

if ( ! function_exists( 'format_currency_localized' ) ) {
	/**
	 * Format currency amount with locale-aware formatting.
	 *
	 * Handles symbol position, decimal/thousands separators according
	 * to the currency's locale conventions. Suitable for storefront display.
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents).
	 * @param string $currency Currency code.
	 * @param string $locale   Optional locale override.
	 *
	 * @return string Locale-formatted currency.
	 */
	function format_currency_localized( $amount, string $currency, string $locale = '' ): string {
		return Currency::format_localized( (int) $amount, $currency, $locale );
	}
}

if ( ! function_exists( 'format_price_interval' ) ) {
	/**
	 * Format price with recurring interval.
	 *
	 * @param mixed       $amount         Amount in cents.
	 * @param string      $currency       Currency code.
	 * @param string|null $interval       Recurring interval.
	 * @param int         $interval_count Interval count.
	 *
	 * @return string Formatted price with interval.
	 */
	function format_price_interval( $amount, string $currency, ?string $interval = null, int $interval_count = 1 ): string {
		return Currency::format_with_interval( (int) $amount, $currency, $interval, $interval_count );
	}
}

if ( ! function_exists( 'render_currency' ) ) {
	/**
	 * Render a price as formatted HTML with optional recurring interval.
	 *
	 * @param mixed       $value    Amount in smallest unit (e.g., cents).
	 * @param object|null $item     Data object (checked for currency, interval properties).
	 * @param string      $currency Optional currency code override.
	 *
	 * @return string|null HTML string or null if value is not numeric.
	 */
	function render_currency( $value, $item = null, string $currency = '' ): ?string {
		return Currency::render( $value, $item, $currency );
	}
}

if ( ! function_exists( 'to_currency_cents' ) ) {
	/**
	 * Convert decimal to smallest unit for Stripe.
	 *
	 * @param mixed  $amount   Decimal amount (e.g., 19.99).
	 * @param string $currency Currency code.
	 *
	 * @return int Amount in cents.
	 */
	function to_currency_cents( $amount, string $currency ): int {
		return Currency::to_smallest_unit( (float) $amount, $currency );
	}
}

if ( ! function_exists( 'from_currency_cents' ) ) {
	/**
	 * Convert from the smallest unit to decimal.
	 *
	 * @param mixed  $amount   Amount in the smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return float Decimal amount.
	 */
	function from_currency_cents( $amount, string $currency ): float {
		return Currency::from_smallest_unit( (int) $amount, $currency );
	}
}

if ( ! function_exists( 'get_currency_options' ) ) {
	/**
	 * Get all supported currencies as code => config pairs.
	 *
	 * @return array Array of currency configurations.
	 */
	function get_currency_options(): array {
		return Currency::all();
	}
}