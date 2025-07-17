<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Calculators\Average;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;
use Sphamster\ClassificationMetrics\Enums\Metric;

/**
 * MicroAverage Calculator for classification metrics.
 *
 * Micro-averaging calculates metrics globally by counting the total true positives,
 * false negatives and false positives across all classes. This gives equal weight
 * to each instance, regardless of class, and is equivalent to accuracy for precision.
 */
final class Micro implements Calculator
{
    /**
     * Calculate micro-averaged metric.
     *
     * Note: The $measures array is ignored for micro-averaging as we need to
     * recalculate globally from the confusion matrix.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param array<string,float> $measures Per-class measures (ignored for micro-avg)
     * @return float The micro-averaged metric value
     */
    public function calculate(ConfusionMatrix $confusion_matrix, array $measures): float
    {
        $labels = $confusion_matrix->labels();

        $total_true_positives = 0;
        $total_false_positives = 0;

        // Sum up TP, FP, and FN across all classes
        foreach ($labels as $label) {
            /** @phpstan-ignore-next-line  */
            $total_true_positives += $confusion_matrix->truePositives($label);

            /** @phpstan-ignore-next-line  */
            $total_false_positives += $confusion_matrix->falsePositives($label);
        }

        // Calculate micro-averaged precision (which equals accuracy)
        // Note: For precision specifically, micro-avg = accuracy
        $denominator = $total_true_positives + $total_false_positives;

        // Handle division by zero case
        /** @phpstan-ignore-next-line  */
        return $denominator > 0 ? round($total_true_positives / $denominator, 4) : 0.0;
    }
}
