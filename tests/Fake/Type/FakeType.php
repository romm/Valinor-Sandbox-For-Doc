<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Tests\Fake\Type;

use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Type\Types\BooleanType;
use CuyZ\Valinor\Type\Types\ClassType;
use CuyZ\Valinor\Type\Types\MixedType;
use CuyZ\Valinor\Type\Types\NativeStringType;

use function class_exists;
use function in_array;

final class FakeType implements Type
{
    private static int $counter = 0;

    private string $name;

    private Type $matching;

    /** @var mixed[] */
    private array $accepting;

    public function __construct(string $name = null)
    {
        $this->name = $name ?? 'FakeType' . self::$counter++;
    }

    public static function from(string $raw): Type
    {
        if ($raw === 'string') {
            return NativeStringType::get();
        }

        if ($raw === 'bool') {
            return BooleanType::get();
        }

        if (class_exists($raw)) {
            return new ClassType($raw);
        }

        return new self();
    }

    /**
     * @param mixed ...$values
     */
    public static function thatWillAccept(...$values): self
    {
        $instance = new self();
        $instance->accepting = $values;

        return $instance;
    }

    public static function thatWillMatch(Type $other): self
    {
        $instance = new self();
        $instance->matching = $other;

        return $instance;
    }

    public function accepts($value): bool
    {
        return isset($this->accepting) && in_array($value, $this->accepting, true);
    }

    public function matches(Type $other): bool
    {
        return $other === $this
            || $other instanceof MixedType
            || $other === ($this->matching ?? null);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}