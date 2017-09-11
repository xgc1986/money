<?php
declare(strict_types=1);

namespace Xgc\Money;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\ArithmeticException;
use Brick\Math\RoundingMode;
use InvalidArgumentException;
use RuntimeException;
use Serializable;
use Xgc\Money\Exception\InvalidComparisonException;
use Xgc\Money\Exception\NegativeMoneyException;

/**
 * Class Money
 *
 * @package Xgc\Money
 */
class Money implements Serializable
{

    /**
     * @var BigDecimal
     */
    protected $amount;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * Money constructor.
     *
     * @param Currency                 $currency
     * @param number|string|BigDecimal $amount
     *
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function __construct(Currency $currency, $amount = null)
    {
        $this->currency = $currency;
        $this->amount   = $this->parseValue($amount);
    }

    public function __clone()
    {
        $this->amount = clone $this->amount;
    }

    /**
     * @return BigDecimal
     */
    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    /**
     * @param BigDecimal $amount
     */
    public function setAmount(BigDecimal $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return Currency
     */
    public function getCurreny(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Money $money
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function add(Money $money): void
    {
        $money = $money->changeCurrency($this->currency);

        $this->amount = $this->amount->plus($money->getAmount());
    }

    /**
     * @param Money $money
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     * @throws NegativeMoneyException
     */
    public function substract(Money $money): void
    {
        $money = $money->changeCurrency($this->currency);

        $amount = $this->amount->minus($money->getAmount());

        if ($amount->isLessThan(BigDecimal::zero())) {
            throw new NegativeMoneyException();
        }

        $this->amount = $amount;
    }

    /**
     * @param number|string|BigDecimal $amount
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function multiply($amount): void
    {
        $amount = $this->parseValue($amount);

        $this->amount = $this->amount->multipliedBy($amount);
    }

    /**
     * @param int $n
     *
     * @return Money[]
     * @throws ArithmeticException
     * @throws InvalidArgumentException
     */
    public function allocate(int $n): array
    {
        $lowResult  = new self($this->currency, $this->amount->dividedBy($n, $this->getScale(), RoundingMode::DOWN));
        $highResult = new self(
            $this->currency,
            $this->amount->dividedBy($n, $this->getScale(), RoundingMode::DOWN)->plus($this->getMinimumAmount())
        );

        $result    = [];
        $remainder = $this->amount->remainder($n)->toInt();

        for ($i = 0; $i < $remainder; $i++) {
            $result[] = clone $highResult;
        }

        for ($i = $remainder; $i < $n; $i++) {
            $result[] = clone $lowResult;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getScale(): int
    {
        return $this->currency->getScale();
    }

    /**
     * @return BigDecimal
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function getMinimumAmount(): BigDecimal
    {
        return $this->currency->getMinimumAmount();
    }

    /**
     * @param Money $money
     * @param bool  $conversion
     *
     * @return bool
     * @throws InvalidComparisonException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function ge(Money $money, bool $conversion = false): bool
    {
        return $this->g($money, $conversion) || $this->e($money, $conversion);
    }

    /**
     * @param Money $money
     * @param bool  $conversion
     *
     * @return bool
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     * @throws InvalidComparisonException
     */
    public function g(Money $money, bool $conversion = false): bool
    {
        if (!$conversion) {
            $this->checkSameCurrency($money);
        }

        return $this->amount->isGreaterThan($money->changeCurrency($this->currency)->getAmount());
    }

    /**
     * @param Money $money
     * @param bool  $conversion
     *
     * @return bool
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     * @throws InvalidComparisonException
     */
    public function e(Money $money, bool $conversion = false): bool
    {
        if (!$conversion) {
            $this->checkSameCurrency($money);
        }

        return $this->amount->isEqualTo($money->changeCurrency($this->currency)->getAmount());
    }

    /**
     * @param Money $money
     * @param bool  $conversion
     *
     * @return bool
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     * @throws InvalidComparisonException
     */
    public function l(Money $money, bool $conversion = false): bool
    {
        if (!$conversion) {
            $this->checkSameCurrency($money);
        }

        return $this->amount->isLessThan($money->changeCurrency($this->currency)->getAmount());
    }

    /**
     * @param Money $money
     * @param bool  $conversion
     *
     * @return bool
     * @throws InvalidComparisonException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws ArithmeticException
     */
    public function le(Money $money, bool $conversion = false): bool
    {
        return $this->l($money, $conversion) || $this->e($money, $conversion);
    }

    /**
     * @param Currency $currency
     *
     * @return Money
     * @throws ArithmeticException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function changeCurrency(Currency $currency): Money
    {
        if ($currency === $this->getCurreny()) {
            return $this;
        }

        $change = $this->currency->getConversion($currency);

        $money = new self($currency);
        $money->setAmount($this->getAmount()->multipliedBy($change));

        return $money;
    }

    /**
     * @param number|string|BigDecimal $amount
     *
     * @return BigDecimal
     * @throws ArithmeticException
     * @throws InvalidArgumentException
     */
    private function parseValue($amount): BigDecimal
    {
        if ($amount instanceof BigDecimal) {
            return $amount;
        }

        return BigDecimal::of($amount ?? 0);
    }

    /**
     * @param Money $money
     *
     * @throws InvalidComparisonException
     */
    public function checkSameCurrency(Money $money)
    {
        if ($this->currency !== $money->currency) {
            throw new InvalidComparisonException();
        }
    }

    /**
     * String representation of object
     *
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize(): string
    {
        return \serialize([
            $this->amount,
            $this->currency->getName()
        ]);
    }

    /**
     * Constructs the object
     *
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @throws \Xgc\Money\Exception\InvalidCurrencyException
     * @since 5.1.0
     */
    public function unserialize($serialized): void
    {
        $curr = null;

        [
            $this->amount,
            $curr
        ] = \unserialize($serialized, []);

        $this->currency = Currency::get($curr);
    }
}
