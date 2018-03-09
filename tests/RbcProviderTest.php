<?php

namespace Currency\Test;

use Currency\RbcProvider;
use Currency\CommonProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class RbcProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCurrencyRateProvider
     */
    public function testGetCurrencyRate($rbcProvider)
    {
        $this->assertSame(56.6616, $rbcProvider->getCurrencyRate(strtotime('2018-03-03'), CommonProvider::USD));
    }

    /**
     * @dataProvider getCurrencyRateExceptionProvider
     */
    public function testGetCurrencyRateException($rbcProvider)
    {
        $this->expectException('Currency\Exception\RbcProviderException');
        $this->assertSame(56.1, $rbcProvider->getCurrencyRate(strtotime('1955-03-03'), CommonProvider::EUR));
    }

    public function getCurrencyRateProvider()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"status": 200, "meta": {"sum_deal": 1.0, "source": "cbrf",
            "currency_from": "USD", "date": "2018-03-03", "currency_to": "RUR"}, "data": {"date": "2018-03-03",
            "sum_result": 56.6616, "rate1": 56.6616, "rate2": 0.0176}}'),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return [
            [new RbcProvider($client)],
        ];
    }

    public function getCurrencyRateExceptionProvider()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"status": 200, "meta": {"sum_deal": 1.0, "source": "cbrf",
            "currency_from": "EUR", "date": "1955-03-03", "currency_to": "RUR"}, "data": null}'),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return [
            [new RbcProvider($client)],
        ];
    }
}