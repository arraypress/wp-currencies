# WordPress Currencies

A comprehensive WordPress library for Stripe currency formatting and conversion, supporting all 135 Stripe currencies with proper decimal handling.

## Features

- 💳 **All 135 Stripe Currencies** - Complete support for every Stripe-supported currency
- 🔢 **Smart Decimal Handling** - Correctly handles zero-decimal (JPY, KRW) and three-decimal (KWD, OMR) currencies
- 💰 **Formatting Options** - Format with symbols, codes, or plain numbers
- 🔄 **Bidirectional Conversion** - Convert between decimal amounts and Stripe's smallest units
- ✅ **Validation & Sanitization** - Validate and sanitize currency codes
- 🎨 **Gutenberg Ready** - Compatible options format for React/Gutenberg
- ⚡ **Zero Dependencies** - Lightweight, only requires PHP 7.4+
- 🔌 **WordPress Native** - Built specifically for WordPress

## Installation

```bash
composer require arraypress/wp-currencies
```

## Basic Usage

```php
use ArrayPress\Currencies\Currency;

// Format amount for display (amount is in cents)
echo Currency::format( 9999, 'USD' );  // $99.99
echo Currency::format( 9999, 'EUR' );  // €99.99
echo Currency::format( 9999, 'JPY' );  // ¥9,999 (zero-decimal)
echo Currency::format( 9999, 'KWD' );  // KD9.999 (three-decimal)

// Format without symbol
echo Currency::format_plain( 9999, 'USD' );  // 99.99

// Format with currency code
echo Currency::format_with_code( 9999, 'USD' );  // 99.99 USD
```

## Conversion Methods

```php
// Convert decimal to Stripe's smallest unit
$cents  = Currency::to_smallest_unit( 99.99, 'USD' );    // 9999
$yen    = Currency::to_smallest_unit( 9999, 'JPY' );       // 9999 (no decimals)
$dinars = Currency::to_smallest_unit( 9.999, 'KWD' );   // 9999 (three decimals)

// Convert from Stripe's smallest unit to decimal
$dollars = Currency::from_smallest_unit( 9999, 'USD' );  // 99.99
$yen     = Currency::from_smallest_unit( 9999, 'JPY' );      // 9999.0
$dinars  = Currency::from_smallest_unit( 9999, 'KWD' );   // 9.999
```

## Validation & Information

```php
// Check if currency is supported
if ( Currency::is_supported( 'USD' ) ) {
	// Valid Stripe currency
}

// Check if zero-decimal currency
if ( Currency::is_zero_decimal( 'JPY' ) ) {
	// Handle zero-decimal logic
}

// Get currency configuration
$config = Currency::get_config( 'EUR' );
// Returns: ['symbol' => '€', 'decimals' => 2]

// Get symbol only
$symbol = Currency::get_symbol( 'GBP' );  // £

// Get decimal places
$decimals = Currency::get_decimals( 'USD' );  // 2

// Sanitize user input
$currency = Currency::sanitize( $_POST['currency'] );
if ( $currency ) {
	// Valid, sanitized currency code
}
```

## Select/Dropdown Options

```php
// Get options for Gutenberg/React components
$options = Currency::get_options();
/* Returns:
[
    ['value' => 'USD', 'label' => 'USD - $'],
    ['value' => 'EUR', 'label' => 'EUR - €'],
    // ... all 135 currencies
]
*/

// Use in WordPress admin
?>
<select name="currency">
	<?php foreach ( Currency::get_options() as $option ): ?>
        <option value="<?php echo esc_attr( $option['value'] ); ?>">
			<?php echo esc_html( $option['label'] ); ?>
        </option>
	<?php endforeach; ?>
</select>
```

## Zero-Decimal Currencies

These currencies don't use decimal places:
- **JPY** - Japanese Yen
- **KRW** - South Korean Won
- **CLP** - Chilean Peso
- **TWD** - New Taiwan Dollar
- **ISK** - Icelandic Króna
- **HUF** - Hungarian Forint
- **PYG** - Paraguayan Guarani
- **UGX** - Ugandan Shilling
- **VND** - Vietnamese Dong
- **VUV** - Vanuatu Vatu
- **RWF** - Rwandan Franc
- **BIF** - Burundian Franc
- **DJF** - Djiboutian Franc
- **GNF** - Guinean Franc
- **KMF** - Comorian Franc
- **MGA** - Malagasy Ariary
- **XAF** - Central African CFA Franc
- **XOF** - West African CFA Franc
- **XPF** - CFP Franc

## Three-Decimal Currencies

These currencies use three decimal places:
- **BHD** - Bahraini Dinar
- **JOD** - Jordanian Dinar
- **KWD** - Kuwaiti Dinar
- **OMR** - Omani Rial
- **TND** - Tunisian Dinar

## Stripe Integration Example

```php
// Processing a payment
$amount   = 99.99; // User enters $99.99
$currency = 'USD';

// Convert to Stripe format
$stripe_amount = Currency::to_smallest_unit( $amount, $currency );

// Create Stripe charge
$charge = \Stripe\Charge::create( [
	'amount'   => $stripe_amount,  // 9999 (cents)
	'currency' => strtolower( $currency ),
	// ...
] );

// Display formatted amount to user
echo 'Charged: ' . Currency::format( $charge->amount, $currency );
```

## WooCommerce Integration Example

```php
// Add currency formatting to WooCommerce
add_filter( 'woocommerce_price', function ( $price, $amount ) {
	$currency = get_woocommerce_currency();
	$cents    = Currency::to_smallest_unit( $amount, $currency );

	return Currency::format( $cents, $currency );
}, 10, 2 );
```

## API Reference

| Method | Description | Return Type |
|--------|-------------|-------------|
| `format($amount, $currency)` | Format with symbol | `string` |
| `format_plain($amount, $currency)` | Format without symbol | `string` |
| `format_with_code($amount, $currency)` | Format with currency code | `string` |
| `to_smallest_unit($amount, $currency)` | Convert to Stripe units | `int` |
| `from_smallest_unit($amount, $currency)` | Convert from Stripe units | `float` |
| `get_config($currency)` | Get currency configuration | `?array` |
| `get_symbol($currency)` | Get currency symbol | `string` |
| `get_decimals($currency)` | Get decimal places | `int` |
| `is_supported($currency)` | Check if supported | `bool` |
| `is_zero_decimal($currency)` | Check if zero-decimal | `bool` |
| `get_all()` | Get all currencies | `array` |
| `get_options()` | Get select options | `array` |
| `sanitize($currency)` | Validate and sanitize | `?string` |

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher (for escaping functions)

## License

GPL-2.0-or-later

## Support

- [Documentation](https://github.com/arraypress/wp-currencies)
- [Issue Tracker](https://github.com/arraypress/wp-currencies/issues)