<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Metrics\Precision;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

beforeEach(function (): void {
    // Matrice 3x3 con calcoli banali
    // Progettata per avere risultati facili da verificare
    $this->labels = ['A', 'B', 'C'];
    $this->matrix = [
        [8, 1, 1],  // A: 8 corretti, 1 come B, 1 come C (10 A reali)
        [2, 6, 2],  // B: 2 come A, 6 corretti, 2 come C (10 B reali)
        [0, 3, 7]   // C: 0 come A, 3 come B, 7 corretti (10 C reali)
    ];
    $this->cm = new ConfusionMatrix($this->labels, $this->matrix);

    /*
     * Calcoli Precision:
     * A: TP=8, FP=2+0=2    → 8/(8+2) = 8/10 = 0.8
     * B: TP=6, FP=1+3=4    → 6/(6+4) = 6/10 = 0.6
     * C: TP=7, FP=1+2=3    → 7/(7+3) = 7/10 = 0.7
     *
     * Support (righe):
     * A: 8+1+1 = 10
     * B: 2+6+2 = 10
     * C: 0+3+7 = 10
     */
});

// RAW PRECISION TESTS
it('calculates raw precision for all classes', function (): void {
    $precision = new Precision();
    $result = $precision->measure($this->cm);

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
it('calculates macro averaged precision', function (): void {
    $precision = new Precision(AverageStrategy::MACRO);
    $result = $precision->measure($this->cm);

    // Macro = (0.8 + 0.6 + 0.7) / 3 = 2.1 / 3 = 0.7
    expect(round($result, 2))->toBe(0.7)
        ->and($result)->toBeFloat();
});

// MICRO AVERAGE TESTS
it('calculates micro averaged precision', function (): void {
    $precision = new Precision(AverageStrategy::MICRO);
    $result = $precision->measure($this->cm);

    // Micro = total_TP / (total_TP + total_FP)
    // total_TP = 8 + 6 + 7 = 21
    // total_FP = 2 + 4 + 3 = 9
    // Micro = 21 / (21 + 9) = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

// WEIGHTED AVERAGE TESTS
it('calculates weighted averaged precision', function (): void {
    $precision = new Precision(AverageStrategy::WEIGHTED);
    $result = $precision->measure($this->cm);

    // Weighted = Σ(precision × support) / Σ(support)
    // Support: A=10, B=10, C=10 (tutti uguali!)
    // Weighted = (0.8×10 + 0.6×10 + 0.7×10) / (10+10+10)
    //          = (8 + 6 + 7) / 30 = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

it('handles division by zero in precision calculation', function (): void {
    // Matrice dove una classe non viene mai predetta
    $labels = ['A', 'B'];
    $matrix = [
        [5, 0],  // A: tutti corretti
        [5, 0]   // B: tutti predetti come A
    ];
    $cm = new ConfusionMatrix($labels, $matrix);

    $precision = new Precision();
    $result = $precision->measure($cm);

    expect($result)->toBe([
        'A' => 0.5,  // 5/(5+5) = 0.5
        'B' => 0.0   // 0/(0+0) = 0.0 (division by zero handled)
    ]);
});

it('handles single class confusion matrix', function (): void {
    $cm = new ConfusionMatrix(['only'], [[10]]);

    $precision = new Precision();
    $result = $precision->measure($cm);

    expect($result)->toBe(['only' => 1.0])  // 10/(10+0) = 1.0
        ->and($result)->toBeArray();
});

it('validates averaging strategies work with perfect precision', function (): void {
    // Perfect classification
    $labels = ['A', 'B', 'C'];
    $matrix = [
        [5, 0, 0],
        [0, 5, 0],
        [0, 0, 5]
    ];
    $cm = new ConfusionMatrix($labels, $matrix);

    // All averages should be 1.0 for perfect classification
    $macro = new Precision(AverageStrategy::MACRO);
    $micro = new Precision(AverageStrategy::MICRO);
    $weighted = new Precision(AverageStrategy::WEIGHTED);

    expect($macro->measure($cm))->toBe(1.0)
        ->and($micro->measure($cm))->toBe(1.0)
        ->and($weighted->measure($cm))->toBe(1.0);
});
