# WordPress Currencies

A comprehensive WordPress library for Stripe currency formatting and conversion, supporting all 136 Stripe currencies
with proper decimal handling and locale-aware formatting.

## Features

- 💳 **All 136 Stripe Currencies** - Complete support for every Stripe-supported currency
- 🔢 **Smart Decimal Handling** - Correctly handles zero-decimal (JPY, KRW) and three-decimal (KWD, OMR) currencies
- 🌍 **Locale-Aware Formatting** - Customer-facing formatting with correct symbol position, separators, and spacing
- 💰 **Multiple Format Options** - Format with symbols, codes, or plain numbers
- 🔄 **Bidirectional Conversion** - Convert between decimal amounts and Stripe's smallest units
- ⚡ **Zero Dependencies** - Lightweight, only requires PHP 8.2+

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
// Defaults to WordPress site locale (get_locale())
echo Currency::format_localized( 9999, 'USD' );  // $99.99
echo Currency::format_localized( 9999, 'EUR' );  // 99,99 €
echo Currency::format_localized( 9999, 'PLN' );  // 99,99 zł
echo Currency::format_localized( 9999, 'BRL' );  // R$ 99,99

// Override locale if needed
echo Currency::format_localized( 9999, 'EUR', 'en_IE' );  // €99.99
```

Requires the PHP intl extension. Falls back to `format()` if unavailable.

## HTML Rendering

For admin tables and templates:

```php
// Render as HTML span
echo Currency::render( 9999, 'USD' );
// <span class="price">$99.99</span>

echo Currency::render( 9999, 'GBP' );
// <span class="price">£99.99</span>
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

## Currency Information

```php
// Get currency name
$name = Currency::get_name( 'USD' );  // US Dollar

// Get currency symbol
$symbol = Currency::get_symbol( 'GBP' );  // £

// Get decimal places
$decimals = Currency::get_decimals( 'USD' );  // 2

// Get the native locale for a currency
$locale = Currency::get_native_locale( 'EUR' );  // de_DE

// Get full configuration
$config = Currency::get_config( 'EUR' );
// Returns: ['name' => 'Euro', 'symbol' => '€', 'decimals' => 2, 'locale' => 'de_DE']

// Get currencies formatted for select dropdowns
$options = Currency::get_options();
// Returns: ['USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', ...]

// Check if currency is supported
if ( Currency::is_supported( 'USD' ) ) {
    // Valid Stripe currency
}

// Check if zero-decimal currency
if ( Currency::is_zero_decimal( 'JPY' ) ) {
    // Handle zero-decimal logic
}
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
// Currency data
$name     = get_currency_name( 'USD' );              // US Dollar
$symbol   = get_currency_symbol( 'GBP' );             // £
$decimals = get_currency_decimals( 'USD' );            // 2
$options  = get_currency_options();                     // ['USD' => 'USD — US Dollar', ...]

// Formatting
$price = format_currency( 9999, 'USD' );               // $99.99
$price = format_currency( 9999, 'EUR', 'de_DE' );      // 99,99 €
$plain = format_currency_plain( 9999, 'USD' );          // 99.99
$html  = render_currency( 9999, 'USD' );                // <span class="price">$99.99</span>
esc_currency_e( 9999, 'USD' );                          // Echoes: $99.99

// Unit conversion
$cents   = to_currency_cents( 19.99, 'USD' );           // 1999
$dollars = from_currency_cents( 1999, 'USD' );           // 19.99

// Validation
$supported = is_currency_supported( 'USD' );             // true
$zero_dec  = is_currency_zero_decimal( 'JPY' );          // true
```

## Zero-Decimal Currencies

These currencies don't use decimal places: BIF, CLP, DJF, GNF, JPY, KMF, KRW, MGA, PYG, RWF, VND, VUV, XAF, XOF, XPF.

## Special Case Currencies

Some currencies have quirks in Stripe's API that this library handles automatically:

- **ISK, UGX** — Logically zero-decimal but Stripe requires two-decimal representation for backward compatibility.
  Treated as two-decimal in the API.
- **HUF, TWD** — Zero-decimal for payouts only; charges accept two-decimal amounts. Treated as two-decimal in the API.

## Three-Decimal Currencies

These currencies use three decimal places: BHD, JOD, KWD, OMR, TND.

## API Reference

| Method                                          | Description                  | Return   |
|-------------------------------------------------|------------------------------|----------|
| **Currency Data**                               |                              |          |
| `all()`                                         | Get all currencies           | `array`  |
| `get_config($currency)`                         | Get currency configuration   | `?array` |
| `get_name($currency)`                           | Get currency name            | `string` |
| `get_symbol($currency)`                         | Get currency symbol          | `string` |
| `get_decimals($currency)`                       | Get decimal places           | `int`    |
| `get_native_locale($currency)`                  | Get native locale            | `string` |
| `get_options()`                                 | Get formatted select options | `array`  |
| **Formatting**                                  |                              |          |
| `format($amount, $currency)`                    | Format with symbol           | `string` |
| `format_localized($amount, $currency, $locale)` | Locale-aware format          | `string` |
| `format_plain($amount, $currency)`              | Format without symbol        | `string` |
| `format_with_code($amount, $currency)`          | Format with currency code    | `string` |
| `render($amount, $currency)`                    | Render as HTML span          | `string` |
| **Unit Conversion**                             |                              |          |
| `to_smallest_unit($amount, $currency)`          | Convert to Stripe units      | `int`    |
| `from_smallest_unit($amount, $currency)`        | Convert from Stripe units    | `float`  |
| **Validation**                                  |                              |          |
| `is_supported($currency)`                       | Check if supported           | `bool`   |
| `is_zero_decimal($currency)`                    | Check if zero-decimal        | `bool`   |

## Requirements

- PHP 8.2 or higher
- WordPress 6.0 or higher
- PHP intl extension (optional, for locale-aware formatting)

## License

GPL-2.0-or-later