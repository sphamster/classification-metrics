<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics;

use InvalidArgumentException;
use Sphamster\ClassificationMetrics\Concerns\HasSortableLabels;
use Sphamster\ClassificationMetrics\Concerns\IsStringable;
use Sphamster\ClassificationMetrics\Contracts\WithSortableLabels;
use Sphamster\ClassificationMetrics\Validators\LabelsValidator;
use Sphamster\ClassificationMetrics\Validators\MatrixValidator;
use Sphamster\ClassificationMetrics\Validators\PredictionsValidator;

class ConfusionMatrix implements WithSortableLabels
{
    use HasSortableLabels;
    use IsStringable;

    /**
     * Creates a new confusion matrix instance.
     *
     * This constructor initializes a confusion matrix with the provided labels and matrix data.
     * It validates both the labels and matrix structure using their respective validators.
     *
     * @param array<string> $labels The class labels for the confusion matrix
     * @param array<array<int>> $matrix The confusion matrix data as a 2D array of integers
     */
    public function __construct(
        private readonly array $labels,
        private readonly array $matrix
    ) {
        LabelsValidator::validate([
            'labels' => $this->labels,
        ]);

        MatrixValidator::validate([
            'labels' => $this->labels,
            'matrix' => $this->matrix
        ]);
    }

    /**
     * Gets the confusion matrix data.
     *
     * This method returns the 2D array representing the confusion matrix,
     * where each cell [i,j] contains the count of instances with true label i
     * that were predicted as label j.
     *
     * @return array<array<int>> The 2D array of the confusion matrix
     */
    public function matrix(): array
    {
        return $this->matrix;
    }

    /**
     * Gets the class labels for the confusion matrix.
     *
     * This method returns the array of class labels that correspond to
     * the rows and columns of the confusion matrix.
     *
     * @return array<string> The array of class labels
     */
    public function labels(): array
    {
        return $this->labels;
    }

    /**
     * Creates a confusion matrix from true and predicted labels.
     *
     * This static factory method constructs a confusion matrix by comparing
     * the true labels with the predicted labels. It validates the input data,
     * ensures label consistency, and computes the confusion matrix.
     *
     * @param array<string> $true_labels The ground truth labels
     * @param array<string> $predicted_labels The predicted labels
     * @param array<string>|null $labels Optional custom set of labels (defaults to unique true labels)
     * @return ConfusionMatrix A new ConfusionMatrix instance
     */
    public static function fromPredictions(
        array  $true_labels,
        array  $predicted_labels,
        ?array $labels = null
    ): self {
        PredictionsValidator::validate([
            'true_labels' => $true_labels,
            'predicted_labels' => $predicted_labels
        ]);

        $labels = self::sortLabels($labels ?? array_unique($true_labels));

        // ensure $labels contains all the dataset's labels
        self::ensureLabelsAreConsistent($true_labels, $predicted_labels, $labels);

        return new self(
            labels: $labels,
            matrix: self::compute($true_labels, $predicted_labels, $labels)
        );
    }

    /**
     * Computes the confusion matrix from true and predicted labels.
     *
     * This method calculates the confusion matrix by counting the occurrences
     * of each (true label, predicted label) pair. It maps labels to numerical indices,
     * initializes a zero matrix, and then increments the appropriate cell for each prediction.
     *
     * @param array<string> $true_labels The ground truth labels
     * @param array<string> $predicted_labels The predicted labels
     * @param array<string> $labels The complete set of class labels
     * @return array<array<int>> The computed confusion matrix as a 2D array
     */
    protected static function compute(
        array $true_labels,
        array $predicted_labels,
        array $labels
    ): array {
        // maps label to numerical index.
        $map = array_flip($labels);

        // size of confusion matrix N x N
        $size = count($labels);

        // Initializes a square matrix with all elements set to zero.
        $matrix = array_fill(0, $size, array_fill(0, $size, 0));

        foreach ($true_labels as $i => $true) {
            // Increments count in confusion matrix cell corresponding to true and predicted label pair.
            $matrix[$map[$true]][$map[$predicted_labels[$i]]]++;
        }

        return $matrix;
    }

    /**
     * Get the index of a label in the labels array.
     *
     * @param string $label The label to find
     * @return int The index of the label
     * @throws InvalidArgumentException If label is not found
     */
    protected function getLabelIndex(string $label): int
    {
        $index = array_search($label, $this->labels, true);

        if ($index === false) {
            throw new InvalidArgumentException("Label '{$label}' not found in confusion matrix labels");
        }

        return (int) $index;
    }

    /**
     * Calculate True Positives for a specific class or all classes.
     *
     * @param string|null $label The class label (null for all classes)
     * @return int|array<string,int> Number of true positives or array with all classes
     */
    public function truePositives(?string $label = null): int|array
    {
        if ($label !== null) {
            return $this->truePositivesForLabel($label);
        }

        $result = [];
        foreach ($this->labels as $label) {
            $result[$label] = $this->truePositivesForLabel($label);
        }
        return $result;
    }

    /**
     * Calculate False Positives for a specific class or all classes.
     *
     * @param string|null $label The class label (null for all classes)
     * @return int|array<string,int> Number of false positives or array with all classes
     */
    public function falsePositives(?string $label = null): int|array
    {
        if ($label !== null) {
            return $this->falsePositivesForLabel($label);
        }

        $result = [];
        foreach ($this->labels as $label) {
            $result[$label] = $this->falsePositivesForLabel($label);
        }
        return $result;
    }

    /**
     * Calculate False Negatives for a specific class or all classes.
     *
     * @param string|null $label The class label (null for all classes)
     * @return int|array<string,int> Number of false negatives or array with all classes
     */
    public function falseNegatives(?string $label = null): int|array
    {
        if ($label !== null) {
            return $this->falseNegativesForLabel($label);
        }

        $result = [];
        foreach ($this->labels as $label) {
            $result[$label] = $this->falseNegativesForLabel($label);
        }
        return $result;
    }

    /**
     * Calculate True Negatives for a specific class or all classes.
     *
     * @param string|null $label The class label (null for all classes)
     * @return int|array<string,int> Number of true negatives or array with all classes
     */
    public function trueNegatives(?string $label = null): int|array
    {
        if ($label !== null) {
            return $this->trueNegativesForLabel($label);
        }

        $result = [];
        foreach ($this->labels as $label) {
            $result[$label] = $this->trueNegativesForLabel($label);
        }
        return $result;
    }

    /**
     * Calculate support (number of actual instances) for a specific class or all classes.
     *
     * @param string|null $label The class label (null for all classes)
     * @return int|array<string,int> Number of actual instances or array with all classes
     */
    public function support(?string $label = null): int|array
    {
        if ($label !== null) {
            return $this->supportForLabel($label);
        }

        $result = [];
        foreach ($this->labels as $label) {
            $result[$label] = $this->supportForLabel($label);
        }

        return $result;
    }

    protected function truePositivesForLabel(string $label): int
    {
        // get label int index
        $class_index = $this->getLabelIndex($label);

        return $this->matrix[$class_index][$class_index];
    }

    protected function falsePositivesForLabel(string $label): int
    {
        $false_positives = 0;
        $class_index = $this->getLabelIndex($label);
        $counter = count($this->matrix);

        for ($i = 0; $i < $counter; $i++) {
            if ($i !== $class_index) {
                $false_positives += $this->matrix[$i][$class_index];
            }
        }

        return $false_positives;
    }

    protected function falseNegativesForLabel(string $label): int
    {
        $false_negatives = 0;
        $class_index = $this->getLabelIndex($label);
        $counter = count($this->matrix[$class_index]);

        for ($j = 0; $j < $counter; $j++) {
            if ($j !== $class_index) {
                $false_negatives += $this->matrix[$class_index][$j];
            }
        }

        return $false_negatives;
    }

    protected function trueNegativesForLabel(string $label): int
    {
        $true_negatives = 0;
        $class_index = $this->getLabelIndex($label);
        $counter = count($this->matrix);

        for ($i = 0; $i < $counter; $i++) {
            for ($j = 0; $j < count($this->matrix[$i]); $j++) {
                if ($i !== $class_index && $j !== $class_index) {
                    $true_negatives += $this->matrix[$i][$j];
                }
            }
        }

        return $true_negatives;
    }

    protected function supportForLabel(string $label): int
    {
        $class_index = $this->getLabelIndex($label);
        return array_sum($this->matrix[$class_index]);
    }
}
