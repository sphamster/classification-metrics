<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Metrics\Recall;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

beforeEach(function (): void {
    $this->labels = ['A', 'B', 'C'];
    $this->matrix = [
        [8, 1, 1],
        [2, 6, 2],
        [0, 3, 7]
    ];
    $this->cm = new ConfusionMatrix($this->labels, $this->matrix);
});

// RAW RECALL TESTS
it('calculates raw recall for all classes', function (): void {
    $recall = new Recall();
    $result = $recall->measure($this->cm);

    expect($result)->toBe([
        'A' => 0.8,  // 8/(8+2) = 0.8
        'B' => 0.6,  // 6/(6+4) = 0.6
        'C' => 0.7   // 7/(7+3) = 0.7
    ])->and($result)->toBeArray()
        ->and($result)->toHaveKey('A')
        ->and($result)->toHaveKey('B')
        ->and($result)->toHaveKey('C');
});

// MACRO AVERAGE TESTS
it('calculates macro averaged recall', function (): void {
    $recall = new Recall(AverageStrategy::MACRO);
    $result = $recall->measure($this->cm);

    // Macro = (0.8 + 0.6 + 0.7) / 3 = 2.1 / 3 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

// MICRO AVERAGE TESTS
it('calculates micro averaged recall', function (): void {
    $recall = new Recall(AverageStrategy::MICRO);
    $result = $recall->measure($this->cm);

    // Micro = total_TP / (total_TP + total_FN)
    // total_TP = 8 + 6 + 7 = 21
    // total_FN = 2 + 4 + 3 = 9
    // Micro = 21 / (21 + 9) = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

// WEIGHTED AVERAGE TESTS
it('calculates weighted averaged recall', function (): void {
    $recall = new Recall(AverageStrategy::WEIGHTED);
    $result = $recall->measure($this->cm);

    // Weighted = Σ(recall × support) / Σ(support)
    // Support: A=10, B=10, C=10 (tutti uguali!)
    // Weighted = (0.8×10 + 0.6×10 + 0.7×10) / (10+10+10)
    //          = (8 + 6 + 7) / 30 = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});
