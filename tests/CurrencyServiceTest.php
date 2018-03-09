<?php

namespace Currency\Test;

use Currency\CurrencyService;
use Currency\CommonProvider;
use Currency\Exception\CommonProviderException;

class CurrencyServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCurrencyRate()
    {
        $service = new CurrencyService();

        $mockProviderOne = $this->createMock(CommonProvider::class);
        $mockProviderOne->method('getCurrencyRate')->willReturn(56.1625, 68.5685);
        $mockProviderTwo = $this->createMock(CommonProvider::class);
        $mockProviderTwo->method('getCurrencyRate')->willReturn(56.9852, 68.1785);

        $service->addCurrencyProvider($mockProviderOne);
        $service->addCurrencyProvider($mockProviderTwo);

        $result = json_encode([
            'USD' => 56.57385,
            'EUR' => 68.3735,
        ]);
        $this->assertSame($result, $service->getCurrency(strtotime('2018-03-03')));
    }

    public function testGetCurrencyRateOne()
    {
        $service = new CurrencyService();

        $mockProvider = $this->createMock(CommonProvider::class);
        $mockProvider->method('getCurrencyRate')->willReturn(56.1625, 68.5685);

        $service->addCurrencyProvider($mockProvider);

        $result = json_encode([
            'USD' => 56.1625,
            'EUR' => 68.5685,
        ]);
        $this->assertSame($result, $service->getCurrency(strtotime('2018-03-03')));
    }

    public function testGetCurrencyRateException()
    {
        $service = new CurrencyService();
        $mockProvider = $this->createMock(CommonProvider::class);
        $mockProvider->method('getCurrencyRate')->will($this->throwException(new CommonProviderException));
        $service->addCurrencyProvider($mockProvider);

        $result = json_encode([
            'USD' => 56.1625,
            'EUR' => 68.5685,
        ]);
        $this->expectException('Currency\Exception\CommonProviderException');
        $this->assertSame($result, $service->getCurrency(strtotime('2018-03-03')));
    }
}