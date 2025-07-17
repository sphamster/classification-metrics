<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Calculators\Average;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;
use Sphamster\ClassificationMetrics\Enums\Metric;

/**
 * Macro Calculator for classification metrics.
 *
 * Macro-averaging calculates metrics for each label and finds their unweighted mean.
 * This does not take label imbalance into account, giving equal weight to each class
 * regardless of the number of instances.
 */
final class Macro implements Calculator
{
    /**
     * Calculate macro-averaged metric.
     *
     * For any metric: macro-avg = (metric_class1 + metric_class2 + ... + metric_classN) / N
     * Each class contributes equally to the final score, regardless of support.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix (not used for macro-avg)
     * @param array<string,float> $measures Per-class metric values keyed by class label
     * @return float The macro-averaged metric value
     */
    public function calculate(ConfusionMatrix $confusion_matrix, array $measures): float
    {
        // Handle empty measures array
        if ($measures === []) {
            return 0.0;
        }

        // Calculate simple arithmetic mean of all class metrics
        return round(array_sum($measures) / count($measures), 4);
    }
}
