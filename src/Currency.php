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
	 * Stripe supported currencies with configuration.
	 *
	 * Complete list as of 2025 - 136 currencies.
	 *
	 * The `decimals` value reflects what Stripe's API expects, not necessarily the
	 * currency's logical decimal places. Some currencies (ISK, UGX) are logically
	 * zero-decimal but Stripe requires two-decimal representation for backward
	 * compatibility. HUF and TWD accept two-decimal charges but are zero-decimal
	 * for payouts only.
	 *
	 * @link  https://stripe.com/docs/currencies
	 * @since 1.0.0
	 * @var array
	 */
	private const CURRENCIES = [

		// Major Currencies
		'USD' => [ 'name' => 'US Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_US' ],
		'EUR' => [ 'name' => 'Euro', 'symbol' => '€', 'decimals' => 2, 'locale' => 'de_DE' ],
		'GBP' => [ 'name' => 'British Pound', 'symbol' => '£', 'decimals' => 2, 'locale' => 'en_GB' ],
		'JPY' => [ 'name' => 'Japanese Yen', 'symbol' => '¥', 'decimals' => 0, 'locale' => 'ja_JP' ],
		'CNY' => [ 'name' => 'Chinese Yuan', 'symbol' => '¥', 'decimals' => 2, 'locale' => 'zh_CN' ],

		// Americas
		'CAD' => [ 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'decimals' => 2, 'locale' => 'en_CA' ],
		'MXN' => [ 'name' => 'Mexican Peso', 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_MX' ],
		'BRL' => [ 'name' => 'Brazilian Real', 'symbol' => 'R$', 'decimals' => 2, 'locale' => 'pt_BR' ],
		'ARS' => [ 'name' => 'Argentine Peso', 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_AR' ],
		'COP' => [ 'name' => 'Colombian Peso', 'symbol' => '$', 'decimals' => 2, 'locale' => 'es_CO' ],
		'PEN' => [ 'name' => 'Peruvian Sol', 'symbol' => 'S/', 'decimals' => 2, 'locale' => 'es_PE' ],
		'CLP' => [ 'name' => 'Chilean Peso', 'symbol' => '$', 'decimals' => 0, 'locale' => 'es_CL' ],
		'UYU' => [ 'name' => 'Uruguayan Peso', 'symbol' => '$U', 'decimals' => 2, 'locale' => 'es_UY' ],
		'PYG' => [ 'name' => 'Paraguayan Guarani', 'symbol' => '₲', 'decimals' => 0, 'locale' => 'es_PY' ],
		'BOB' => [ 'name' => 'Bolivian Boliviano', 'symbol' => 'Bs', 'decimals' => 2, 'locale' => 'es_BO' ],
		'CRC' => [ 'name' => 'Costa Rican Colón', 'symbol' => '₡', 'decimals' => 2, 'locale' => 'es_CR' ],
		'DOP' => [ 'name' => 'Dominican Peso', 'symbol' => 'RD$', 'decimals' => 2, 'locale' => 'es_DO' ],
		'GTQ' => [ 'name' => 'Guatemalan Quetzal', 'symbol' => 'Q', 'decimals' => 2, 'locale' => 'es_GT' ],
		'HNL' => [ 'name' => 'Honduran Lempira', 'symbol' => 'L', 'decimals' => 2, 'locale' => 'es_HN' ],
		'NIO' => [ 'name' => 'Nicaraguan Córdoba', 'symbol' => 'C$', 'decimals' => 2, 'locale' => 'es_NI' ],
		'PAB' => [ 'name' => 'Panamanian Balboa', 'symbol' => 'B/', 'decimals' => 2, 'locale' => 'es_PA' ],

		// Europe (Non-Euro)
		'CHF' => [ 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'decimals' => 2, 'locale' => 'de_CH' ],
		'SEK' => [ 'name' => 'Swedish Krona', 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'sv_SE' ],
		'DKK' => [ 'name' => 'Danish Krone', 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'da_DK' ],
		'NOK' => [ 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'nb_NO' ],
		'ISK' => [ 'name' => 'Icelandic Króna', 'symbol' => 'kr', 'decimals' => 2, 'locale' => 'is_IS' ],
		// Logically zero-decimal but Stripe requires two-decimal representation
		'PLN' => [ 'name' => 'Polish Złoty', 'symbol' => 'zł', 'decimals' => 2, 'locale' => 'pl_PL' ],
		'CZK' => [ 'name' => 'Czech Koruna', 'symbol' => 'Kč', 'decimals' => 2, 'locale' => 'cs_CZ' ],
		'HUF' => [ 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'decimals' => 2, 'locale' => 'hu_HU' ],
		// Zero-decimal for payouts only; charges accept two-decimal
		'RON' => [ 'name' => 'Romanian Leu', 'symbol' => 'lei', 'decimals' => 2, 'locale' => 'ro_RO' ],
		'BGN' => [ 'name' => 'Bulgarian Lev', 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'bg_BG' ],
		'RSD' => [ 'name' => 'Serbian Dinar', 'symbol' => 'din', 'decimals' => 2, 'locale' => 'sr_RS' ],
		'MKD' => [ 'name' => 'Macedonian Denar', 'symbol' => 'ден', 'decimals' => 2, 'locale' => 'mk_MK' ],
		'MDL' => [ 'name' => 'Moldovan Leu', 'symbol' => 'L', 'decimals' => 2, 'locale' => 'ro_MD' ],
		'UAH' => [ 'name' => 'Ukrainian Hryvnia', 'symbol' => '₴', 'decimals' => 2, 'locale' => 'uk_UA' ],
		'GEL' => [ 'name' => 'Georgian Lari', 'symbol' => '₾', 'decimals' => 2, 'locale' => 'ka_GE' ],
		'ALL' => [ 'name' => 'Albanian Lek', 'symbol' => 'L', 'decimals' => 2, 'locale' => 'sq_AL' ],
		'BAM' => [ 'name'     => 'Bosnia-Herzegovina Convertible Mark',
		           'symbol'   => 'KM',
		           'decimals' => 2,
		           'locale'   => 'bs_BA'
		],

		// Asia-Pacific
		'HKD' => [ 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'decimals' => 2, 'locale' => 'zh_HK' ],
		'TWD' => [ 'name' => 'New Taiwan Dollar', 'symbol' => 'NT$', 'decimals' => 2, 'locale' => 'zh_TW' ],
		// Zero-decimal for payouts only; charges accept two-decimal
		'KRW' => [ 'name' => 'South Korean Won', 'symbol' => '₩', 'decimals' => 0, 'locale' => 'ko_KR' ],
		'SGD' => [ 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'decimals' => 2, 'locale' => 'en_SG' ],
		'THB' => [ 'name' => 'Thai Baht', 'symbol' => '฿', 'decimals' => 2, 'locale' => 'th_TH' ],
		'MYR' => [ 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'decimals' => 2, 'locale' => 'ms_MY' ],
		'PHP' => [ 'name' => 'Philippine Peso', 'symbol' => '₱', 'decimals' => 2, 'locale' => 'en_PH' ],
		'IDR' => [ 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'decimals' => 2, 'locale' => 'id_ID' ],
		'VND' => [ 'name' => 'Vietnamese Đồng', 'symbol' => '₫', 'decimals' => 0, 'locale' => 'vi_VN' ],
		'INR' => [ 'name' => 'Indian Rupee', 'symbol' => '₹', 'decimals' => 2, 'locale' => 'en_IN' ],
		'PKR' => [ 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'decimals' => 2, 'locale' => 'ur_PK' ],
		'BDT' => [ 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'decimals' => 2, 'locale' => 'bn_BD' ],
		'LKR' => [ 'name' => 'Sri Lankan Rupee', 'symbol' => 'Rs', 'decimals' => 2, 'locale' => 'si_LK' ],
		'NPR' => [ 'name' => 'Nepalese Rupee', 'symbol' => '₨', 'decimals' => 2, 'locale' => 'ne_NP' ],
		'MMK' => [ 'name' => 'Myanmar Kyat', 'symbol' => 'K', 'decimals' => 2, 'locale' => 'my_MM' ],
		'KHR' => [ 'name' => 'Cambodian Riel', 'symbol' => '៛', 'decimals' => 2, 'locale' => 'km_KH' ],
		'LAK' => [ 'name' => 'Lao Kip', 'symbol' => '₭', 'decimals' => 2, 'locale' => 'lo_LA' ],
		'MNT' => [ 'name' => 'Mongolian Tögrög', 'symbol' => '₮', 'decimals' => 2, 'locale' => 'mn_MN' ],
		'BND' => [ 'name' => 'Brunei Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'ms_BN' ],
		'PGK' => [ 'name' => 'Papua New Guinean Kina', 'symbol' => 'K', 'decimals' => 2, 'locale' => 'en_PG' ],
		'FJD' => [ 'name' => 'Fijian Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_FJ' ],
		'SBD' => [ 'name' => 'Solomon Islands Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_SB' ],
		'TOP' => [ 'name' => 'Tongan Paʻanga', 'symbol' => 'T$', 'decimals' => 2, 'locale' => 'to_TO' ],
		'VUV' => [ 'name' => 'Vanuatu Vatu', 'symbol' => 'VT', 'decimals' => 0, 'locale' => 'en_VU' ],
		'WST' => [ 'name' => 'Samoan Tālā', 'symbol' => 'WS$', 'decimals' => 2, 'locale' => 'en_WS' ],
		'MVR' => [ 'name' => 'Maldivian Rufiyaa', 'symbol' => 'Rf', 'decimals' => 2, 'locale' => 'dv_MV' ],

		// Oceania
		'AUD' => [ 'name' => 'Australian Dollar', 'symbol' => 'A$', 'decimals' => 2, 'locale' => 'en_AU' ],
		'NZD' => [ 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'decimals' => 2, 'locale' => 'en_NZ' ],

		// Middle East
		'AED' => [ 'name' => 'United Arab Emirates Dirham', 'symbol' => 'د.إ', 'decimals' => 2, 'locale' => 'ar_AE' ],
		'SAR' => [ 'name' => 'Saudi Riyal', 'symbol' => 'SR', 'decimals' => 2, 'locale' => 'ar_SA' ],
		'QAR' => [ 'name' => 'Qatari Riyal', 'symbol' => 'QR', 'decimals' => 2, 'locale' => 'ar_QA' ],
		'OMR' => [ 'name' => 'Omani Rial', 'symbol' => 'ر.ع.', 'decimals' => 3, 'locale' => 'ar_OM' ],
		'KWD' => [ 'name' => 'Kuwaiti Dinar', 'symbol' => 'KD', 'decimals' => 3, 'locale' => 'ar_KW' ],
		'BHD' => [ 'name' => 'Bahraini Dinar', 'symbol' => 'BD', 'decimals' => 3, 'locale' => 'ar_BH' ],
		'JOD' => [ 'name' => 'Jordanian Dinar', 'symbol' => 'JD', 'decimals' => 3, 'locale' => 'ar_JO' ],
		'ILS' => [ 'name' => 'Israeli New Shekel', 'symbol' => '₪', 'decimals' => 2, 'locale' => 'he_IL' ],
		'TRY' => [ 'name' => 'Turkish Lira', 'symbol' => '₺', 'decimals' => 2, 'locale' => 'tr_TR' ],
		'LBP' => [ 'name' => 'Lebanese Pound', 'symbol' => 'ل.ل', 'decimals' => 2, 'locale' => 'ar_LB' ],

		// Africa
		'ZAR' => [ 'name' => 'South African Rand', 'symbol' => 'R', 'decimals' => 2, 'locale' => 'en_ZA' ],
		'EGP' => [ 'name' => 'Egyptian Pound', 'symbol' => 'E£', 'decimals' => 2, 'locale' => 'ar_EG' ],
		'NGN' => [ 'name' => 'Nigerian Naira', 'symbol' => '₦', 'decimals' => 2, 'locale' => 'en_NG' ],
		'KES' => [ 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'decimals' => 2, 'locale' => 'en_KE' ],
		'GHS' => [ 'name' => 'Ghanaian Cedi', 'symbol' => '₵', 'decimals' => 2, 'locale' => 'en_GH' ],
		'MAD' => [ 'name' => 'Moroccan Dirham', 'symbol' => 'MAD', 'decimals' => 2, 'locale' => 'ar_MA' ],
		'TND' => [ 'name' => 'Tunisian Dinar', 'symbol' => 'DT', 'decimals' => 3, 'locale' => 'ar_TN' ],
		'DZD' => [ 'name' => 'Algerian Dinar', 'symbol' => 'DA', 'decimals' => 2, 'locale' => 'ar_DZ' ],
		'ETB' => [ 'name' => 'Ethiopian Birr', 'symbol' => 'Br', 'decimals' => 2, 'locale' => 'am_ET' ],
		'UGX' => [ 'name' => 'Ugandan Shilling', 'symbol' => 'USh', 'decimals' => 2, 'locale' => 'en_UG' ],
		// Logically zero-decimal but Stripe requires two-decimal representation
		'TZS' => [ 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'decimals' => 2, 'locale' => 'en_TZ' ],
		'RWF' => [ 'name' => 'Rwandan Franc', 'symbol' => 'FRw', 'decimals' => 0, 'locale' => 'rw_RW' ],
		'MUR' => [ 'name' => 'Mauritian Rupee', 'symbol' => '₨', 'decimals' => 2, 'locale' => 'en_MU' ],
		'SCR' => [ 'name' => 'Seychellois Rupee', 'symbol' => '₨', 'decimals' => 2, 'locale' => 'en_SC' ],
		'MZN' => [ 'name' => 'Mozambican Metical', 'symbol' => 'MT', 'decimals' => 2, 'locale' => 'pt_MZ' ],
		'ZMW' => [ 'name' => 'Zambian Kwacha', 'symbol' => 'ZK', 'decimals' => 2, 'locale' => 'en_ZM' ],
		'BWP' => [ 'name' => 'Botswanan Pula', 'symbol' => 'P', 'decimals' => 2, 'locale' => 'en_BW' ],
		'NAD' => [ 'name' => 'Namibian Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_NA' ],
		'SZL' => [ 'name' => 'Swazi Lilangeni', 'symbol' => 'L', 'decimals' => 2, 'locale' => 'en_SZ' ],
		'LSL' => [ 'name' => 'Lesotho Loti', 'symbol' => 'L', 'decimals' => 2, 'locale' => 'en_LS' ],
		'MWK' => [ 'name' => 'Malawian Kwacha', 'symbol' => 'MK', 'decimals' => 2, 'locale' => 'en_MW' ],
		'AOA' => [ 'name' => 'Angolan Kwanza', 'symbol' => 'Kz', 'decimals' => 2, 'locale' => 'pt_AO' ],
		'BIF' => [ 'name' => 'Burundian Franc', 'symbol' => 'FBu', 'decimals' => 0, 'locale' => 'rn_BI' ],
		'DJF' => [ 'name' => 'Djiboutian Franc', 'symbol' => 'Fdj', 'decimals' => 0, 'locale' => 'fr_DJ' ],
		'GNF' => [ 'name' => 'Guinean Franc', 'symbol' => 'FG', 'decimals' => 0, 'locale' => 'fr_GN' ],
		'KMF' => [ 'name' => 'Comorian Franc', 'symbol' => 'CF', 'decimals' => 0, 'locale' => 'fr_KM' ],
		'CDF' => [ 'name' => 'Congolese Franc', 'symbol' => 'FC', 'decimals' => 2, 'locale' => 'fr_CD' ],
		'MGA' => [ 'name' => 'Malagasy Ariary', 'symbol' => 'Ar', 'decimals' => 0, 'locale' => 'mg_MG' ],
		'XAF' => [ 'name' => 'Central African CFA Franc', 'symbol' => 'FCFA', 'decimals' => 0, 'locale' => 'fr_CM' ],
		'XOF' => [ 'name' => 'West African CFA Franc', 'symbol' => 'CFA', 'decimals' => 0, 'locale' => 'fr_SN' ],

		// Caribbean
		'JMD' => [ 'name' => 'Jamaican Dollar', 'symbol' => 'J$', 'decimals' => 2, 'locale' => 'en_JM' ],
		'TTD' => [ 'name' => 'Trinidad & Tobago Dollar', 'symbol' => 'TT$', 'decimals' => 2, 'locale' => 'en_TT' ],
		'BBD' => [ 'name' => 'Barbadian Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BB' ],
		'BSD' => [ 'name' => 'Bahamian Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BS' ],
		'BZD' => [ 'name' => 'Belize Dollar', 'symbol' => 'BZ$', 'decimals' => 2, 'locale' => 'en_BZ' ],
		'BMD' => [ 'name' => 'Bermudan Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_BM' ],
		'KYD' => [ 'name' => 'Cayman Islands Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_KY' ],
		'XCD' => [ 'name' => 'East Caribbean Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_AG' ],
		'AWG' => [ 'name' => 'Aruban Florin', 'symbol' => 'ƒ', 'decimals' => 2, 'locale' => 'nl_AW' ],
		'ANG' => [ 'name' => 'Netherlands Antillean Guilder', 'symbol' => 'ƒ', 'decimals' => 2, 'locale' => 'nl_CW' ],
		'HTG' => [ 'name' => 'Haitian Gourde', 'symbol' => 'G', 'decimals' => 2, 'locale' => 'fr_HT' ],

		// Former Soviet States
		'RUB' => [ 'name' => 'Russian Ruble', 'symbol' => '₽', 'decimals' => 2, 'locale' => 'ru_RU' ],
		'KZT' => [ 'name' => 'Kazakhstani Tenge', 'symbol' => '₸', 'decimals' => 2, 'locale' => 'kk_KZ' ],
		'UZS' => [ 'name' => 'Uzbekistani Som', 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'uz_UZ' ],
		'AZN' => [ 'name' => 'Azerbaijani Manat', 'symbol' => '₼', 'decimals' => 2, 'locale' => 'az_AZ' ],
		'AMD' => [ 'name' => 'Armenian Dram', 'symbol' => '֏', 'decimals' => 2, 'locale' => 'hy_AM' ],
		'KGS' => [ 'name' => 'Kyrgystani Som', 'symbol' => 'лв', 'decimals' => 2, 'locale' => 'ky_KG' ],
		'TJS' => [ 'name' => 'Tajikistani Somoni', 'symbol' => 'SM', 'decimals' => 2, 'locale' => 'tg_TJ' ],
		'TMT' => [ 'name' => 'Turkmenistani Manat', 'symbol' => 'T', 'decimals' => 2, 'locale' => 'tk_TM' ],

		// Other
		'AFN' => [ 'name' => 'Afghan Afghani', 'symbol' => '؋', 'decimals' => 2, 'locale' => 'fa_AF' ],
		'XPF' => [ 'name' => 'CFP Franc', 'symbol' => '₣', 'decimals' => 0, 'locale' => 'fr_PF' ],
		'CVE' => [ 'name' => 'Cape Verdean Escudo', 'symbol' => '$', 'decimals' => 2, 'locale' => 'pt_CV' ],
		'GIP' => [ 'name' => 'Gibraltar Pound', 'symbol' => '£', 'decimals' => 2, 'locale' => 'en_GI' ],
		'GMD' => [ 'name' => 'Gambian Dalasi', 'symbol' => 'D', 'decimals' => 2, 'locale' => 'en_GM' ],
		'GYD' => [ 'name' => 'Guyanaese Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_GY' ],
		'LRD' => [ 'name' => 'Liberian Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'en_LR' ],
		'SLL' => [ 'name' => 'Sierra Leonean Leone', 'symbol' => 'Le', 'decimals' => 2, 'locale' => 'en_SL' ],
		'SOS' => [ 'name' => 'Somali Shilling', 'symbol' => 'S', 'decimals' => 2, 'locale' => 'so_SO' ],
		'SRD' => [ 'name' => 'Surinamese Dollar', 'symbol' => '$', 'decimals' => 2, 'locale' => 'nl_SR' ],
		'STN' => [ 'name' => 'São Tomé & Príncipe Dobra', 'symbol' => 'Db', 'decimals' => 2, 'locale' => 'pt_ST' ],
	];

	/** =========================================================================
	 *  Currency Data
	 *  ======================================================================== */

	/**
	 * Get all supported currencies.
	 *
	 * @return array All currency configurations.
	 * @since 1.0.0
	 */
	public static function all(): array {
		return self::CURRENCIES;
	}

	/**
	 * Get currency configuration.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return array|null Configuration array or null if unsupported.
	 * @since 1.0.0
	 */
	public static function get_config( string $currency ): ?array {
		return self::CURRENCIES[ strtoupper( $currency ) ] ?? null;
	}

	/**
	 * Get currency name.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return string Currency name or code if not found.
	 * @since 1.0.0
	 */
	public static function get_name( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['name'] ?? strtoupper( $currency );
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return string Symbol or currency code if not found.
	 * @since 1.0.0
	 */
	public static function get_symbol( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['symbol'] ?? strtoupper( $currency );
	}

	/**
	 * Get decimal places for currency.
	 *
	 * Returns the number of decimal places that Stripe's API expects for this
	 * currency. Note that some currencies (ISK, UGX) are logically zero-decimal
	 * but Stripe requires two-decimal representation.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return int Number of decimal places.
	 * @since 1.0.0
	 */
	public static function get_decimals( string $currency ): int {
		$config = self::get_config( $currency );

		return $config['decimals'] ?? 2;
	}

	/**
	 * Get the native locale for a currency.
	 *
	 * Returns the primary locale associated with the currency's home country.
	 * For locale-aware formatting, prefer format_localized() which defaults
	 * to the WordPress site locale.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return string Locale string (e.g., 'en_US').
	 * @since 1.0.0
	 */
	public static function get_native_locale( string $currency ): string {
		$config = self::get_config( $currency );

		return $config['locale'] ?? 'en_US';
	}

	/**
	 * Get currencies formatted as select options.
	 *
	 * Returns currencies in "Name (symbol) — CODE" format suitable for
	 * dropdown selects.
	 *
	 * @return array<string, string> Options keyed by currency code.
	 * @since 1.0.0
	 */
	public static function get_options(): array {
		$options = [];

		foreach ( self::CURRENCIES as $code => $config ) {
			$options[ $code ] = $config['name'] . ' (' . $config['symbol'] . ') — ' . $code;
		}

		return $options;
	}

	/**
	 * Get currency codes as select options.
	 *
	 * Returns a simple code-to-code map suitable for compact dropdowns
	 * where only the currency code is needed.
	 *
	 * @return array<string, string> Options keyed by currency code.
	 * @since 1.0.0
	 */
	public static function get_codes(): array {
		$options = [];

		foreach ( self::CURRENCIES as $code => $config ) {
			$options[ $code ] = $code;
		}

		return $options;
	}

	/** =========================================================================
	 *  Formatting
	 *  ======================================================================== */

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
	 * @since 1.0.0
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
	 * according to locale conventions. Suitable for customer-facing
	 * storefront display.
	 *
	 * When no locale is provided, defaults to the WordPress site locale.
	 * Requires the PHP intl extension. Falls back to format() if unavailable.
	 *
	 * @param int    $amount   Amount in smallest unit (cents, pence, etc).
	 * @param string $currency Currency code (3-letter ISO).
	 * @param string $locale   Optional locale override (e.g., 'de_DE').
	 *
	 * @return string Locale-formatted amount with symbol.
	 * @since 1.0.0
	 */
	public static function format_localized( int $amount, string $currency, string $locale = '' ): string {
		$currency = strtoupper( $currency );
		$config   = self::get_config( $currency );

		if ( ! $config || ! class_exists( 'NumberFormatter' ) ) {
			return self::format( $amount, $currency );
		}

		if ( empty( $locale ) ) {
			$locale = function_exists( 'get_locale' ) ? get_locale() : 'en_US';
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
	 * @param int    $amount   Amount in smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return string Formatted amount without symbol.
	 * @since 1.0.0
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
	 * @param int    $amount   Amount in smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return string Amount with currency code (e.g., "99.99 USD").
	 * @since 1.0.0
	 */
	public static function format_with_code( int $amount, string $currency ): string {
		return self::format_plain( $amount, $currency ) . ' ' . strtoupper( $currency );
	}

	/**
	 * Render a formatted price as HTML.
	 *
	 * Wraps the formatted amount in a span for use in admin tables,
	 * order screens, or any context that needs inline HTML output.
	 *
	 * @param int    $amount   Amount in smallest unit (e.g., cents).
	 * @param string $currency Currency code.
	 *
	 * @return string HTML span with formatted price.
	 * @since 1.0.0
	 */
	public static function render( int $amount, string $currency ): string {
		return sprintf(
			'<span class="price">%s</span>',
			esc_html( self::format( $amount, $currency ) )
		);
	}

	/** =========================================================================
	 *  Unit Conversion
	 *  ======================================================================== */

	/**
	 * Convert decimal amount to the smallest unit for Stripe.
	 *
	 * @param float  $amount   Decimal amount (e.g., 19.99).
	 * @param string $currency Currency code.
	 *
	 * @return int Amount in the smallest unit.
	 * @since 1.0.0
	 */
	public static function to_smallest_unit( float $amount, string $currency ): int {
		$config   = self::get_config( $currency );
		$decimals = $config['decimals'] ?? 2;

		return (int) round( $amount * pow( 10, $decimals ) );
	}

	/**
	 * Convert from the smallest unit to decimal amount.
	 *
	 * @param int    $amount   Amount in the smallest unit.
	 * @param string $currency Currency code.
	 *
	 * @return float Decimal amount.
	 * @since 1.0.0
	 */
	public static function from_smallest_unit( int $amount, string $currency ): float {
		$config   = self::get_config( $currency );
		$decimals = $config['decimals'] ?? 2;

		if ( $decimals === 0 ) {
			return (float) $amount;
		}

		return $amount / pow( 10, $decimals );
	}

	/** =========================================================================
	 *  Validation
	 *  ======================================================================== */

	/**
	 * Check if currency is supported.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return bool True if supported.
	 * @since 1.0.0
	 */
	public static function is_supported( string $currency ): bool {
		return isset( self::CURRENCIES[ strtoupper( $currency ) ] );
	}

	/**
	 * Check if currency is zero-decimal.
	 *
	 * Zero-decimal currencies (e.g. JPY, KRW) are stored and sent to
	 * Stripe as whole numbers without any multiplication.
	 *
	 * Note: Some currencies like ISK and UGX are logically zero-decimal
	 * but Stripe requires two-decimal representation for backward
	 * compatibility. This method returns false for those currencies
	 * since the API expects two-decimal values.
	 *
	 * @param string $currency Currency code.
	 *
	 * @return bool True if zero-decimal currency.
	 * @since 1.0.0
	 */
	public static function is_zero_decimal( string $currency ): bool {
		$config = self::get_config( $currency );

		return $config && $config['decimals'] === 0;
	}

}