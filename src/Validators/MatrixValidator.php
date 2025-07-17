<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Validators;

use Sphamster\ClassificationMetrics\Contracts\Validator;
use Sphamster\ClassificationMetrics\Exceptions\SizeMismatchException;

class MatrixValidator implements Validator
{
    /**
     * Validates that the matrix dimensions match the number of labels.
     *
     * This method ensures that:
     * 1. The number of rows in the matrix equals the number of labels
     * 2. Each row has the same number of columns as there are labels (square matrix)
     *
     * @param array{labels: array<string>, matrix: array<array<int>>} $data An associative array containing 'labels' and 'matrix' keys
     * @throws SizeMismatchException If the matrix dimensions don't match the number of labels
     */
    public static function validate(array $data): void
    {
        if (count($data['labels']) !== count($data['matrix'])) {
            throw new SizeMismatchException('Matrix dimensions must match labels');
        }

        foreach ($data['matrix'] as $row) {
            if (count($row) !== count($data['labels'])) {
                throw new SizeMismatchException('Matrix must be square');
            }
        }
    }
}
