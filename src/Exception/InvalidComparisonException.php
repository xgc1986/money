<?php
declare(strict_types=1);

namespace Xgc\Money\Exception;

use Throwable;

/**
 * Class InvalidComparisonException
 *
 * @package Xgc\Money\Exception
 */
class InvalidComparisonException extends \InvalidArgumentException
{
    /**
     * InvalidCurrencyException constructor.
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'A comparation without conversion must be done with money with the same currency',
            0,
            $previous
        );
    }
}
