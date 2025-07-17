<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Calculators\Average;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;

/**
 * Weighted Calculator for classification metrics.
 *
 * Weighted-averaging calculates metrics for each label and finds their average weighted
 * by support (the number of true instances for each label). This takes label imbalance
 * into account, giving more weight to classes with more instances.
 */
final class Weighted implements Calculator
{
    /**
     * Calculate weighted-averaged metric.
     *
     * For any metric: weighted-avg = Σ(metric_class_i × support_class_i) / Σ(support_class_i)
     * Classes with more instances (higher support) contribute more to the final score.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix (used to get support)
     * @param array<string,float> $measures Per-class metric values keyed by class label
     * @return float The weighted-averaged metric value
     */
    public function calculate(ConfusionMatrix $confusion_matrix, array $measures): float
    {
        // Handle empty measures array
        if ($measures === []) {
            return 0.0;
        }

        // Get support for each class (number of true instances)
        $supports = $confusion_matrix->support();

        /** @phpstan-ignore-next-line  */
        $total_support = array_sum($supports);

        // Handle case where total support is zero
        if ($total_support === 0) {
            return 0.0;
        }

        // Calculate weighted sum: Σ(metric × support)
        $weighted_sum = 0.0;
        foreach ($measures as $label => $metric_value) {
            // Get support for this specific label
            $class_support = $supports[$label] ?? 0;
            $weighted_sum += $metric_value * $class_support;
        }

        // Return weighted average: weighted_sum / total_support
        return round($weighted_sum / $total_support, 4);
    }
}
