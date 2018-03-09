<?php

namespace Currency;

use Currency\Exception\RbcProviderException;

/**
 * Class RbcProvider
 * @package currency
 */
class RbcProvider extends CommonProvider
{
    const BASE_URL = 'https://cash.rbc.ru/cash/json/converter_currency_rate/?';
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @param string $currency
     * @param $timestamp
     * @throws RbcProviderException
     * @return float
     */
    public function getCurrencyRate($timestamp, $currency = CommonProvider::USD)
    {
        $date = date(self::DATE_FORMAT, $timestamp);
        $params = [
            'currency_from' => $currency,
            'currency_to' => 'RUR',
            'source' => 'cbrf',
            'sum' => '1',
            'date' => $date,
        ];
        $decoded = json_decode($this->getDataFromUrl(self::BASE_URL, $params), true);
        if (isset($decoded['status']) && $decoded['status'] !== 200) {
            throw new RbcProviderException('error in rbc service: ' . $decoded['status']);
        }
        if (!isset($decoded['data']) || !isset($decoded['data']['sum_result'])) {
            throw new RbcProviderException('Error in cbr service: no data for this date "' . $date . '"');
        }
        return (float)$decoded['data']['sum_result'];
    }
}