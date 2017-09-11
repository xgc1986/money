<?php
declare(strict_types=1);

namespace Tests\Unit;

use Xgc\Money\Currency;
use Xgc\Money\Test\TestCase;

/**
 * Class CurrencyTest
 *
 * @package Tests\Unit
 */
class CurrencyTest extends TestCase
{
    public function testGetConversion()
    {
        $currency = Currency::get('USD');
        self::assertTrue(\is_numeric($currency->getConversion(Currency::get('BTC'))));
    }

    public function testGetScale()
    {
        $currency = Currency::get('USD');
        self::assertSame(2, $currency->getScale());

        $currency = Currency::get('BTC');
        self::assertSame(8, $currency->getScale());
    }

    public function testGetConversions()
    {
        $currency   = Currency::get('USD');
        $currencies = [];
        foreach (Currency::CURRENCIES as $key => $curr) {
            $currencies[] = Currency::get((string) $key);
        }

        $ret = $currency->getConversions($currencies);
        self::assertCount(20, $ret);
    }

    public function testGetMinimumAmount()
    {
        $currency = Currency::get('USD');
        self::assertTrue($currency->getMinimumAmount()->isEqualTo('0.01'));

        $currency = Currency::get('BTC');
        self::assertTrue($currency->getMinimumAmount()->isEqualTo('0.00000001'));
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidCurrencyException
     */
    public function testInvalidCurrency()
    {
        Currency::get('da coin');
    }
}
