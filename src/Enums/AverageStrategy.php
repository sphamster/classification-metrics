<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Enums;

use Sphamster\ClassificationMetrics\Calculators\Average;
use Sphamster\ClassificationMetrics\Contracts\Calculator;

/**
 * Enumeration for different averaging strategies in multiclass classification.
 */
enum AverageStrategy: string
{
    /**
     * Calculate metrics for each label and find their unweighted mean.
     * This does not take label imbalance into account.
     */
    case MACRO = 'macro';

    /**
     * Calculate metrics globally by counting the total true positives,
     * false negatives and false positives.
     */
    case MICRO = 'micro';

    /**
     * Calculate metrics for each label and find their average weighted
     * by support (the number of true instances for each label).
     */
    case WEIGHTED = 'weighted';


    public function toCalculator(): Calculator
    {
        return match ($this) {
            self::MACRO => new Average\Macro(),
            self::WEIGHTED =>  new Average\Weighted(),
            self::MICRO =>  new Average\Micro(),
        };
    }
}
