<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Contracts;

use Sphamster\ClassificationMetrics\ConfusionMatrix;

interface Calculator
{
    /**
     * @param array<string, float> $measures
     */
    public function calculate(ConfusionMatrix $confusion_matrix, array $measures): float;
}
