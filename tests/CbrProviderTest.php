<?php

namespace Currency\Test;

use Currency\CbrProvider;
use Currency\CommonProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class CbrProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCurrencyRateProvider
     */
    public function testGetCurrencyRate($cbrProvider)
    {
        $this->assertSame(56.6616, $cbrProvider->getCurrencyRate(strtotime('03-03-2018'), CommonProvider::USD));
    }

    /**
     * @dataProvider getCurrencyRateExceptionProvider
     */
    public function testGetCurrencyRateException($cbrProvider)
    {
        $this->expectException('Currency\Exception\CbrProviderException');
        $this->assertSame(56.1, $cbrProvider->getCurrencyRate(strtotime('03-03-2088'), CommonProvider::USD));
    }

    public function getCurrencyRateProvider()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '<?xml version="1.0" encoding="windows-1251"?>
<ValCurs ID="R01235" DateRange1="03.03.2018" DateRange2="03.03.2018" name="Foreign Currency Market Dynamic">
<Record Date="03.03.2018" Id="R01235"><Nominal>1</Nominal><Value>56,6616</Value></Record></ValCurs>'),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return [
            [new CbrProvider($client)],
        ];
    }

    public function getCurrencyRateExceptionProvider()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '<?xml version="1.0" encoding="windows-1251"?>
<ValCurs ID="R01235" DateRange1="03.03.2088" DateRange2="03.03.2088" name="Foreign Currency Market Dynamic"></ValCurs>'),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return [
            [new CbrProvider($client)],
        ];
    }
}