<?php
declare(strict_types=1);

namespace Tests\Unit;

use Xgc\Money\Currency;
use Xgc\Money\Currency\USD;
use Xgc\Money\Currency\EUR;
use Xgc\Money\Currency\BTC;
use Xgc\Money\Money;
use Xgc\Money\Test\TestCase;

/**
 * Class MoneyTest
 *
 * @package Tests\Unit
 */
class MoneyTest extends TestCase
{
    public function testConversion()
    {
        $money = new Money(Currency::get('USD'), 4000);
        $money->changeCurrency(Currency::get('BTC'));
        $money = $money->changeCurrency(Currency::get('BTC'));

        self::assertSame(Currency::get('BTC'), $money->getCurreny());
    }

    public function testClone()
    {
        $money  = new USD(100);
        $cloned = clone $money;

        self::assertNotSame($money, $cloned);
        self::assertNotSame($money->getAmount(), $cloned->getAmount());
        self::assertSame($money->getCurreny(), $cloned->getCurreny());
    }

    public function testOperations()
    {
        $money = new EUR(100);

        $money->add(new EUR(100));
        self::assertTrue((new EUR(200))->e($money));

        $money->substract(new EUR(100));
        self::assertTrue((new EUR(100))->e($money));

        $money->multiply(2);
        self::assertTrue((new EUR(200))->e($money));

        $allocations = $money->allocate(3);
        self::assertCount(3, $allocations);
        self::assertTrue((new EUR(66.67))->e($allocations[0]));
        self::assertTrue((new EUR(66.67))->e($allocations[1]));
        self::assertTrue((new EUR(66.66))->e($allocations[2]));

        self::assertTrue((new EUR(66.67))->le($allocations[0]));
        self::assertTrue((new EUR(66.67))->ge($allocations[0]));
        self::assertFalse((new EUR(66.67))->l($allocations[0]));
        self::assertFalse((new EUR(66.67))->g($allocations[0]));

        self::assertTrue((new BTC(1000))->ge($money, true));
        self::assertTrue((new BTC(1000))->g($money, true));
        self::assertFalse((new BTC(1000))->e($money, true));
        self::assertFalse((new BTC(1000))->l($money, true));
        self::assertFalse((new BTC(1000))->le($money, true));
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidComparisonException
     */
    public function testInvalidComparisonLE()
    {
        $eur = new EUR();
        $usd = new USD();

        $eur->le($usd);
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidComparisonException
     */
    public function testInvalidComparisonL()
    {
        $eur = new EUR();
        $usd = new USD();

        $eur->l($usd);
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidComparisonException
     */
    public function testInvalidComparisonE()
    {
        $eur = new EUR();
        $usd = new USD();

        $eur->e($usd);
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidComparisonException
     */
    public function testInvalidComparisonG()
    {
        $eur = new EUR();
        $usd = new USD();

        $eur->g($usd);
    }

    /**
     * @expectedException \Xgc\Money\Exception\InvalidComparisonException
     */
    public function testInvalidComparisonGE()
    {
        $eur = new EUR();
        $usd = new USD();

        $eur->ge($usd);
    }

    /**
     * @expectedException \Xgc\Money\Exception\NegativeMoneyException
     */
    public function testInvalidSubstraction()
    {
        $eur = new EUR(100);
        $eur2 = new EUR(102);

        $eur->substract($eur2);
    }

    public function testSerializable()
    {
        $btc = new BTC();
        $serialized = \serialize($btc);
        $btc2 = \unserialize($serialized, []);

        self::assertTrue($btc->e($btc2));
    }
}
