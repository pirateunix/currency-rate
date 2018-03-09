<?php

namespace Currency;

use Currency\Exception\CbrProviderException;

/**
 * Class CbrProvider
 * @package currency
 */
class CbrProvider extends CommonProvider
{
    const BASE_URL = 'http://www.cbr.ru/scripts/XML_dynamic.asp?';
    const DATE_FORMAT = 'd/m/Y';

    private $currencyCodeMap = [
        CommonProvider::USD => 'R01235',
        CommonProvider::EUR => 'R01239'
    ];

    /**
     * @param string $currency
     * @param $timestamp
     * @throws CbrProviderException
     * @return float
     */
    public function getCurrencyRate($timestamp, $currency = CommonProvider::USD)
    {
        $date = date(self::DATE_FORMAT, $timestamp);
        $params = [
            'date_req1' => $date,
            'date_req2' => $date,
            'VAL_NM_RQ' => $this->currencyCodeMap[$currency],
        ];
        $decoded = new \SimpleXMLElement($this->getDataFromUrl(self::BASE_URL, $params));
        if (!isset($decoded->Record) || !isset($decoded->Record->Value)) {
            throw new CbrProviderException('Error in cbr service: no data for this date "' . $date . '"');
        }
        $rate = str_replace(',', '.', $decoded->Record->Value->__toString());
        return floatval($rate);
    }
}