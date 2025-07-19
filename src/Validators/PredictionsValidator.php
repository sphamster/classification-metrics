<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Validators;

use InvalidArgumentException;
use Sphamster\ClassificationMetrics\Contracts\Validator;

class PredictionsValidator implements Validator
{
    /**
     * Validates the true and predicted labels data.
     *
     * This method checks that both true and predicted labels arrays are not empty
     * and that they are consistent with each other (same length and all predicted
     * labels exist in true labels).
     *
     * @param array<array<string>> $data An associative array containing 'true_labels' and 'predicted_labels' keys
     * @throws InvalidArgumentException If labels are empty or inconsistent
     */
    public static function validate(array $data): void
    {
        if (self::hasEmptyLabels($data['true_labels']) || self::hasEmptyLabels($data['predicted_labels'])) {

            throw new InvalidArgumentException('Missing or empty labels');
        }

        if ( ! self::hasConsistentLabels(...$data)) {
            throw new InvalidArgumentException('Each label must be present in true labels');
        }
    }

    /**
     * Checks if a labels array is empty.
     *
     * This helper method determines whether the provided array of labels is empty.
     *
     * @param array<string> $labels The array of labels to check
     * @return bool True if the labels array is empty, false otherwise
     */
    protected static function hasEmptyLabels(array $labels): bool
    {
        return $labels === [];
    }

    /**
     * Checks if predicted labels are consistent with true labels.
     *
     * This method verifies that the predicted labels array has the same length as the true labels array
     * and that all unique predicted labels exist in the set of unique true labels.
     *
     * @param array<string> $true_labels The ground truth labels
     * @param array<string> $predicted_labels The predicted labels
     * @throws InvalidArgumentException If the arrays have different lengths
     * @return bool True if the labels are consistent, false otherwise
     */
    protected static function hasConsistentLabels(array $true_labels, array $predicted_labels): bool
    {
        // get only unique labels
        $unique_true = array_unique($true_labels);
        $unique_predicted = array_unique($predicted_labels);

        if (count($true_labels) !== count($predicted_labels)) {
            throw new InvalidArgumentException('true and predicted labels must have same labels');
        }

        return count(array_diff($unique_predicted, $unique_true)) === 0;
    }
}
