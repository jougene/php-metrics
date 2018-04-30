<?php

namespace Lamoda\Metric\Responder\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Lamoda\Metric\Common\MetricInterface;
use Lamoda\Metric\Common\MetricSourceInterface;
use Lamoda\Metric\Responder\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class TelegrafJsonResponseFactory implements ResponseFactoryInterface
{
    private const CONTENT_TYPE = 'application/json';

    /** {@inheritdoc} */
    public function create(MetricSourceInterface $source, array $options = []): ResponseInterface
    {
        $normalizedGroups = [];
        $groups = $this->arrangeGroups($source, $options);
        foreach ($groups as $name => $group) {
            $normalizedGroups[$name] = $this->formatGroup($group, $options);
        }

        $normalizedGroups = array_values(array_filter($normalizedGroups));

        if (array_keys($normalizedGroups) === [0]) {
            $normalizedGroups = $normalizedGroups[0];
        }

        return new Response(
            200,
            ['Content-Type' => self::CONTENT_TYPE],
            json_encode($normalizedGroups)
        );
    }

    private function formatGroup(array $source, array $options): array
    {
        $result = [];
        $tags = [];
        $propagatedTags = $options['propagate_tags'] ?? null;
        $prefix = $options['prefix'] ?? '';

        foreach ($source as $metric) {
            $value = $metric->resolve();
            if (is_numeric($value)) {
                $value = (float) $value;
            }

            $result[$prefix . $metric->getName()] = $value;

            foreach ($metric->getTags() as $tag => $value) {
                if ($propagatedTags && \in_array($tag, $propagatedTags, true)) {
                    $tags[$tag] = (string) $value;
                }
            }
        }

        foreach ($tags as $tag => $value) {
            $result[$tag] = $value;
        }

        return $result;
    }

    /**
     * @param MetricSourceInterface $source
     * @param array                 $options
     *
     * @return MetricInterface[][]
     */
    private function arrangeGroups(MetricSourceInterface $source, array $options): array
    {
        $groups = [];
        if (empty($options['group_by_tags'])) {
            $groups[0] = iterator_to_array($source->getMetrics(), false);

            return $groups;
        }

        $tags = (array) $options['group_by_tags'];
        foreach ($source->getMetrics() as $metric) {
            $vector = [];
            foreach ($tags as $tag) {
                $vector[] = $metric->getTags()[$tag] ?? '__';
            }

            $groups[$this->createTagVector(...$vector)][] = $metric;
        }

        return array_values($groups);
    }

    private function createTagVector(string ...$values)
    {
        return implode(';', $values);
    }
}
