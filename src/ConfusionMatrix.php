<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics;

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



}
