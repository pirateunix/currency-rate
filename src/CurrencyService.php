<?php

namespace Currency;

use Currency\Exception\CurrencyServiceException;

/**
 * Class CurrencyService
 * @package currency
 */
class CurrencyService
{
    /**
     * @var CommonProvider[]
     */
    private $providers;

    /**
     * Get average currency rate of USD and EUR in date
     * @param null $timestamp
     * @throws CurrencyServiceException
     * @return string
     */
    public function getCurrency($timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        } elseif (strtotime(date('d-m-Y H:i:s', $timestamp)) !== (int)$timestamp) {
            throw new CurrencyServiceException('Invalid param: timestamp');
        }

        if (!empty($this->providers)) {
            foreach ($this->providers as $provider) {
                $UsdRates[] = $provider->getCurrencyRate($timestamp, CommonProvider::USD);
                $EurRates[] = $provider->getCurrencyRate($timestamp, CommonProvider::EUR);
            }
        }
        $average = [
            'USD' => empty($UsdRates) ? null : array_sum($UsdRates) / count($UsdRates),
            'EUR' => empty($EurRates) ? null : array_sum($EurRates) / count($EurRates),
        ];
        return json_encode($average);
    }

    public function addCurrencyProvider(CommonProvider $provider)
    {
        $this->providers[] = $provider;
    }
}