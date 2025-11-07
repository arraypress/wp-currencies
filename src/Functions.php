<?php
/**
 * Global Currency Helper Functions
 *
 * Provides convenient global functions for currency formatting and conversion.
 * These functions are wrappers around the ArrayPress\Currencies\Currency class.
 *
 * Functions included:
 * - format_currency() - Format amounts for display
 * - to_currency_cents() - Convert decimal to the smallest unit
 * - from_currency_cents() - Convert from the smallest unit to decimal
 *
 * @package ArrayPress\Currencies
 * @since   1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use ArrayPress\Currencies\Currency;

if ( ! function_exists( 'format_currency' ) ) {
	/**
	 * Format currency amount for display
	 *
	 * @param mixed  $amount   Amount in the smallest unit (cents)
	 * @param string $currency Currency code (e.g., 'USD', 'EUR')
	 * @param bool   $plain    Return without symbol (default: false)
	 *
	 * @return string Formatted currency
	 * @since 1.0.0
	 */
	function format_currency( $amount, string $currency, bool $plain = false ): string {
		// Ensure integer
		$amount = (int) $amount;

		if ( $plain ) {
			return Currency::format_plain( $amount, $currency );
		}

		return Currency::format( $amount, $currency );
	}
}

if ( ! function_exists( 'to_currency_cents' ) ) {
	/**
	 * Convert decimal amount to the smallest unit for Stripe
	 *
	 * @param mixed  $amount   Decimal amount (e.g., 19.99)
	 * @param string $currency Currency code
	 *
	 * @return int Amount in the smallest unit (cents)
	 * @since 1.0.0
	 */
	function to_currency_cents( $amount, string $currency ): int {
		return Currency::to_smallest_unit( (float) $amount, $currency );
	}
}

if ( ! function_exists( 'from_currency_cents' ) ) {
	/**
	 * Convert from the smallest unit to decimal amount
	 *
	 * @param mixed  $amount   Amount in the smallest unit
	 * @param string $currency Currency code
	 *
	 * @return float Decimal amount
	 * @since 1.0.0
	 */
	function from_currency_cents( $amount, string $currency ): float {
		return Currency::from_smallest_unit( (int) $amount, $currency );
	}
}

if ( ! function_exists( 'sanitize_currency' ) ) {
	/**
	 * Sanitize any currency input to cents
	 *
	 * @param mixed  $amount   Amount in any format
	 * @param string $currency Currency code
	 *
	 * @return int Amount in cents
	 * @since 1.0.0
	 */
	function sanitize_currency( $amount, string $currency = 'USD' ): int {
		return Currency::sanitize_to_cents( $amount, $currency );
	}
}

if ( ! function_exists( 'sanitize_to_decimal' ) ) {
	/**
	 * Sanitize and convert cents to decimal for display
	 *
	 * @param mixed  $amount   Amount in smallest unit
	 * @param string $currency Currency code
	 *
	 * @return float Decimal amount (e.g., 299.00)
	 * @since 1.0.0
	 */
	function sanitize_to_decimal( $amount, string $currency = 'USD' ): float {
		return Currency::sanitize_to_decimal( $amount, $currency );
	}
}