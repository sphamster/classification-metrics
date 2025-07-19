<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Exceptions\EmptyLabelException;
use Sphamster\ClassificationMetrics\Exceptions\SizeMismatchException;

// VALID CASES


it('creates a valid confusion matrix', function (): void {
    $labels = ['cat', 'dog'];
    $matrix = [
        [8, 2],  // cat: 8 correct, 2 as dog
        [1, 9]   // dog: 1 as cat, 9 correct
    ];

    $confusion_matrix = new ConfusionMatrix($labels, $matrix);

    expect($confusion_matrix->labels())->toBe(['cat', 'dog'])
        ->and($confusion_matrix->matrix())->toBe($matrix);
});

it('creates a valid confusion matrix from predictions', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = ['dog', 'cat', 'cat', 'cat'];

    $confusion_matrix = ConfusionMatrix::fromPredictions($true_labels, $predicted_labels);

    expect($confusion_matrix->labels())->toBe(['cat', 'dog'])
        ->and($confusion_matrix->matrix()[0][0])->toBe(1) // cat predicted as cat
        ->and($confusion_matrix->matrix()[0][1])->toBe(1) // cat predicted as dog
        ->and($confusion_matrix->matrix()[1][0])->toBe(2) // dog predicted as cat
        ->and($confusion_matrix->matrix()[1][1])->toBe(0); // dog predicted as dog
});


it('converts confusion matrix to string format', function (): void {
    $labels = ['cat', 'dog'];
    $matrix = [
        [8, 2],  // cat: 8 correct, 2 as dog
        [1, 9]   // dog: 1 as cat, 9 correct
    ];

    $confusion_matrix = new ConfusionMatrix($labels, $matrix);
    $string_output = (string) $confusion_matrix;

    expect($string_output)
        ->toContain('cat')
        ->toContain('dog')
        ->toContain('8')
        ->toContain('2')
        ->toContain('1')
        ->toContain('9');
});


// INVALID CASES

it('throws exception for empty labels', function (): void {
    expect(fn (): ConfusionMatrix => new ConfusionMatrix([], []))
        ->toThrow(EmptyLabelException::class, 'Labels cannot be empty');
});

it('throws exception when matrix rows !== labels count', function (): void {
    $labels = ['a', 'b'];
    $matrix = [[1, 2]];

    expect(fn (): ConfusionMatrix => new ConfusionMatrix($labels, $matrix))
        ->toThrow(SizeMismatchException::class, 'Matrix dimensions must match labels');
});

it('throws exception when matrix is not square', function (): void {
    $labels = ['a', 'b'];
    $matrix = [
        [1, 2, 3],
        [4, 5]
    ];

    expect(fn (): ConfusionMatrix => new ConfusionMatrix($labels, $matrix))
        ->toThrow(SizeMismatchException::class, 'Matrix must be square');
});

it('throws exception when labels are empty', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = [];

    expect(fn (): ConfusionMatrix => ConfusionMatrix::fromPredictions($true_labels, $predicted_labels))
        ->toThrow(InvalidArgumentException::class, 'Missing or empty labels');
});

it('throws exception when labels have different size', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = ['cat', 'dog', 'cat', 'dog', 'dog'];

    expect(fn (): ConfusionMatrix => ConfusionMatrix::fromPredictions($true_labels, $predicted_labels))
        ->toThrow(InvalidArgumentException::class, 'true and predicted labels must have same labels');
});

it('throws exception when labels are inconsinstent', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = ['cat', 'dog', 'cat', 'bird'];

    expect(fn (): ConfusionMatrix => ConfusionMatrix::fromPredictions($true_labels, $predicted_labels))
        ->toThrow(InvalidArgumentException::class, 'Each label must be present in true labels');
});

it('throws exception when there are missing labels in custom ordered $labels', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = ['cat', 'dog', 'cat', 'dog'];

    expect(fn (): ConfusionMatrix => ConfusionMatrix::fromPredictions($true_labels, $predicted_labels, labels: ['dog']))
        ->toThrow(
            exception: InvalidArgumentException::class,
            exceptionMessage: 'You must provide all labels in $labels array. Missing labels: [ cat ]'
        );
});

it('throws exception when there are extra labels in custom ordered $labels', function (): void {
    $true_labels = ['cat', 'dog', 'cat', 'dog'];
    $predicted_labels = ['cat', 'dog', 'cat', 'dog'];

    expect(fn (): ConfusionMatrix => ConfusionMatrix::fromPredictions($true_labels, $predicted_labels, labels: ['dog','cat', 'bird']))
        ->toThrow(
            exception: InvalidArgumentException::class,
            exceptionMessage: 'You provided some extra labels in $labels array. Extra labels: [ bird ]'
        );
});
