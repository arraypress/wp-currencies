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
		'USD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_US' ],
		'EUR' => [ 'symbol' => '€', 'decimals' => 2, 'locale' => 'de_DE' ],
		'GBP' => [ 'symbol' => '£', 'decimals' => 2, 'locale' => 'en_GB' ],
		'JPY' => [ 'symbol' => '¥', 'decimals' => 0, 'locale' => 'ja_JP' ],
		'CNY' => [ 'symbol' => '¥', 'decimals' => 2, 'locale' => 'zh_CN' ],

		// Americas
		'CAD' => [ 'symbol' => 'C$', 'decimals' => 2, 'locale' => 'en_CA' ],
		'MXN' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_MX' ],
		'BRL' => [ 'symbol' => 'R$', 'decimals' => 2, 'locale' => 'pt_BR' ],
		'ARS' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_AR' ],
		'COP' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_CO' ],
		'PEN' => [ 'symbol' => 'S/', 'decimals' => 2, 'locale' => 'es_PE' ],
		'CLP' => [ 'symbol' => '$', 'decimals' => 0, 'locale' => 'es_CL' ],
		'UYU' => [ 'symbol' => '$U', 'decimals' => 2, 'locale' => 'es_UY' ],
		'PYG' => [ 'symbol' => '₲', 'decimals' => 0, 'locale' => 'es_PY' ],
		'BOB' => [ 'symbol' => 'Bs', 'decimals' => 2, 'locale' => 'es_BO' ],
		'CRC' => [ 'symbol' => '₡', 'decimals' => 2, 'locale' => 'es_CR' ],
		'DOP' => [ 'symbol' => 'RD$', 'decimals' => 2, 'locale' => 'es_DO' ],
		'GTQ' => [ 'symbol' => 'Q', 'decimals' => 2, 'locale' => 'es_GT' ],
		'HNL' => [ 'symbol' => 'L', 'decimals' => 2, 'locale' => 'es_HN' ],
		'NIO' => [ 'symbol' => 'C$', 'decimals' => 2, 'locale' => 'es_NI' ],
		'PAB' => [ 'symbol' => 'B/', 'decimals' => 2, 'locale' => 'es_PA' ],

		// Europe (Non-Euro)
		'CHF' => [ 'symbol' => 'CHF', 'decimals' => 2, 'locale' => 'de_CH' ],
		'SEK' => [ 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'sv_SE' ],
		'DKK' => [ 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'da_DK' ],
		'NOK' => [ 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'nb_NO' ],
		'ISK' => [ 'symbol' => 'kr', 'decimals' => 0, 'locale' => 'is_IS' ],
		'PLN' => [ 'symbol' => 'zł', 'decimals' => 2, 'locale' => 'pl_PL' ],
		'CZK' => [ 'symbol' => 'Kč', 'decimals' => 2, 'locale' => 'cs_CZ' ],
		'HUF' => [ 'symbol' => 'Ft', 'decimals' => 0, 'locale' => 'hu_HU' ],
		'RON' => [ 'symbol' => 'lei', 'decimals' => 2, 'locale' => 'ro_RO' ],
		'BGN' => [ 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'bg_BG' ],
		'HRK' => [ 'symbol' => 'kn', 'decimals' => 2, 'locale' => 'hr_HR' ],
		'RSD' => [ 'symbol' => 'din', 'decimals' => 2, 'locale' => 'sr_RS' ],
		'MKD' => [ 'symbol' => 'ден', 'decimals' => 2, 'locale' => 'mk_MK' ],
		'MDL' => [ 'symbol' => 'L', 'decimals' => 2, 'locale' => 'ro_MD' ],
		'UAH' => [ 'symbol' => '₴', 'decimals' => 2, 'locale' => 'uk_UA' ],
		'GEL' => [ 'symbol' => '₾', 'decimals' => 2, 'locale' => 'ka_GE' ],
		'ALL' => [ 'symbol' => 'L', 'decimals' => 2, 'locale' => 'sq_AL' ],
		'BAM' => [ 'symbol' => 'KM', 'decimals' => 2, 'locale' => 'bs_BA' ],

		// Asia-Pacific
		'HKD' => [ 'symbol' => 'HK$', 'decimals' => 2, 'locale' => 'zh_HK' ],
		'TWD' => [ 'symbol' => 'NT$', 'decimals' => 0, 'locale' => 'zh_TW' ],
		'KRW' => [ 'symbol' => '₩', 'decimals' => 0, 'locale' => 'ko_KR' ],
		'SGD' => [ 'symbol' => 'S$', 'decimals' => 2, 'locale' => 'en_SG' ],
		'THB' => [ 'symbol' => '฿', 'decimals' => 2, 'locale' => 'th_TH' ],
		'MYR' => [ 'symbol' => 'RM', 'decimals' => 2, 'locale' => 'ms_MY' ],
		'PHP' => [ 'symbol' => '₱', 'decimals' => 2, 'locale' => 'en_PH' ],
		'IDR' => [ 'symbol' => 'Rp', 'decimals' => 2, 'locale' => 'id_ID' ],
		'VND' => [ 'symbol' => '₫', 'decimals' => 0, 'locale' => 'vi_VN' ],
		'INR' => [ 'symbol' => '₹', 'decimals' => 2, 'locale' => 'en_IN' ],
		'PKR' => [ 'symbol' => '₨', 'decimals' => 2, 'locale' => 'ur_PK' ],
		'BDT' => [ 'symbol' => '৳', 'decimals' => 2, 'locale' => 'bn_BD' ],
		'LKR' => [ 'symbol' => 'Rs', 'decimals' => 2, 'locale' => 'si_LK' ],
		'NPR' => [ 'symbol' => '₨', 'decimals' => 2, 'locale' => 'ne_NP' ],
		'MMK' => [ 'symbol' => 'K', 'decimals' => 2, 'locale' => 'my_MM' ],
		'KHR' => [ 'symbol' => '៛', 'decimals' => 2, 'locale' => 'km_KH' ],
		'LAK' => [ 'symbol' => '₭', 'decimals' => 2, 'locale' => 'lo_LA' ],
		'MNT' => [ 'symbol' => '₮', 'decimals' => 2, 'locale' => 'mn_MN' ],
		'BND' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'ms_BN' ],
		'PGK' => [ 'symbol' => 'K', 'decimals' => 2, 'locale' => 'en_PG' ],
		'FJD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_FJ' ],
		'SBD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_SB' ],
		'TOP' => [ 'symbol' => 'T$', 'decimals' => 2, 'locale' => 'to_TO' ],
		'VUV' => [ 'symbol' => 'VT', 'decimals' => 0, 'locale' => 'en_VU' ],
		'WST' => [ 'symbol' => 'WS$', 'decimals' => 2, 'locale' => 'en_WS' ],
		'MVR' => [ 'symbol' => 'Rf', 'decimals' => 2, 'locale' => 'dv_MV' ],

		// Oceania
		'AUD' => [ 'symbol' => 'A$', 'decimals' => 2, 'locale' => 'en_AU' ],
		'NZD' => [ 'symbol' => 'NZ$', 'decimals' => 2, 'locale' => 'en_NZ' ],

		// Middle East
		'AED' => [ 'symbol' => 'د.إ', 'decimals' => 2, 'locale' => 'ar_AE' ],
		'SAR' => [ 'symbol' => 'SR', 'decimals' => 2, 'locale' => 'ar_SA' ],
		'QAR' => [ 'symbol' => 'QR', 'decimals' => 2, 'locale' => 'ar_QA' ],
		'OMR' => [ 'symbol' => 'ر.ع.', 'decimals' => 3, 'locale' => 'ar_OM' ],
		'KWD' => [ 'symbol' => 'KD', 'decimals' => 3, 'locale' => 'ar_KW' ],
		'BHD' => [ 'symbol' => 'BD', 'decimals' => 3, 'locale' => 'ar_BH' ],
		'JOD' => [ 'symbol' => 'JD', 'decimals' => 3, 'locale' => 'ar_JO' ],
		'ILS' => [ 'symbol' => '₪', 'decimals' => 2, 'locale' => 'he_IL' ],
		'TRY' => [ 'symbol' => '₺', 'decimals' => 2, 'locale' => 'tr_TR' ],
		'LBP' => [ 'symbol' => 'ل.ل', 'decimals' => 2, 'locale' => 'ar_LB' ],

		// Africa
		'ZAR' => [ 'symbol' => 'R', 'decimals' => 2, 'locale' => 'en_ZA' ],
		'EGP' => [ 'symbol' => 'E£', 'decimals' => 2, 'locale' => 'ar_EG' ],
		'NGN' => [ 'symbol' => '₦', 'decimals' => 2, 'locale' => 'en_NG' ],
		'KES' => [ 'symbol' => 'KSh', 'decimals' => 2, 'locale' => 'en_KE' ],
		'GHS' => [ 'symbol' => '₵', 'decimals' => 2, 'locale' => 'en_GH' ],
		'MAD' => [ 'symbol' => 'MAD', 'decimals' => 2, 'locale' => 'ar_MA' ],
		'TND' => [ 'symbol' => 'DT', 'decimals' => 3, 'locale' => 'ar_TN' ],
		'DZD' => [ 'symbol' => 'DA', 'decimals' => 2, 'locale' => 'ar_DZ' ],
		'ETB' => [ 'symbol' => 'Br', 'decimals' => 2, 'locale' => 'am_ET' ],
		'UGX' => [ 'symbol' => 'USh', 'decimals' => 0, 'locale' => 'en_UG' ],
		'TZS' => [ 'symbol' => 'TSh', 'decimals' => 2, 'locale' => 'en_TZ' ],
		'RWF' => [ 'symbol' => 'FRw', 'decimals' => 0, 'locale' => 'rw_RW' ],
		'MUR' => [ 'symbol' => '₨', 'decimals' => 2, 'locale' => 'en_MU' ],
		'SCR' => [ 'symbol' => '₨', 'decimals' => 2, 'locale' => 'en_SC' ],
		'MZN' => [ 'symbol' => 'MT', 'decimals' => 2, 'locale' => 'pt_MZ' ],
		'ZMW' => [ 'symbol' => 'ZK', 'decimals' => 2, 'locale' => 'en_ZM' ],
		'BWP' => [ 'symbol' => 'P', 'decimals' => 2, 'locale' => 'en_BW' ],
		'NAD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_NA' ],
		'SZL' => [ 'symbol' => 'L', 'decimals' => 2, 'locale' => 'en_SZ' ],
		'LSL' => [ 'symbol' => 'L', 'decimals' => 2, 'locale' => 'en_LS' ],
		'MWK' => [ 'symbol' => 'MK', 'decimals' => 2, 'locale' => 'en_MW' ],
		'AOA' => [ 'symbol' => 'Kz', 'decimals' => 2, 'locale' => 'pt_AO' ],
		'BIF' => [ 'symbol' => 'FBu', 'decimals' => 0, 'locale' => 'rn_BI' ],
		'DJF' => [ 'symbol' => 'Fdj', 'decimals' => 0, 'locale' => 'fr_DJ' ],
		'GNF' => [ 'symbol' => 'FG', 'decimals' => 0, 'locale' => 'fr_GN' ],
		'KMF' => [ 'symbol' => 'CF', 'decimals' => 0, 'locale' => 'fr_KM' ],
		'CDF' => [ 'symbol' => 'FC', 'decimals' => 2, 'locale' => 'fr_CD' ],
		'MGA' => [ 'symbol' => 'Ar', 'decimals' => 0, 'locale' => 'mg_MG' ],
		'XAF' => [ 'symbol' => 'FCFA', 'decimals' => 0, 'locale' => 'fr_CM' ],
		'XOF' => [ 'symbol' => 'CFA', 'decimals' => 0, 'locale' => 'fr_SN' ],

		// Caribbean
		'JMD' => [ 'symbol' => 'J$', 'decimals' => 2, 'locale' => 'en_JM' ],
		'TTD' => [ 'symbol' => 'TT$', 'decimals' => 2, 'locale' => 'en_TT' ],
		'BBD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BB' ],
		'BSD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BS' ],
		'BZD' => [ 'symbol' => 'BZ$', 'decimals' => 2, 'locale' => 'en_BZ' ],
		'BMD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BM' ],
		'KYD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_KY' ],
		'XCD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_AG' ],
		'AWG' => [ 'symbol' => 'ƒ', 'decimals' => 2, 'locale' => 'nl_AW' ],
		'ANG' => [ 'symbol' => 'ƒ', 'decimals' => 2, 'locale' => 'nl_CW' ],
		'HTG' => [ 'symbol' => 'G', 'decimals' => 2, 'locale' => 'fr_HT' ],

		// Former Soviet States
		'RUB' => [ 'symbol' => '₽', 'decimals' => 2, 'locale' => 'ru_RU' ],
		'KZT' => [ 'symbol' => '₸', 'decimals' => 2, 'locale' => 'kk_KZ' ],
		'UZS' => [ 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'uz_UZ' ],
		'AZN' => [ 'symbol' => '₼', 'decimals' => 2, 'locale' => 'az_AZ' ],
		'AMD' => [ 'symbol' => '֏', 'decimals' => 2, 'locale' => 'hy_AM' ],
		'KGS' => [ 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'ky_KG' ],
		'TJS' => [ 'symbol' => 'SM', 'decimals' => 2, 'locale' => 'tg_TJ' ],
		'TMT' => [ 'symbol' => 'T', 'decimals' => 2, 'locale' => 'tk_TM' ],

		// Other
		'AFN' => [ 'symbol' => '؋', 'decimals' => 2, 'locale' => 'fa_AF' ],
		'XPF' => [ 'symbol' => '₣', 'decimals' => 0, 'locale' => 'fr_PF' ],
		'CVE' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'pt_CV' ],
		'GIP' => [ 'symbol' => '£', 'decimals' => 2, 'locale' => 'en_GI' ],
		'GMD' => [ 'symbol' => 'D', 'decimals' => 2, 'locale' => 'en_GM' ],
		'GYD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_GY' ],
		'LRD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_LR' ],
		'SLL' => [ 'symbol' => 'Le', 'decimals' => 2, 'locale' => 'en_SL' ],
		'SOS' => [ 'symbol' => 'S', 'decimals' => 2, 'locale' => 'so_SO' ],
		'SRD' => [ 'symbol' => '$', 'decimals' => 2, 'locale' => 'nl_SR' ],
		'STD' => [ 'symbol' => 'Db', 'decimals' => 2, 'locale' => 'pt_ST' ],
	];

	/* ========================================================================
	 * FORMATTING
	 * ======================================================================== */

	/**
	 * Format amount for display with currency symbol.
	 *
	 * Uses a simple symbol-prefix format suitable for admin contexts.
	 * For customer-facing locale-aware formatting, use format_localized().
	 *
	 * @param int    $amount   Amount in smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (3-letter ISO).
	 *
	 * @return string Formatted amount with symbol.
	 */
	public static function format( int $amount, string $currency ): string {
		$currency = strtoupper( $currency );
		$config   = self::get_config( $currency );

		if ( ! $config ) {
			return (string) $amount;
		}

		$is_negative = $amount < 0;
		$abs_amount  = abs( $amount );

		if ( $config['decimals'] === 0 ) {
			$formatted = number_format( $abs_amount );
		} else {
			$divisor   = pow( 10, $config['decimals'] );
			$formatted = number_format( $abs_amount / $divisor, $config['decimals'] );
		}

		return ( $is_negative ? '-' : '' ) . $config['symbol'] . $formatted;
	}

	/**
	 * Format amount using locale-aware formatting.
	 *
	 * Handles symbol position, decimal/thousands separators, and spacing
	 * according to the currency's locale conventions. Suitable for
	 * customer-facing storefront display.
	 *
	 * Requires the PHP intl extension. Falls back to format() if unavailable.
	 *
	 * @param int    $amount   Amount in smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (3-letter ISO).
	 * @param string $locale   Optional locale override (e.g., 'de_DE').
	 *
	 * @return string Locale-formatted amount with symbol.
	 */
	public static function format_localized( int $amount, string $currency, string $locale = '' ): string {
		$currency = strtoupper( $currency );
		$config   = self::get_config( $currency );

		if ( ! $config || ! class_exists( 'NumberFormatter' ) ) {
			return self::format( $amount, $currency );
		}

		if ( empty( $locale ) ) {
			$locale = $config['locale'] ?? 'en_US';
		}

		$decimals = $config['decimals'];
		$decimal  = $decimals > 0 ? $amount / pow( 10, $decimals ) : $amount;

		$formatter = new \NumberFormatter( $locale, \NumberFormatter::CURRENCY );
		$result    = $formatter->formatCurrency( (float) $decimal, $currency );

		return $result !== false ? $result : self::format( $amount, $currency );
	}

	/**
	 * Format amount without currency symbol.
	 *
	 * @param int    $amount   Amount in the smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return string Formatted amount without symbol.
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
	 * Format with currency code instead of symbol.
	 *
	 * @param int    $amount   Amount in the smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return string Amount with currency code (e.g., "99.99 USD").
	 */
	public static function format_with_code( int $amount, string $currency ): string {
		return self::format_plain( $amount, $currency ) . ' ' . strtoupper( $currency );
	}

	/**
	 * Format a price with recurring interval information.
	 *
	 * @param int         $amount         Amount in the smallest unit (cents).
	 * @param string      $currency       Currency code.
	 * @param string|null $interval       Recurring interval (day/week/month/year).
	 * @param int         $interval_count Number of intervals (default 1).
	 *
	 * @return string Formatted price with interval (e.g., "$99.00 per month").
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
	 * Format a price with recurring interval using locale-aware formatting.
	 *
	 * @param int         $amount         Amount in the smallest unit (cents).
	 * @param string      $currency       Currency code.
	 * @param string|null $interval       Recurring interval (day/week/month/year).
	 * @param int         $interval_count Number of intervals (default 1).
	 * @param string      $locale         Optional locale override.
	 *
	 * @return string Locale-formatted price with interval.
	 */
	public static function format_localized_with_interval( int $amount, string $currency, ?string $interval = null, int $interval_count = 1, string $locale = '' ): string {
		$formatted_price = self::format_localized( $amount, $currency, $locale );

		if ( empty( $interval ) ) {
			return $formatted_price;
		}

		$interval_text = self::get_interval_text( $interval, $interval_count );

		return $formatted_price . ' ' . $interval_text;
	}

	/**
	 * Get human-readable interval text.
	 *
	 * @param string $interval       Interval type (day/week/month/year).
	 * @param int    $interval_count Number of intervals.
	 *
	 * @return string Formatted interval text (e.g., "per month", "every 3 months").
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

	/* ========================================================================
	 * UNIT CONVERSION
	 * ======================================================================== */

	/**
	 * Convert decimal amount to the smallest unit for Stripe.
	 *
	 * @param float  $amount   Decimal amount (e.g., 19.99).
	 * @param string $currency Currency code.
	 *
	 * @return int Amount in the smallest unit.
	 */
	public static function to_smallest_unit( float $amount, string $currency ): int {
		$config   = self::get_config( $currency );
		$decimals = $config['decimals'] ?? 2;

		$multiplier = pow( 10, $decimals );

		return (int) round( $amount * $multiplier );
	}

	/**
	 * Convert from the smallest unit to decimal amount.
	 *
	 * @param int    $amount   Amount in the smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return float Decimal amount.
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

	/* ========================================================================
	 * CURRENCY DATA
	 * ======================================================================== */

	/**
	 * Get all supported currencies.
	 *
	 * @return array All currency configurations.
	 */
	public static function all(): array {
		return self::CURRENCIES;
	}

	/**
	 * Get currency configuration.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return array|null Configuration array or null.
	 */
	public static function get_config( string $currency ): ?array {
		return self::CURRENCIES[ strtoupper( $currency ) ] ?? null;
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return string Symbol or currency code if not found.
	 */
	public static function get_symbol( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['symbol'] ?? strtoupper( $currency );
	}

	/**
	 * Get decimal places for currency.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return int Number of decimal places.
	 */
	public static function get_decimals( string $currency ): int {
		$config = self::get_config( $currency );

		return $config['decimals'] ?? 2;
	}

	/**
	 * Get locale for currency.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return string Locale string (e.g., 'en_US').
	 */
	public static function get_locale( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['locale'] ?? 'en_US';
	}

	/**
	 * Check if currency is supported.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return bool True if supported.
	 */
	public static function is_supported( string $currency ): bool {
		return isset( self::CURRENCIES[ strtoupper( $currency ) ] );
	}

	/**
	 * Check if currency is zero-decimal.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return bool True if zero-decimal currency.
	 */
	public static function is_zero_decimal( string $currency ): bool {
		$config = self::get_config( $currency );

		return $config && $config['decimals'] === 0;
	}

	/* ========================================================================
	 * OBJECT RESOLUTION
	 * ======================================================================== */

	/**
	 * Resolve currency code from an item object.
	 *
	 * Checks for a get_currency() method, a currency property, or falls back
	 * to the provided default.
	 *
	 * @param object|null $item    Data object to check for currency.
	 * @param string      $default Default currency code if not found.
	 *
	 * @return string Currency code (uppercase).
	 */
	public static function resolve( $item, string $default = 'USD' ): string {
		if ( $item && method_exists( $item, 'get_currency' ) ) {
			$currency = $item->get_currency();
			if ( ! empty( $currency ) ) {
				return strtoupper( $currency );
			}
		}

		if ( is_object( $item ) && property_exists( $item, 'currency' ) && ! empty( $item->currency ) ) {
			return strtoupper( $item->currency );
		}

		return strtoupper( $default );
	}

	/**
	 * Resolve recurring interval from an item object.
	 *
	 * Checks for interval-related methods or properties on the item.
	 *
	 * @param object|null $item Data object to check for interval.
	 *
	 * @return array{interval: string|null, interval_count: int}
	 */
	public static function resolve_interval( $item ): array {
		$interval       = null;
		$interval_count = 1;

		if ( $item && method_exists( $item, 'get_recurring_interval' ) ) {
			$interval = $item->get_recurring_interval() ?: null;
		} elseif ( is_object( $item ) && property_exists( $item, 'recurring_interval' ) ) {
			$interval = $item->recurring_interval ?: null;
		}

		if ( $interval ) {
			if ( $item && method_exists( $item, 'get_recurring_interval_count' ) ) {
				$interval_count = (int) ( $item->get_recurring_interval_count() ?: 1 );
			} elseif ( is_object( $item ) && property_exists( $item, 'recurring_interval_count' ) ) {
				$interval_count = (int) ( $item->recurring_interval_count ?: 1 );
			}
		}

		return [
			'interval'       => $interval,
			'interval_count' => $interval_count,
		];
	}

	/* ========================================================================
	 * RENDERING
	 * ======================================================================== */

	/**
	 * Render a price amount as formatted HTML with optional recurring interval.
	 *
	 * Converts an amount in the smallest currency unit to a formatted string.
	 * Currency and interval are resolved from the item object when not explicitly
	 * provided. One-time prices show the amount only. Recurring prices append
	 * the interval (e.g., "per month").
	 *
	 * @param mixed       $value          Amount in smallest unit (e.g., cents).
	 * @param object|null $item           Data object (checked for currency, interval properties).
	 * @param string      $currency       Optional currency code override.
	 * @param string|null $interval       Optional interval override.
	 * @param int|null    $interval_count Optional interval count override.
	 *
	 * @return string|null HTML string or null if value is not numeric.
	 */
	public static function render( $value, $item = null, string $currency = '', ?string $interval = null, ?int $interval_count = null ): ?string {
		if ( ! is_numeric( $value ) ) {
			return null;
		}

		if ( empty( $currency ) ) {
			$currency = self::resolve( $item );
		}

		if ( $interval === null && $interval_count === null ) {
			$resolved       = self::resolve_interval( $item );
			$interval       = $resolved['interval'];
			$interval_count = $resolved['interval_count'];
		}

		$formatted = self::format_with_interval(
			intval( $value ),
			$currency,
			$interval,
			$interval_count ?? 1
		);

		return sprintf(
			'<span class="price">%s</span>',
			esc_html( $formatted )
		);
	}

}