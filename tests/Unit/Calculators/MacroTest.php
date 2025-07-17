<?php

declare(strict_types=1);

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Calculators\Average\Macro;

beforeEach(function (): void {
    // Create a simple confusion matrix (not used by Macro but required for interface)
    $this->cm = new ConfusionMatrix(['A', 'B'], [[5, 2], [1, 4]]);
    $this->calculator = new Macro();
});

//it('calculates macro average for multiple classes', function (): void {
//    $measures = ['A' => 0.8, 'B' => 0.6, 'C' => 0.7];
//
//    $result = $this->calculator->calculate($this->cm, $measures);
//
//    // (0.8 + 0.6 + 0.7) / 3 = 2.1 / 3 = 0.7
//    expect($result)->toBe(0.7);
//});


it('handles empty measures array directly', function (): void {
    $cm = new ConfusionMatrix(['A'], [[5]]);
    $calculator = new Macro();

    // Call directly with empty array to force the condition
    $result = $calculator->calculate($this->cm, []);

    expect($result)->toBe(0.0);
});
