<?php
declare(strict_types=1);

namespace Xgc\Money\Currency;

use InvalidArgumentException;
use Xgc\Money\Currency;
use Xgc\Money\Money;

/**
 * Class USD
 *
 * @package Xgc\Money\Currency
 */
class USD extends Money
{
    /**
     * USD constructor.
     *
     * @param null $amount
     *
     * @throws InvalidArgumentException
     */
    public function __construct($amount = null)
    {
        parent::__construct(Currency::get('USD'), $amount);
    }
}
