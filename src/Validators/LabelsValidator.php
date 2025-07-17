<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Validators;

use Sphamster\ClassificationMetrics\Contracts\Validator;
use Sphamster\ClassificationMetrics\Exceptions\EmptyLabelException;

class LabelsValidator implements Validator
{
    /**
     * Validates that the labels array is not empty.
     *
     * This method checks if the 'labels' key in the provided data array
     * contains any elements. If the array is empty, it throws an EmptyLabelException.
     *
     * @param array<array<string>> $data An associative array containing a 'labels' key
     * @throws EmptyLabelException If the labels array is empty
     */
    public static function validate(array $data): void
    {
        if ($data['labels'] === []) {
            throw new EmptyLabelException('Labels cannot be empty');
        }
    }
}
