<?php
declare(strict_types=1);

namespace Xgc\Money\Currency;

use InvalidArgumentException;
use Xgc\Money\Currency;
use Xgc\Money\Money;

/**
 * Class EUR
 *
 * @package Xgc\Money\Currency
 */
class EUR extends Money
{
    /**
     * EUR constructor.
     *
     * @param null $amount
     *
     * @throws InvalidArgumentException
     */
    public function __construct($amount = null)
    {
        parent::__construct(Currency::get('EUR'), $amount);
    }
}
