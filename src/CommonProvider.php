<?php

namespace Currency;

use Currency\Exception\CommonProviderException;
use \GuzzleHttp\Client;

/**
 * Class CommonProvider
 * @package currency
 */
abstract class CommonProvider
{
    const USD = 'USD';
    const EUR = 'EUR';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public abstract function getCurrencyRate($timestamp, $currency);

    /**
     * Get data from url, using curl
     * @param $url
     * @param $params
     * @throws CommonProviderException
     * @return mixed
     */
    protected function getDataFromUrl($url, $params = null)
    {
        try {
            $res = $this->client->request('GET', $url, ['query' => $params]);
            if ($res->getStatusCode() !== 200) {
                throw new CommonProviderException('Error. Expected response http status: 200, but received ' .
                    $res->getStatusCode());
            }
        } catch (\Exception $e) {
            throw new CommonProviderException('Error, while attempt to get data from url');
        }
        return $res->getBody();
    }

}