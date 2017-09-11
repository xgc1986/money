<?php
declare(strict_types=1);

namespace Xgc\Money\Exception;

use Throwable;

/**
 * Class InvalidCurrencyException
 *
 * @package Xgc\PhpConfig\Exception
 */
class InvalidCurrencyException extends \InvalidArgumentException
{
    /**
     * InvalidCurrencyException constructor.
     *
     * @param string         $currency
     * @param Throwable|null $previous
     */
    public function __construct($currency, Throwable $previous = null)
    {
        parent::__construct("Invalid currency '$currency'", 0, $previous);
    }
}
