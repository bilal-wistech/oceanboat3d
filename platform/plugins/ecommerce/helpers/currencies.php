<?php

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Botble\Ecommerce\Models\Currency;
use Botble\Ecommerce\Facades\CurrencyFacade;
use Botble\Ecommerce\Supports\CurrencySupport;
use Botble\Ecommerce\Repositories\Interfaces\CurrencyInterface;

if (!function_exists('format_price')) {
    /**
     * @param float|int|null $price
     * @param Currency|null|string $currency
     * @param bool $withoutCurrency
     * @param bool $useSymbol
     * @return string
     */
    function format_price($price, $currency = null, bool $withoutCurrency = false, bool $useSymbol = true): string
    {
        // Define API settings
        $apiKey = '70b023dbad1be2854385410e';
        $baseUrl = 'https://v6.exchangerate-api.com/v6/';
        $defaultCurrency = cms_currency()->getDefaultCurrency()->title;

        // Get the currency for conversion
        $currency = $currency ?? get_application_currency();
        if (!$currency) {
            return human_price_text($price, $currency);
        }
        $currencyTitle = $currency->title;

        // Check if the currency is the default currency
        if ($currencyTitle === $defaultCurrency) {
            return $withoutCurrency ? human_price_text($price, $currency) : human_price_text($price, $currency) . ' ' . $currency->symbol;
        }

        // Try to get the conversion rate from cache or API
        $cacheKey = "exchange_rate_{$defaultCurrency}_{$currencyTitle}";
        $conversionRate = Cache::get($cacheKey);

        if (!$conversionRate) {
            try {
                $client = new Client();
                $url = "{$baseUrl}{$apiKey}/latest/{$defaultCurrency}";
                $response = $client->get($url);
                $data = json_decode($response->getBody(), true);

                if ($data['result'] === 'success') {
                    if (isset($data['conversion_rates'][$currencyTitle])) {
                        $conversionRate = ($data['conversion_rates'][$currencyTitle]);
                        Cache::put($cacheKey, $conversionRate, 3600); // Cache for 1 hour
                    } else {
                        throw new Exception("Conversion rate for {$currencyTitle} not found.");
                    }
                } else {
                    throw new Exception("Failed to retrieve exchange rates.");
                }
            } catch (Exception $e) {
                Log::error('Currency conversion error: ' . $e->getMessage());
                return human_price_text($price, $currency);
            }
        }

        // Perform the price conversion
        $price = $price * ($conversionRate - 0.0005);

        // Format the output
        if ($withoutCurrency) {
            return human_price_text($price, $currency);
        }

        if ($useSymbol && $currency->is_prefix_symbol) {
            $space = get_ecommerce_setting('add_space_between_price_and_currency', 0) == 1 ? ' ' : '';
            return $currency->symbol . $space . human_price_text($price, $currency);
        }

        return human_price_text($price, $currency) . ' ' . $currency->symbol;
    }
}

if (!function_exists('format_price_cart')) {
    /**
     * @param float|int|null $price
     * @param Currency|null|string $currency
     * @param bool $withoutCurrency
     * @param bool $useSymbol
     * @return string
     */
    function format_price_cart($price, $currency = null, bool $withoutCurrency = false, bool $useSymbol = true): string
    {
        // Define API settings
        $apiKey = '70b023dbad1be2854385410e';
        $baseUrl = 'https://v6.exchangerate-api.com/v6/';
        $defaultCurrency = cms_currency()->getDefaultCurrency()->title;

        // Get the currency for conversion
        $currency = $currency ?? get_application_currency();
        if (!$currency) {
            return human_price_text($price, $currency);
        }
        $currencyTitle = $currency->title;

        // Check if the currency is the default currency
        if ($currencyTitle === $defaultCurrency) {
            return $withoutCurrency ? human_price_text($price, $currency) : human_price_text($price, $currency) . ' ' . $currency->symbol;
        }

        // Try to get the conversion rate from cache or API
        $cacheKey = "exchange_rate_{$defaultCurrency}_{$currencyTitle}";
        $conversionRate = Cache::get($cacheKey);

        if (!$conversionRate) {
            try {
                $client = new Client();
                $url = "{$baseUrl}{$apiKey}/latest/{$defaultCurrency}";
                $response = $client->get($url);
                $data = json_decode($response->getBody(), true);

                if ($data['result'] === 'success') {
                    if (isset($data['conversion_rates'][$currencyTitle])) {
                        $conversionRate = ($data['conversion_rates'][$currencyTitle]);
                        Cache::put($cacheKey, $conversionRate, 3600); // Cache for 1 hour
                    } else {
                        throw new Exception("Conversion rate for {$currencyTitle} not found.");
                    }
                } else {
                    throw new Exception("Failed to retrieve exchange rates.");
                }
            } catch (Exception $e) {
                Log::error('Currency conversion error: ' . $e->getMessage());
                return human_price_text($price, $currency);
            }
        }

        // Perform the price conversion
        $price = $price * ($conversionRate - 0.0005);

        // Format the output
        if ($withoutCurrency) {
            return human_price_text($price, $currency);
        }

        if ($useSymbol && $currency->is_prefix_symbol) {
            $space = get_ecommerce_setting('add_space_between_price_and_currency', 0) == 1 ? ' ' : '';
            return $currency->symbol . $space . human_price_text($price, $currency);
        }

        return human_price_text($price, $currency) . ' ' . $currency->symbol;
    }
}

if (!function_exists('human_price_text_cart')) {
    /**
     * @param float|null|mixed $price
     * @param Currency|null|string $currency
     * @param string $priceUnit
     * @return string
     */
    function human_price_text_cart($price, $currency = null, string $priceUnit = ''): string
    {
        $numberAfterDot = ($currency instanceof Currency) ? $currency->decimals : 0;

        if (config('plugins.ecommerce.general.display_big_money_in_million_billion')) {
            if ($price >= 1000000 && $price < 1000000000) {
                $priceUnit = __('million') . ' ' . $priceUnit;
                $numberAfterDot = strlen(substr(strrchr($price, '.'), 1));
            } elseif ($price >= 1000000000) {
                $priceUnit = __('billion') . ' ' . $priceUnit;
                $numberAfterDot = strlen(substr(strrchr($price, '.'), 1));
            }
        }

        if (is_numeric($price)) {
            $price = preg_replace('/[^0-9,.]/s', '', $price);
        }

        $decimalSeparator = get_ecommerce_setting('decimal_separator', '.');

        if ($decimalSeparator == 'space') {
            $decimalSeparator = ' ';
        }

        $thousandSeparator = get_ecommerce_setting('thousands_separator', ',');

        if ($thousandSeparator == 'space') {
            $thousandSeparator = ' ';
        }

        // $price = number_format(
        //     $price,
        //     (int)$numberAfterDot,
        //     $decimalSeparator,
        //     $thousandSeparator
        // );

        $space = get_ecommerce_setting('add_space_between_price_and_currency', 0) == 1 ? ' ' : null;

        return $price . $space . ($priceUnit ?: '');
    }
}

if (!function_exists('human_price_text')) {
    /**
     * @param float|null|mixed $price
     * @param Currency|null|string $currency
     * @param string $priceUnit
     * @return string
     */
    function human_price_text($price, $currency, string $priceUnit = ''): string
    {
        $numberAfterDot = ($currency instanceof Currency) ? $currency->decimals : 0;

        if (config('plugins.ecommerce.general.display_big_money_in_million_billion')) {
            if ($price >= 1000000 && $price < 1000000000) {
                $price = round($price / 1000000, 2) + 0;
                $priceUnit = __('million') . ' ' . $priceUnit;
                $numberAfterDot = strlen(substr(strrchr($price, '.'), 1));
            } elseif ($price >= 1000000000) {
                $price = round($price / 1000000000, 2) + 0;
                $priceUnit = __('billion') . ' ' . $priceUnit;
                $numberAfterDot = strlen(substr(strrchr($price, '.'), 1));
            }
        }

        if (is_numeric($price)) {
            $price = preg_replace('/[^0-9,.]/s', '', $price);
        }

        $decimalSeparator = get_ecommerce_setting('decimal_separator', '.');

        if ($decimalSeparator == 'space') {
            $decimalSeparator = ' ';
        }

        $thousandSeparator = get_ecommerce_setting('thousands_separator', ',');

        if ($thousandSeparator == 'space') {
            $thousandSeparator = ' ';
        }

        $price = number_format(
            $price,
            (int) $numberAfterDot,
            $decimalSeparator,
            $thousandSeparator
        );

        $space = get_ecommerce_setting('add_space_between_price_and_currency', 0) == 1 ? ' ' : null;

        return $price . $space . ($priceUnit ?: '');
    }
}

if (!function_exists('get_current_exchange_rate')) {
    /**
     * @param null $currency
     */
    function get_current_exchange_rate($currency = null)
    {
        if (!$currency) {
            $currency = get_application_currency();
        } elseif (!$currency instanceof Currency) {
            $currency = app(CurrencyInterface::class)->getFirstBy(['id' => $currency]);
        }

        if (!$currency->is_default && $currency->exchange_rate > 0) {
            return $currency->exchange_rate;
        }

        return 1;
    }
}

if (!function_exists('cms_currency')) {
    /**
     * @return CurrencySupport
     */
    function cms_currency(): CurrencySupport
    {
        return CurrencyFacade::getFacadeRoot();
    }
}

if (!function_exists('get_all_currencies')) {
    /**
     * @return Collection
     */
    function get_all_currencies(): Collection
    {
        return cms_currency()->currencies();
    }
}

if (!function_exists('get_application_currency')) {
    /**
     * @return Currency|null
     */
    function get_application_currency(): ?Currency
    {
        $currency = cms_currency()->getApplicationCurrency();

        if (is_in_admin() || !$currency) {
            $currency = cms_currency()->getDefaultCurrency();
        }

        return $currency;
    }
}

if (!function_exists('get_application_currency_id')) {
    /**
     * @return int|null
     */
    function get_application_currency_id(): ?int
    {
        return get_application_currency()->id;
    }
}
