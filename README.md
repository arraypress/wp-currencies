# WordPress Currencies

A comprehensive WordPress library for Stripe currency formatting and conversion, supporting all 135 Stripe currencies
with proper decimal handling and locale-aware formatting.

## Features

- 💳 **All 135 Stripe Currencies** - Complete support for every Stripe-supported currency
- 🔢 **Smart Decimal Handling** - Correctly handles zero-decimal (JPY, KRW) and three-decimal (KWD, OMR) currencies
- 🌍 **Locale-Aware Formatting** - Customer-facing formatting with correct symbol position, separators, and spacing
- 💰 **Multiple Format Options** - Format with symbols, codes, plain numbers, or recurring intervals
- 🔄 **Bidirectional Conversion** - Convert between decimal amounts and Stripe's smallest units
- 🔍 **Object Resolution** - Auto-resolve currency and interval from data objects
- ⚡ **Zero Dependencies** - Lightweight, only requires PHP 7.4+

## Installation

```bash
composer require arraypress/wp-currencies
```

## Basic Formatting

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

## Locale-Aware Formatting

For customer-facing storefronts where correct symbol position and separators matter:

```php
// Uses each currency's default locale
echo Currency::format_localized( 9999, 'USD' );  // $99.99
echo Currency::format_localized( 9999, 'EUR' );  // 99,99 €
echo Currency::format_localized( 9999, 'PLN' );  // 99,99 zł
echo Currency::format_localized( 9999, 'BRL' );  // R$ 99,99

// Override locale if needed
echo Currency::format_localized( 9999, 'EUR', 'en_IE' );  // €99.99

// With recurring interval
echo Currency::format_localized_with_interval( 9999, 'EUR', 'month' );  // 99,99 € per month
```

Requires the PHP intl extension. Falls back to `format()` if unavailable.

## Recurring Intervals

```php
// Format with billing period
echo Currency::format_with_interval( 9999, 'USD', 'month' );      // $99.99 per month
echo Currency::format_with_interval( 9999, 'USD', 'year' );        // $99.99 per year
echo Currency::format_with_interval( 9999, 'USD', 'month', 3 );   // $99.99 every 3 months
echo Currency::format_with_interval( 9999, 'USD', 'month', 6 );   // $99.99 every 6 months

// Get interval text separately
echo Currency::get_interval_text( 'month' );     // per month
echo Currency::get_interval_text( 'month', 3 );  // every 3 months
```

## Conversion Methods

```php
// Convert decimal to Stripe's smallest unit
$cents  = Currency::to_smallest_unit( 99.99, 'USD' );   // 9999
$yen    = Currency::to_smallest_unit( 9999, 'JPY' );    // 9999 (no decimals)
$dinars = Currency::to_smallest_unit( 9.999, 'KWD' );   // 9999 (three decimals)

// Convert from Stripe's smallest unit to decimal
$dollars = Currency::from_smallest_unit( 9999, 'USD' );  // 99.99
$yen     = Currency::from_smallest_unit( 9999, 'JPY' );  // 9999.0
$dinars  = Currency::from_smallest_unit( 9999, 'KWD' );  // 9.999
```

## Object Resolution

Automatically resolve currency and recurring interval from data objects:

```php
// Resolve currency from an object (checks get_currency(), currency property, or default)
$currency = Currency::resolve( $price_row );           // "GBP"
$currency = Currency::resolve( $price_row, 'EUR' );    // fallback to EUR

// Resolve recurring interval from an object
$interval = Currency::resolve_interval( $price_row );
// Returns: ['interval' => 'month', 'interval_count' => 1]

// Render handles resolution automatically
echo Currency::render( $row->amount, $row );
// Outputs: <span class="price">£9.99 per month</span>
```

## HTML Rendering

For admin tables and templates:

```php
// Render with auto-resolved currency and interval
echo Currency::render( 9999, $item );
// <span class="price">$99.99 per month</span>

// Render with explicit currency
echo Currency::render( 9999, null, 'GBP' );
// <span class="price">£99.99</span>

// Returns null for invalid values
$html = Currency::render( 'invalid' );  // null
```

## Currency Information

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
// Returns: ['symbol' => '€', 'decimals' => 2, 'locale' => 'de_DE']

// Get individual properties
$symbol   = Currency::get_symbol( 'GBP' );    // £
$decimals = Currency::get_decimals( 'USD' );   // 2
$locale   = Currency::get_locale( 'EUR' );     // de_DE
```

## Stripe Integration Example

```php
// Processing a payment
$amount   = 99.99;
$currency = 'USD';

// Convert to Stripe format
$stripe_amount = Currency::to_smallest_unit( $amount, $currency );

// Create Stripe charge
$charge = \Stripe\Charge::create( [
    'amount'   => $stripe_amount,  // 9999 (cents)
    'currency' => strtolower( $currency ),
] );

// Display formatted amount to customer
echo Currency::format_localized( $charge->amount, $currency );
```

## Helper Functions

Global functions are available for convenience:

```php
// Format for display
$price = format_currency( 9999, 'USD' );              // $99.99
$plain = format_currency( 9999, 'USD', true );         // 99.99

// Locale-aware format for storefronts
$price = format_currency_localized( 9999, 'EUR' );     // 99,99 €

// Format with recurring interval
$sub = format_price_interval( 9999, 'USD', 'month' );  // $99.99 per month

// Render as HTML
$html = render_currency( 9999, $item );

// Get all currencies
$currencies = get_currency_options();

// Convert between formats
$cents   = to_currency_cents( 19.99, 'USD' );    // 1999
$dollars = from_currency_cents( 1999, 'USD' );    // 19.99
```

## Zero-Decimal Currencies

These currencies don't use decimal places: BIF, CLP, DJF, GNF, HUF, ISK, JPY, KMF, KRW, MGA, PYG, RWF, TWD, UGX, VND,
VUV, XAF, XOF, XPF.

## Three-Decimal Currencies

These currencies use three decimal places: BHD, JOD, KWD, OMR, TND.

## API Reference

| Method                                                                           | Description                   | Return    |
|----------------------------------------------------------------------------------|-------------------------------|-----------|
| `format($amount, $currency)`                                                     | Format with symbol            | `string`  |
| `format_localized($amount, $currency, $locale)`                                  | Locale-aware format           | `string`  |
| `format_plain($amount, $currency)`                                               | Format without symbol         | `string`  |
| `format_with_code($amount, $currency)`                                           | Format with currency code     | `string`  |
| `format_with_interval($amount, $currency, $interval, $count)`                    | Format with billing period    | `string`  |
| `format_localized_with_interval($amount, $currency, $interval, $count, $locale)` | Localized with billing period | `string`  |
| `get_interval_text($interval, $count)`                                           | Human-readable interval       | `string`  |
| `to_smallest_unit($amount, $currency)`                                           | Convert to Stripe units       | `int`     |
| `from_smallest_unit($amount, $currency)`                                         | Convert from Stripe units     | `float`   |
| `all()`                                                                          | Get all currencies            | `array`   |
| `get_config($currency)`                                                          | Get currency configuration    | `?array`  |
| `get_symbol($currency)`                                                          | Get currency symbol           | `string`  |
| `get_decimals($currency)`                                                        | Get decimal places            | `int`     |
| `get_locale($currency)`                                                          | Get default locale            | `string`  |
| `is_supported($currency)`                                                        | Check if supported            | `bool`    |
| `is_zero_decimal($currency)`                                                     | Check if zero-decimal         | `bool`    |
| `resolve($item, $default)`                                                       | Resolve currency from object  | `string`  |
| `resolve_interval($item)`                                                        | Resolve interval from object  | `array`   |
| `render($value, $item, $currency, $interval, $count)`                            | Render as HTML                | `?string` |

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- PHP intl extension (optional, for locale-aware formatting)

## License

GPL-2.0-or-later