<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Metrics\F1Score;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

beforeEach(function (): void {
    // Matrice 3x3 con calcoli banali per F1-Score
    // Stessa matrice per confrontare con precision e recall
    $this->labels = ['A', 'B', 'C'];
    $this->matrix = [
        [8, 1, 1],  // A: 8 corretti, 1 come B, 1 come C (10 A reali)
        [2, 6, 2],  // B: 2 come A, 6 corretti, 2 come C (10 B reali)
        [0, 3, 7]   // C: 0 come A, 3 come B, 7 corretti (10 C reali)
    ];
    $this->cm = new ConfusionMatrix($this->labels, $this->matrix);

    /*
     * Calcoli F1-Score:
     *
     * A: Precision=0.8, Recall=0.8 → F1 = 2*(0.8*0.8)/(0.8+0.8) = 1.28/1.6 = 0.8
     * B: Precision=0.6, Recall=0.6 → F1 = 2*(0.6*0.6)/(0.6+0.6) = 0.72/1.2 = 0.6
     * C: Precision=0.7, Recall=0.7 → F1 = 2*(0.7*0.7)/(0.7+0.7) = 0.98/1.4 = 0.7
     *
     * Note: Per questa matrice, precision = recall per ogni classe,
     * quindi F1 = precision = recall
     */
});

// RAW F1-SCORE TESTS
it('calculates raw F1-Score for all classes', function (): void {
    $f1 = new F1Score();
    $result = $f1->measure($this->cm);

    expect($result)->toBe([
        'A' => 0.8,  // F1 per A
        'B' => 0.6,  // F1 per B
        'C' => 0.7   // F1 per C
    ])->and($result)->toBeArray()
        ->and($result)->toHaveKey('A')
        ->and($result)->toHaveKey('B')
        ->and($result)->toHaveKey('C');
});

// MACRO AVERAGE TESTS
it('calculates macro averaged F1-Score', function (): void {
    $f1 = new F1Score(AverageStrategy::MACRO);
    $result = $f1->measure($this->cm);

    // Macro = (0.8 + 0.6 + 0.7) / 3 = 2.1 / 3 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

// MICRO AVERAGE TESTS
it('calculates micro averaged F1-Score', function (): void {
    $f1 = new F1Score(AverageStrategy::MICRO);
    $result = $f1->measure($this->cm);

    // Micro F1 = micro precision = micro recall = accuracy
    // total_TP = 8 + 6 + 7 = 21
    // total_predictions = 30
    // Micro F1 = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

// WEIGHTED AVERAGE TESTS
it('calculates weighted averaged F1-Score', function (): void {
    $f1 = new F1Score(AverageStrategy::WEIGHTED);
    $result = $f1->measure($this->cm);

    // Weighted = Σ(f1 × support) / Σ(support)
    // Support: A=10, B=10, C=10 (tutti uguali!)
    // Weighted = (0.8×10 + 0.6×10 + 0.7×10) / (10+10+10)
    //          = (8 + 6 + 7) / 30 = 21/30 = 0.7
    expect($result)->toBe(0.7)
        ->and($result)->toBeFloat();
});

it('handles division by zero when precision and recall are both zero', function (): void {
    // Create matrix where class B has no true positives (precision=0, recall=0)
    $labels = ['A', 'B'];
    $matrix = [
        [10, 0],  // A: all correct, none predicted as B
        [10, 0]   // B: all predicted as A, none correct (TP=0, FP=0, FN=10)
    ];
    $cm = new ConfusionMatrix($labels, $matrix);

    $f1 = new F1Score();
    $result = $f1->measure($cm);

    // Class B: precision=0/0=0.0, recall=0/10=0.0 → denominator=0.0 → F1=0.0
    expect($result['B'])->toBe(0.0);
});
