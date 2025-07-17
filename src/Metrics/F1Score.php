<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Metrics;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;
use Sphamster\ClassificationMetrics\Contracts\Metric;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

/**
 * F1-Score metric implementation.
 *
 * F1-Score is the harmonic mean of precision and recall.
 * It provides a balanced measure between precision and recall,
 * giving equal weight to both metrics.
 * Formula: F1 = 2 * (precision * recall) / (precision + recall)
 */
final class F1Score implements Metric
{
    public function __construct(
        private readonly ?AverageStrategy $strategy = null,
    ) {
    }

    /**
     * Measure F1-Score for all classes or aggregate using strategy.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @return float|array<string,float> Aggregated F1-Score or per-class F1-Scores
     */
    public function measure(ConfusionMatrix $confusion_matrix): array|float
    {
        // Calculate F1-Score per label and store into an array
        $measures = $this->f1Score($confusion_matrix);

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
     * Calculate F1-Score for each class.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @return array<string,float> F1-Score values keyed by class label
     */
    private function f1Score(ConfusionMatrix $confusion_matrix): array
    {
        $measures = [];
        $labels = $confusion_matrix->labels();

        foreach ($labels as $label) {
            $measures[$label] = $this->f1ScoreForLabel($confusion_matrix, $label);
        }

        return $measures;
    }

    /**
     * Calculate F1-Score for a single class label.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param string $label The class label
     * @return float The F1-Score value for the label
     */
    private function f1ScoreForLabel(ConfusionMatrix $confusion_matrix, string $label): float
    {
        $precision = $this->calculatePrecisionForLabel($confusion_matrix, $label); //todo: why dont call precision metric?
        $recall = $this->calculateRecallForLabel($confusion_matrix, $label); //todo: why dont call recall metric?

        $denominator = $precision + $recall;

        // Handle division by zero case
        if ($denominator === 0.0) {
            return 0.0;
        }

        // F1 = 2 * (precision * recall) / (precision + recall)
        return round(
            num: (2.0 * $precision * $recall) / $denominator,
            precision: 2
        );
    }

    /**
     * Calculate precision for a single class label.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param string $label The class label
     * @return float The precision value for the label
     */
    private function calculatePrecisionForLabel(ConfusionMatrix $confusion_matrix, string $label): float
    {
        /** @var int $true_positives */
        $true_positives = $confusion_matrix->truePositives($label);

        /** @var int $false_positives */
        $false_positives = $confusion_matrix->falsePositives($label);

        $denominator = $true_positives + $false_positives;

        return $denominator > 0 ? $true_positives / $denominator : 0.0;
    }

    /**
     * Calculate recall for a single class label.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param string $label The class label
     * @return float The recall value for the label
     */
    private function calculateRecallForLabel(ConfusionMatrix $confusion_matrix, string $label): float
    {
        /** @var int $true_positives */
        $true_positives = $confusion_matrix->truePositives($label);

        /** @var int $false_negatives */
        $false_negatives = $confusion_matrix->falseNegatives($label);

        $denominator = $true_positives + $false_negatives;

        return $denominator > 0 ? $true_positives / $denominator : 0.0;
    }

}
