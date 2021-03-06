<?php

namespace Lamoda\Metric\Common;

use Lamoda\Metric\Storage\MutableMetricInterface;

final class Metric implements MutableMetricInterface
{
    /** @var string */
    private $name;
    /** @var float */
    private $value;
    /** @var string[] */
    private $tags;

    /**
     * Metric constructor.
     *
     * @param string   $name
     * @param float    $value
     * @param string[] $tags
     */
    public function __construct(string $name, float $value, array $tags = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->tags = $tags;
    }

    /** {@inheritdoc} */
    public function getName(): string
    {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function resolve(): float
    {
        return $this->value;
    }

    /** {@inheritdoc} */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** {@inheritdoc} */
    public function adjust(float $delta): void
    {
        $this->value += $delta;
    }

    /** {@inheritdoc} */
    public function setValue(float $value): void
    {
        $this->value = $value;
    }
}
