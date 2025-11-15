<?php
/**
 * WordPress Currencies Library for Stripe
 *
 * A comprehensive currency formatting and conversion library for Stripe payments in WordPress.
 *
 * @package     ArrayPress\Currencies
 * @copyright   Copyright (c) 2025, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

namespace ArrayPress\Currencies;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Currency class for Stripe payment processing
 *
 * Handles currency formatting, conversion, and validation for all
 * Stripe-supported currencies with proper decimal handling.
 *
 * @since 1.0.0
 */
class Currency {

	/**
	 * Stripe supported currencies with configuration
	 *
	 * Complete list as of 2025 - 135 currencies
	 * @link  https://stripe.com/docs/currencies
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private const CURRENCIES = [
		// Major Currencies
		'USD' => [ 'symbol' => '$', 'decimals' => 2 ],        // US Dollar
		'EUR' => [ 'symbol' => '€', 'decimals' => 2 ],        // Euro
		'GBP' => [ 'symbol' => '£', 'decimals' => 2 ],        // British Pound
		'JPY' => [ 'symbol' => '¥', 'decimals' => 0 ],        // Japanese Yen - ZERO DECIMAL
		'CNY' => [ 'symbol' => '¥', 'decimals' => 2 ],        // Chinese Yuan

		// Americas
		'CAD' => [ 'symbol' => 'C$', 'decimals' => 2 ],       // Canadian Dollar
		'MXN' => [ 'symbol' => '$', 'decimals' => 2 ],        // Mexican Peso
		'BRL' => [ 'symbol' => 'R$', 'decimals' => 2 ],       // Brazilian Real
		'ARS' => [ 'symbol' => '$', 'decimals' => 2 ],        // Argentine Peso
		'COP' => [ 'symbol' => '$', 'decimals' => 2 ],        // Colombian Peso
		'PEN' => [ 'symbol' => 'S/', 'decimals' => 2 ],       // Peruvian Sol
		'CLP' => [ 'symbol' => '$', 'decimals' => 0 ],        // Chilean Peso - ZERO DECIMAL
		'UYU' => [ 'symbol' => '$U', 'decimals' => 2 ],       // Uruguayan Peso
		'PYG' => [ 'symbol' => '₲', 'decimals' => 0 ],        // Paraguayan Guarani - ZERO DECIMAL
		'BOB' => [ 'symbol' => 'Bs', 'decimals' => 2 ],       // Bolivian Boliviano
		'CRC' => [ 'symbol' => '₡', 'decimals' => 2 ],        // Costa Rican Colón
		'DOP' => [ 'symbol' => 'RD$', 'decimals' => 2 ],      // Dominican Peso
		'GTQ' => [ 'symbol' => 'Q', 'decimals' => 2 ],        // Guatemalan Quetzal
		'HNL' => [ 'symbol' => 'L', 'decimals' => 2 ],        // Honduran Lempira
		'NIO' => [ 'symbol' => 'C$', 'decimals' => 2 ],       // Nicaraguan Córdoba
		'PAB' => [ 'symbol' => 'B/', 'decimals' => 2 ],       // Panamanian Balboa

		// Europe (Non-Euro)
		'CHF' => [ 'symbol' => 'CHF', 'decimals' => 2 ],      // Swiss Franc
		'SEK' => [ 'symbol' => 'kr', 'decimals' => 2 ],       // Swedish Krona
		'DKK' => [ 'symbol' => 'kr', 'decimals' => 2 ],       // Danish Krone
		'NOK' => [ 'symbol' => 'kr', 'decimals' => 2 ],       // Norwegian Krone
		'ISK' => [ 'symbol' => 'kr', 'decimals' => 0 ],       // Icelandic Króna - ZERO DECIMAL
		'PLN' => [ 'symbol' => 'zł', 'decimals' => 2 ],       // Polish Złoty
		'CZK' => [ 'symbol' => 'Kč', 'decimals' => 2 ],       // Czech Koruna
		'HUF' => [ 'symbol' => 'Ft', 'decimals' => 0 ],       // Hungarian Forint - ZERO DECIMAL
		'RON' => [ 'symbol' => 'lei', 'decimals' => 2 ],      // Romanian Leu
		'BGN' => [ 'symbol' => 'лв', 'decimals' => 2 ],       // Bulgarian Lev
		'HRK' => [ 'symbol' => 'kn', 'decimals' => 2 ],       // Croatian Kuna
		'RSD' => [ 'symbol' => 'din', 'decimals' => 2 ],      // Serbian Dinar
		'MKD' => [ 'symbol' => 'ден', 'decimals' => 2 ],      // Macedonian Denar
		'MDL' => [ 'symbol' => 'L', 'decimals' => 2 ],        // Moldovan Leu
		'UAH' => [ 'symbol' => '₴', 'decimals' => 2 ],        // Ukrainian Hryvnia
		'GEL' => [ 'symbol' => '₾', 'decimals' => 2 ],        // Georgian Lari
		'ALL' => [ 'symbol' => 'L', 'decimals' => 2 ],        // Albanian Lek
		'BAM' => [ 'symbol' => 'KM', 'decimals' => 2 ],       // Bosnia-Herzegovina Mark

		// Asia-Pacific
		'HKD' => [ 'symbol' => 'HK$', 'decimals' => 2 ],      // Hong Kong Dollar
		'TWD' => [ 'symbol' => 'NT$', 'decimals' => 0 ],      // Taiwan Dollar - ZERO DECIMAL
		'KRW' => [ 'symbol' => '₩', 'decimals' => 0 ],        // South Korean Won - ZERO DECIMAL
		'SGD' => [ 'symbol' => 'S$', 'decimals' => 2 ],       // Singapore Dollar
		'THB' => [ 'symbol' => '฿', 'decimals' => 2 ],        // Thai Baht
		'MYR' => [ 'symbol' => 'RM', 'decimals' => 2 ],       // Malaysian Ringgit
		'PHP' => [ 'symbol' => '₱', 'decimals' => 2 ],        // Philippine Peso
		'IDR' => [ 'symbol' => 'Rp', 'decimals' => 2 ],       // Indonesian Rupiah
		'VND' => [ 'symbol' => '₫', 'decimals' => 0 ],        // Vietnamese Dong - ZERO DECIMAL
		'INR' => [ 'symbol' => '₹', 'decimals' => 2 ],        // Indian Rupee
		'PKR' => [ 'symbol' => '₨', 'decimals' => 2 ],       // Pakistani Rupee
		'BDT' => [ 'symbol' => '৳', 'decimals' => 2 ],        // Bangladeshi Taka
		'LKR' => [ 'symbol' => 'Rs', 'decimals' => 2 ],       // Sri Lankan Rupee
		'NPR' => [ 'symbol' => '₨', 'decimals' => 2 ],       // Nepalese Rupee
		'MMK' => [ 'symbol' => 'K', 'decimals' => 2 ],        // Myanmar Kyat
		'KHR' => [ 'symbol' => '៛', 'decimals' => 2 ],        // Cambodian Riel
		'LAK' => [ 'symbol' => '₭', 'decimals' => 2 ],        // Lao Kip
		'MNT' => [ 'symbol' => '₮', 'decimals' => 2 ],        // Mongolian Tugrik
		'BND' => [ 'symbol' => '$', 'decimals' => 2 ],        // Brunei Dollar
		'PGK' => [ 'symbol' => 'K', 'decimals' => 2 ],        // Papua New Guinea Kina
		'FJD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Fijian Dollar
		'SBD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Solomon Islands Dollar
		'TOP' => [ 'symbol' => 'T$', 'decimals' => 2 ],       // Tongan Paʻanga
		'VUV' => [ 'symbol' => 'VT', 'decimals' => 0 ],       // Vanuatu Vatu - ZERO DECIMAL
		'WST' => [ 'symbol' => 'WS$', 'decimals' => 2 ],      // Samoan Tala
		'MVR' => [ 'symbol' => 'Rf', 'decimals' => 2 ],       // Maldivian Rufiyaa

		// Oceania
		'AUD' => [ 'symbol' => 'A$', 'decimals' => 2 ],       // Australian Dollar
		'NZD' => [ 'symbol' => 'NZ$', 'decimals' => 2 ],      // New Zealand Dollar

		// Middle East
		'AED' => [ 'symbol' => 'د.إ', 'decimals' => 2 ],      // UAE Dirham
		'SAR' => [ 'symbol' => 'SR', 'decimals' => 2 ],       // Saudi Riyal
		'QAR' => [ 'symbol' => 'QR', 'decimals' => 2 ],       // Qatari Riyal
		'OMR' => [ 'symbol' => 'ر.ع.', 'decimals' => 3 ],     // Omani Rial - THREE DECIMAL
		'KWD' => [ 'symbol' => 'KD', 'decimals' => 3 ],       // Kuwaiti Dinar - THREE DECIMAL
		'BHD' => [ 'symbol' => 'BD', 'decimals' => 3 ],       // Bahraini Dinar - THREE DECIMAL
		'JOD' => [ 'symbol' => 'JD', 'decimals' => 3 ],       // Jordanian Dinar - THREE DECIMAL
		'ILS' => [ 'symbol' => '₪', 'decimals' => 2 ],        // Israeli Shekel
		'TRY' => [ 'symbol' => '₺', 'decimals' => 2 ],        // Turkish Lira
		'LBP' => [ 'symbol' => 'ل.ل', 'decimals' => 2 ],      // Lebanese Pound

		// Africa
		'ZAR' => [ 'symbol' => 'R', 'decimals' => 2 ],        // South African Rand
		'EGP' => [ 'symbol' => 'E£', 'decimals' => 2 ],       // Egyptian Pound
		'NGN' => [ 'symbol' => '₦', 'decimals' => 2 ],        // Nigerian Naira
		'KES' => [ 'symbol' => 'KSh', 'decimals' => 2 ],      // Kenyan Shilling
		'GHS' => [ 'symbol' => '₵', 'decimals' => 2 ],        // Ghanaian Cedi
		'MAD' => [ 'symbol' => 'MAD', 'decimals' => 2 ],      // Moroccan Dirham
		'TND' => [ 'symbol' => 'DT', 'decimals' => 3 ],       // Tunisian Dinar - THREE DECIMAL
		'DZD' => [ 'symbol' => 'DA', 'decimals' => 2 ],       // Algerian Dinar
		'ETB' => [ 'symbol' => 'Br', 'decimals' => 2 ],       // Ethiopian Birr
		'UGX' => [ 'symbol' => 'USh', 'decimals' => 0 ],      // Ugandan Shilling - ZERO DECIMAL
		'TZS' => [ 'symbol' => 'TSh', 'decimals' => 2 ],      // Tanzanian Shilling
		'RWF' => [ 'symbol' => 'FRw', 'decimals' => 0 ],      // Rwandan Franc - ZERO DECIMAL
		'MUR' => [ 'symbol' => '₨', 'decimals' => 2 ],       // Mauritian Rupee
		'SCR' => [ 'symbol' => '₨', 'decimals' => 2 ],       // Seychellois Rupee
		'MZN' => [ 'symbol' => 'MT', 'decimals' => 2 ],       // Mozambican Metical
		'ZMW' => [ 'symbol' => 'ZK', 'decimals' => 2 ],       // Zambian Kwacha
		'BWP' => [ 'symbol' => 'P', 'decimals' => 2 ],        // Botswanan Pula
		'NAD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Namibian Dollar
		'SZL' => [ 'symbol' => 'L', 'decimals' => 2 ],        // Swazi Lilangeni
		'LSL' => [ 'symbol' => 'L', 'decimals' => 2 ],        // Lesotho Loti
		'MWK' => [ 'symbol' => 'MK', 'decimals' => 2 ],       // Malawian Kwacha
		'AOA' => [ 'symbol' => 'Kz', 'decimals' => 2 ],       // Angolan Kwanza
		'BIF' => [ 'symbol' => 'FBu', 'decimals' => 0 ],      // Burundian Franc - ZERO DECIMAL
		'DJF' => [ 'symbol' => 'Fdj', 'decimals' => 0 ],      // Djiboutian Franc - ZERO DECIMAL
		'GNF' => [ 'symbol' => 'FG', 'decimals' => 0 ],       // Guinean Franc - ZERO DECIMAL
		'KMF' => [ 'symbol' => 'CF', 'decimals' => 0 ],       // Comorian Franc - ZERO DECIMAL
		'CDF' => [ 'symbol' => 'FC', 'decimals' => 2 ],       // Congolese Franc
		'MGA' => [ 'symbol' => 'Ar', 'decimals' => 0 ],       // Malagasy Ariary - ZERO DECIMAL
		'XAF' => [ 'symbol' => 'FCFA', 'decimals' => 0 ],     // Central African CFA Franc - ZERO DECIMAL
		'XOF' => [ 'symbol' => 'CFA', 'decimals' => 0 ],      // West African CFA Franc - ZERO DECIMAL

		// Caribbean
		'JMD' => [ 'symbol' => 'J$', 'decimals' => 2 ],       // Jamaican Dollar
		'TTD' => [ 'symbol' => 'TT$', 'decimals' => 2 ],      // Trinidad and Tobago Dollar
		'BBD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Barbadian Dollar
		'BSD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Bahamian Dollar
		'BZD' => [ 'symbol' => 'BZ$', 'decimals' => 2 ],      // Belize Dollar
		'BMD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Bermudian Dollar
		'KYD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Cayman Islands Dollar
		'XCD' => [ 'symbol' => '$', 'decimals' => 2 ],        // East Caribbean Dollar
		'AWG' => [ 'symbol' => 'ƒ', 'decimals' => 2 ],        // Aruban Florin
		'ANG' => [ 'symbol' => 'ƒ', 'decimals' => 2 ],        // Netherlands Antillean Guilder
		'HTG' => [ 'symbol' => 'G', 'decimals' => 2 ],        // Haitian Gourde

		// Former Soviet States
		'RUB' => [ 'symbol' => '₽', 'decimals' => 2 ],        // Russian Ruble
		'KZT' => [ 'symbol' => '₸', 'decimals' => 2 ],        // Kazakhstani Tenge
		'UZS' => [ 'symbol' => 'лв', 'decimals' => 2 ],       // Uzbekistani Som
		'AZN' => [ 'symbol' => '₼', 'decimals' => 2 ],        // Azerbaijani Manat
		'AMD' => [ 'symbol' => '֏', 'decimals' => 2 ],        // Armenian Dram
		'KGS' => [ 'symbol' => 'лв', 'decimals' => 2 ],       // Kyrgyzstani Som
		'TJS' => [ 'symbol' => 'SM', 'decimals' => 2 ],       // Tajikistani Somoni
		'TMT' => [ 'symbol' => 'T', 'decimals' => 2 ],        // Turkmenistani Manat

		// Other
		'AFN' => [ 'symbol' => '؋', 'decimals' => 2 ],        // Afghan Afghani
		'XPF' => [ 'symbol' => '₣', 'decimals' => 0 ],        // CFP Franc - ZERO DECIMAL
		'CVE' => [ 'symbol' => '$', 'decimals' => 2 ],        // Cape Verdean Escudo
		'GIP' => [ 'symbol' => '£', 'decimals' => 2 ],        // Gibraltar Pound
		'GMD' => [ 'symbol' => 'D', 'decimals' => 2 ],        // Gambian Dalasi
		'GYD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Guyanese Dollar
		'LRD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Liberian Dollar
		'SLL' => [ 'symbol' => 'Le', 'decimals' => 2 ],       // Sierra Leonean Leone
		'SOS' => [ 'symbol' => 'S', 'decimals' => 2 ],        // Somali Shilling
		'SRD' => [ 'symbol' => '$', 'decimals' => 2 ],        // Surinamese Dollar
		'STD' => [ 'symbol' => 'Db', 'decimals' => 2 ],       // São Tomé and Príncipe Dobra
	];

	/**
	 * Format amount for display
	 *
	 * @param int    $amount   Amount in smallest unit (cents, pence, etc)
	 * @param string $currency Currency code (3-letter ISO)
	 *
	 * @return string Formatted amount with symbol
	 * @since 1.0.0
	 *
	 */
	public static function format( int $amount, string $currency ): string {
		$currency = strtoupper( $currency );
		$config   = self::get_config( $currency );

		if ( ! $config ) {
			return (string) $amount;
		}

		// Convert from the smallest unit
		if ( $config['decimals'] === 0 ) {
			$formatted = number_format( $amount );
		} else {
			$divisor   = pow( 10, $config['decimals'] );
			$formatted = number_format( $amount / $divisor, $config['decimals'] );
		}

		return $config['symbol'] . $formatted;
	}

	/**
	 * Format amount without symbol
	 *
	 * @param int    $amount   Amount in the smallest unit
	 * @param string $currency Currency code
	 *
	 * @return string Formatted amount without symbol
	 * @since 1.0.0
	 *
	 */
	public static function format_plain( int $amount, string $currency ): string {
		$currency = strtoupper( $currency );
		$config   = self::get_config( $currency );

		if ( ! $config ) {
			return (string) $amount;
		}

		if ( $config['decimals'] === 0 ) {
			return number_format( $amount );
		}

		$divisor = pow( 10, $config['decimals'] );

		return number_format( $amount / $divisor, $config['decimals'] );
	}

	/**
	 * Format with currency code instead of symbol
	 *
	 * @param int    $amount   Amount in the smallest unit
	 * @param string $currency Currency code
	 *
	 * @return string Amount with currency code (e.g., "99.99 USD")
	 * @since 1.0.0
	 *
	 */
	public static function format_with_code( int $amount, string $currency ): string {
		return self::format_plain( $amount, $currency ) . ' ' . strtoupper( $currency );
	}

	/**
	 * Convert decimal amount to the smallest unit for Stripe
	 *
	 * @param float  $amount   Decimal amount (e.g., 19.99)
	 * @param string $currency Currency code
	 *
	 * @return int Amount in the smallest unit
	 * @since 1.0.0
	 *
	 */
	public static function to_smallest_unit( float $amount, string $currency ): int {
		$config   = self::get_config( $currency );
		$decimals = $config['decimals'] ?? 2;

		$multiplier = pow( 10, $decimals );

		return (int) round( $amount * $multiplier );
	}

	/**
	 * Convert from the smallest unit to decimal amount
	 *
	 * @param int    $amount   Amount in the smallest unit
	 * @param string $currency Currency code
	 *
	 * @return float Decimal amount
	 * @since 1.0.0
	 *
	 */
	public static function from_smallest_unit( int $amount, string $currency ): float {
		$config   = self::get_config( $currency );
		$decimals = $config['decimals'] ?? 2;

		if ( $decimals === 0 ) {
			return (float) $amount;
		}

		$divisor = pow( 10, $decimals );

		return $amount / $divisor;
	}

	/**
	 * Get currency configuration
	 *
	 * @param string $currency Currency code
	 *
	 * @return array|null Configuration array or null
	 * @since 1.0.0
	 *
	 */
	public static function get_config( string $currency ): ?array {
		return self::CURRENCIES[ strtoupper( $currency ) ] ?? null;
	}

	/**
	 * Get currency symbol
	 *
	 * @param string $currency Currency code
	 *
	 * @return string Symbol or currency code if not found
	 * @since 1.0.0
	 *
	 */
	public static function get_symbol( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['symbol'] ?? strtoupper( $currency );
	}

	/**
	 * Get decimal places for currency
	 *
	 * @param string $currency Currency code
	 *
	 * @return int Number of decimal places
	 * @since 1.0.0
	 *
	 */
	public static function get_decimals( string $currency ): int {
		$config = self::get_config( $currency );

		return $config['decimals'] ?? 2;
	}

	/**
	 * Check if currency is supported
	 *
	 * @param string $currency Currency code
	 *
	 * @return bool True if supported
	 * @since 1.0.0
	 *
	 */
	public static function is_supported( string $currency ): bool {
		return isset( self::CURRENCIES[ strtoupper( $currency ) ] );
	}

	/**
	 * Check if currency is zero-decimal
	 *
	 * @param string $currency Currency code
	 *
	 * @return bool True if zero-decimal currency
	 * @since 1.0.0
	 *
	 */
	public static function is_zero_decimal( string $currency ): bool {
		$config = self::get_config( $currency );

		return $config && $config['decimals'] === 0;
	}

	/**
	 * Get all supported currencies
	 *
	 * @return array All currency configurations
	 * @since 1.0.0
	 *
	 */
	public static function get_all(): array {
		return self::CURRENCIES;
	}

	/**
	 * Get currencies for select options
	 *
	 * @return array Gutenberg-compatible options array
	 * @since 1.0.0
	 *
	 */
	public static function get_options(): array {
		$options = [];

		foreach ( self::CURRENCIES as $code => $config ) {
			$options[] = [
				'value' => $code,
				'label' => sprintf( '%s - %s', $code, $config['symbol'] ),
			];
		}

		return $options;
	}

	/**
	 * Validate and sanitize currency code
	 *
	 * @param string $currency Currency code to validate
	 *
	 * @return string|null Sanitized code or null if invalid
	 * @since 1.0.0
	 *
	 */
	public static function sanitize( string $currency ): ?string {
		$currency = strtoupper( trim( $currency ) );

		return self::is_supported( $currency ) ? $currency : null;
	}

	/**
	 * Sanitize and convert any currency input to the smallest unit
	 * Handles both decimal (299.00) and cent (29900) inputs
	 *
	 * @param mixed  $amount   Amount in any format
	 * @param string $currency Currency code
	 *
	 * @return int Amount in the smallest unit (cents)
	 * @since 1.0.0
	 */
	public static function sanitize_to_cents( $amount, string $currency = 'USD' ): int {
		// Handle null/empty
		if ( empty( $amount ) ) {
			return 0;
		}

		// If contains decimal point or comma, treat as decimal amount
		if ( strpos( (string) $amount, '.' ) !== false || strpos( (string) $amount, ',' ) !== false ) {
			$decimal = floatval( str_replace( ',', '.', (string) $amount ) );

			return self::to_smallest_unit( $decimal, $currency );
		}

		// Assume already in the smallest unit
		return (int) $amount;
	}

	/**
	 * Sanitize and convert cents to decimal for display in forms
	 *
	 * @param mixed  $amount   Amount in smallest unit
	 * @param string $currency Currency code
	 *
	 * @return float Decimal amount (e.g., 299.00)
	 * @since 1.0.0
	 */
	public static function sanitize_to_decimal( $amount, string $currency = 'USD' ): float {
		if ( empty( $amount ) ) {
			return 0.0;
		}

		return self::from_smallest_unit( (int) $amount, $currency );
	}

	/**
	 * Format a price with recurring interval information
	 *
	 * @param int         $amount         Amount in the smallest unit (cents)
	 * @param string      $currency       Currency code
	 * @param string|null $interval       Recurring interval (day/week/month/year)
	 * @param int         $interval_count Number of intervals (default 1)
	 *
	 * @return string Formatted price with interval (e.g., "$99.00 per month")
	 * @since 1.0.0
	 */
	public static function format_with_interval( int $amount, string $currency, ?string $interval = null, int $interval_count = 1 ): string {
		$formatted_price = self::format( $amount, $currency );

		if ( empty( $interval ) ) {
			return $formatted_price;
		}

		$interval_text = self::get_interval_text( $interval, $interval_count );

		return $formatted_price . ' ' . $interval_text;
	}

	/**
	 * Get human-readable interval text
	 *
	 * @param string $interval       Interval type (day/week/month/year)
	 * @param int    $interval_count Number of intervals
	 *
	 * @return string Formatted interval text (e.g., "per month", "every 3 months")
	 * @since 1.0.0
	 */
	public static function get_interval_text( string $interval, int $interval_count = 1 ): string {
		if ( $interval_count === 1 ) {
			switch ( $interval ) {
				case 'day':
					return _x( 'per day', 'recurring interval', 'arraypress' );
				case 'week':
					return _x( 'per week', 'recurring interval', 'arraypress' );
				case 'month':
					return _x( 'per month', 'recurring interval', 'arraypress' );
				case 'year':
					return _x( 'per year', 'recurring interval', 'arraypress' );
			}
		} else {
			switch ( $interval ) {
				case 'day':
					return sprintf( _n( 'every %d day', 'every %d days', $interval_count, 'arraypress' ), $interval_count );
				case 'week':
					return sprintf( _n( 'every %d week', 'every %d weeks', $interval_count, 'arraypress' ), $interval_count );
				case 'month':
					return sprintf( _n( 'every %d month', 'every %d months', $interval_count, 'arraypress' ), $interval_count );
				case 'year':
					return sprintf( _n( 'every %d year', 'every %d years', $interval_count, 'arraypress' ), $interval_count );
			}
		}

		return '';
	}

}