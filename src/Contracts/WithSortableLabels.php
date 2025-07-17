<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Contracts;

interface WithSortableLabels
{
    /**
     * Sorts and filters unique labels.
     *
     * @param array<string> $labels The input array of labels to be sorted
     * @return array<string> Sorted array of unique labels
     */
    public static function sortLabels(array $labels): array;
}
