<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Calculators\Average\Weighted;

describe('Weighted Calculator Tests', function (): void {
    beforeEach(function (): void {
        $this->calculator = new Weighted();
    });

    it('calculates weighted average for multiple classes', function (): void {
        $cm = new ConfusionMatrix(['A', 'B', 'C'], [
            [10, 2, 3],  // A: support = 15
            [1, 20, 4],  // B: support = 25
            [2, 3, 35]   // C: support = 40
        ]);

        $measures = ['A' => 0.8, 'B' => 0.6, 'C' => 0.9];

        $result = $this->calculator->calculate($cm, $measures);

        // Weighted = (0.8×15 + 0.6×25 + 0.9×40) / (15+25+40) = (12+15+36)/80 = 63/80 = 0.7875
        expect($result)->toBe(0.7875);
    });

    it('calculates weighted average for two classes', function (): void {
        $cm = new ConfusionMatrix(['A', 'B'], [
            [10, 5],   // A: support = 15
            [3, 20]    // B: support = 23
        ]);

        $measures = ['A' => 0.8, 'B' => 0.6];

        $result = $this->calculator->calculate($cm, $measures);

        // Weighted = (0.8×15 + 0.6×23) / (15+23) = (12+13.8)/38 = 25.8/38 ≈ 0.6789
        expect($result)->toBe(0.6789);
    });

    it('returns 0.0 when measures array is empty', function (): void {
        $cm = new ConfusionMatrix(['A'], [[5]]);

        $result = $this->calculator->calculate($cm, []);

        expect($result)->toBe(0.0);
    });

    it('returns 0.0 when total support is zero', function (): void {
        $cm = new ConfusionMatrix(['A'], [[0]]);
        $measures = ['A' => 0.5];

        $result = $this->calculator->calculate($cm, $measures);

        expect($result)->toBe(0.0);
    });

    it('handles missing label in support using default value', function (): void {
        $cm = new ConfusionMatrix(['A', 'B'], [
            [10, 2],  // A: support = 12
            [3, 8]    // B: support = 11
        ]);

        // Measures has extra label 'C' not in confusion matrix
        $measures = ['A' => 0.8, 'B' => 0.6, 'C' => 0.9];

        $result = $this->calculator->calculate($cm, $measures);

        // Weighted = (0.8×12 + 0.6×11 + 0.9×0) / (12+11) = (9.6+6.6+0)/23 = 16.2/23 ≈ 0.7043
        expect($result)->toBe(0.7043);
    });

    it('calculates weighted average for single class', function (): void {
        $cm = new ConfusionMatrix(['only'], [[10]]);
        $measures = ['only' => 0.75];

        $result = $this->calculator->calculate($cm, $measures);

        // Weighted = (0.75×10) / 10 = 0.75
        expect($result)->toBe(0.75);
    });

    it('rounds result to 4 decimal places', function (): void {
        $cm = new ConfusionMatrix(['A', 'B'], [
            [7, 3],   // A: support = 10
            [2, 8]    // B: support = 10
        ]);

        $measures = ['A' => 0.123456789, 'B' => 0.987654321];

        $result = $this->calculator->calculate($cm, $measures);

        // Weighted = (0.123456789×10 + 0.987654321×10) / 20 = 11.11111110/20 = 0.555555555 → rounded to 0.5556
        expect($result)->toBe(0.5556);
    });
});
