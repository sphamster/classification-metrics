<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Contracts;

use InvalidArgumentException;

interface Validator
{
    /**
     * Validates the provided data according to specific rules.
     *
     * This method is implemented by validator classes to check if the provided data
     * meets the required criteria. If validation fails, it should throw an exception
     * with a descriptive message.
     *
     * @param array<array<string>> $data The data to validate
     * @throws InvalidArgumentException If validation fails
     */
    public static function validate(array $data): void;
}
