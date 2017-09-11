<?php
declare(strict_types=1);

namespace Xgc\Money\Exception;

use RuntimeException;
use Throwable;

/**
 * Class NegativeMoneyException
 *
 * @package Xgc\Money\Exception
 */
class NegativeMoneyException extends RuntimeException
{

    /**
     * NegativeMoneyException constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('The amount of money cannot be negative', 0, $previous);
    }
}
