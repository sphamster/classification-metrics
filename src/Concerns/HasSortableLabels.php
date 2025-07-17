<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Concerns;

use InvalidArgumentException;

trait HasSortableLabels
{
    /**
     * Sorts and filters unique labels.
     *
     * This method takes an array of string labels, removes any duplicates,
     * and sorts them alphabetically before returning the result.
     *
     * @param array<string> $labels The input array of labels to be sorted
     * @return array<string> Sorted array of unique labels
     */
    public static function sortLabels(array $labels): array
    {
        // filters only unique labels
        $labels = array_unique([...$labels]);

        sort($labels);

        return $labels;
    }

    /**
     * Ensures consistency between provided labels and dataset labels.
     *
     * This method validates that the provided labels array contains exactly the same
     * labels as those found in the combined true and predicted labels arrays.
     * It throws exceptions if there are missing or extra labels.
     *
     * @param array<string> $true_labels The array of true labels from the dataset
     * @param array<string> $predicted_labels The array of predicted labels from the dataset
     * @param array<string> $labels The array of labels to validate against the dataset
     * @throws InvalidArgumentException If there are missing or extra labels
     */
    protected static function ensureLabelsAreConsistent(
        array $true_labels,
        array $predicted_labels,
        array $labels
    ): void {
        // merging all true and predicted labels with no duplicates
        $all_labels = array_unique([...$true_labels, ...$predicted_labels]);

        // merge all missing unique labels
        $missing = array_unique([
            ...array_diff($all_labels, $labels), // labels in dataset (all_labels) but not in $labels
            ...array_diff($labels, $all_labels)  // labels in $labels but not in dataset (all_labels)
        ]);

        if (array_diff($all_labels, $labels) !== []) {
            // some labels are in dataset but not in the sorted $labels array
            throw new InvalidArgumentException(
                sprintf(
                    'You must provide all labels in $labels array. Missing labels: [ %s ]',
                    implode(', ', $missing)
                )
            );
        }

        if (array_diff($labels, $all_labels) !== []) {
            // some labels are in $labels but not in the dataset
            throw new InvalidArgumentException(
                sprintf(
                    'You provided some extra labels in $labels array. Extra labels: [ %s ]',
                    implode(', ', $missing)
                )
            );
        }
    }
}
