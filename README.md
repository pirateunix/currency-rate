# CurrencyRate

Return json with average currency rate of USD and EUR at the required date. 
Date specified in timestamp. 
Now available two data providers with currency rate: www.cbr.ru and cash.rbc.ru

## Installation

Run in project folder
```
composer install
```

## Use

Example:
```
require_once 'vendor/autoload.php';

use \GuzzleHttp\Client;

$service = new Currency\CurrencyService();
$service->addCurrencyProvider(new Currency\CbrProvider(new Client()));
$service->addCurrencyProvider(new Currency\RbcProvider(new Client()));

echo $service->getCurrency(strtotime('03-03-2018'));
```

## Test
Just run:
```
vendor/bin/phpunit
```


