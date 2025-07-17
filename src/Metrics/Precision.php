<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Metrics;

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Contracts\Calculator;
use Sphamster\ClassificationMetrics\Contracts\Metric;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

final class Precision implements Metric
{
    public function __construct(
        private readonly ?AverageStrategy $strategy = null,
    ) {

    }

    /**
     * @return array<string, float>|float
     */
    public function measure(ConfusionMatrix $confusion_matrix): array|float
    {
        // Calculate precision per label and store into an array
        $measures = $this->precision($confusion_matrix);

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
     * Calculate precision for each class.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @return array<string,float> Precision values keyed by class label
     */
    private function precision(ConfusionMatrix $confusion_matrix): array
    {
        $measures = [];
        $labels = $confusion_matrix->labels();

        foreach ($labels as $label) {
            $measures[$label] = $this->precisionForLabel($confusion_matrix, $label);
        }

        return $measures;
    }


    /**
     * Calculate precision for a single class label.
     *
     * @param ConfusionMatrix $confusion_matrix The confusion matrix
     * @param string $label The class label
     * @return float The precision value for the label
     */
    private function precisionForLabel(ConfusionMatrix $confusion_matrix, string $label): float
    {
        /** @var int $true_positives */
        $true_positives = $confusion_matrix->truePositives($label);

        /** @var int $false_positives */
        $false_positives = $confusion_matrix->falsePositives($label);

        $denominator = $true_positives + $false_positives;

        // Handle division by zero case
        return $denominator > 0 ? round($true_positives / $denominator, 4) : 0.0;
    }
}
