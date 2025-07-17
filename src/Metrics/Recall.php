<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Metrics;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;
use Sphamster\ClassificationMetrics\Contracts\Metric;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

/**
 * Recall (Sensitivity) metric implementation.
 *
 * Recall is the ratio of true positives to the sum of true positives and false negatives.
 * It answers: "Of all actual positive instances, how many were correctly identified?"
 * Also known as Sensitivity or True Positive Rate.
 */
final class Recall implements Metric
{
    public function __construct(
        private readonly ?AverageStrategy $strategy = null,
    ) {
    }

    /**
     * Measure recall for all classes or aggregate using strategy.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @return float|array<string,float> Aggregated recall or per-class recalls
     */
    public function measure(ConfusionMatrix $confusion_matrix): array|float
    {
        // Calculate recall per label and store into an array
        $measures = $this->recall($confusion_matrix);

        // If no strategy provided, return raw per-class measures
        if ( ! $this->strategy instanceof AverageStrategy) {
            return $measures;
        }

        // Apply averaging strategy to aggregate the measures
        /** @var Calculator $calculator */
        $calculator = $this->strategy->toCalculator();
        return $calculator->calculate($confusion_matrix, $measures);
    }

    /**
     * Calculate recall for each class.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @return array<string,float> Recall values keyed by class label
     */
    private function recall(ConfusionMatrix $confusion_matrix): array
    {
        $measures = [];
        $labels = $confusion_matrix->labels();

        foreach ($labels as $label) {
            $measures[$label] = $this->recallForLabel($confusion_matrix, $label);
        }

        return $measures;
    }

    /**
     * Calculate recall for a single class label.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param string $label The class label
     * @return float The recall value for the label
     */
    private function recallForLabel(ConfusionMatrix $confusion_matrix, string $label): float
    {
        /** @var int $true_positives */
        $true_positives = $confusion_matrix->truePositives($label);

        /** @var int $false_negatives */
        $false_negatives = $confusion_matrix->falseNegatives($label);

        $denominator = $true_positives + $false_negatives;

        // Handle division by zero case
        return $denominator > 0 ? round($true_positives / $denominator, 4) : 0.0;
    }
}
