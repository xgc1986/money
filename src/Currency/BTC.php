<?php
declare(strict_types=1);

namespace Xgc\Money\Currency;

use InvalidArgumentException;
use Xgc\Money\Currency;
use Xgc\Money\Money;

/**
 * Class BTC
 *
 * @package Xgc\Money\Currency
 */
class BTC extends Money
{
    /**
     * BTC constructor.
     *
     * @param null $amount
     *
     * @throws InvalidArgumentException
     */
    public function __construct($amount = null)
    {
        parent::__construct(Currency::get('BTC'), $amount);
    }
}
