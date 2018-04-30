<?php

namespace Lamoda\Metric\Storage\Tests;

use Lamoda\Metric\Storage\MetricMutator;
use Lamoda\Metric\Storage\MetricStorageInterface;
use Lamoda\Metric\Storage\MutableMetricInterface;
use PHPUnit\Framework\TestCase;

final class MetricMutatorTest extends TestCase
{
    private const TAG = ['tag' => 'value'];
    private const NAME = 'test';

    public function testMutatorMutatorAdjustsFoundMetric(): void
    {
        $metric = $this->createMock(MutableMetricInterface::class);

        $storage = $this->createMock(MetricStorageInterface::class);
        $storage->expects($this->once())->method('findMetric')
            ->with(self::NAME, self::TAG)
            ->willReturn($metric);

        $metric->expects($this->once())->method('adjust')->with(5.0);

        $mutator = new MetricMutator($storage);
        $mutator->adjustMetricValue(5.0, self::NAME, self::TAG);
    }

    public function testMutatorMutatorAdjustsUnknownCreatedMetric(): void
    {
        $metric = $this->createMock(MutableMetricInterface::class);

        $storage = $this->createMock(MetricStorageInterface::class);
        $storage->expects($this->once())->method('findMetric')
            ->with(self::NAME, self::TAG)
            ->willReturn(null);
        $storage->expects($this->once())->method('createMetric')
            ->with(self::NAME, 0, self::TAG)
            ->willReturn($metric);

        $metric->expects($this->once())->method('adjust')->with(5.0);

        $mutator = new MetricMutator($storage);
        $mutator->adjustMetricValue(5.0, self::NAME, self::TAG);
    }

    public function testMutatorMutatorUpdatesFoundMetric(): void
    {
        $metric = $this->createMock(MutableMetricInterface::class);

        $storage = $this->createMock(MetricStorageInterface::class);
        $storage->expects($this->once())->method('findMetric')
            ->with(self::NAME, self::TAG)
            ->willReturn($metric);

        $metric->expects($this->once())->method('setValue')->with(5.0);

        $mutator = new MetricMutator($storage);
        $mutator->setMetricValue(5.0, self::NAME, self::TAG);
    }

    public function testMutatorMutatorUpdatesUnknownCreatedMetric(): void
    {
        $metric = $this->createMock(MutableMetricInterface::class);

        $storage = $this->createMock(MetricStorageInterface::class);
        $storage->expects($this->once())->method('findMetric')
            ->with(self::NAME, self::TAG)
            ->willReturn(null);
        $storage->expects($this->once())->method('createMetric')
            ->with(self::NAME, 0, self::TAG)
            ->willReturn($metric);

        $metric->expects($this->once())->method('setValue')->with(5.0);

        $mutator = new MetricMutator($storage);
        $mutator->setMetricValue(5.0, self::NAME, self::TAG);
    }
}
