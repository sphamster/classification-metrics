<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Sphamster\ClassificationMetrics\ConfusionMatrix;
use Sphamster\ClassificationMetrics\Metrics\Precision;
use Sphamster\ClassificationMetrics\Metrics\Recall;
use Sphamster\ClassificationMetrics\Metrics\F1Score;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

echo "🔍 VALIDATION TEST: Manual vs Automated Calculations\n";
echo "====================================================\n\n";

// ========================================================================
// STEP 1: Define Test Matrix (3x3)
// ========================================================================

echo "📊 Test Matrix 3x3:\n";
echo "-------------------\n";

$labels = ['A', 'B', 'C'];
$matrix = [
    [80,  10,  10],  // A: 80 correct, 10 as B, 10 as C (100 A reali)
    [15,  70,  15],  // B: 15 as A, 70 correct, 15 as C (100 B reali)
    [5,  20,  75]   // C: 5 as A, 20 as B, 75 correct (100 C reali)
];

echo "     A   B   C\n";
echo "A [ 80, 10, 10]  (100 A reali)\n";
echo "B [ 15, 70, 15]  (100 B reali)\n";
echo "C [  5, 20, 75]  (100 C reali)\n\n";

// Create ConfusionMatrix object
$cm = new ConfusionMatrix($labels, $matrix);

// ========================================================================
// STEP 2: Manual Calculations (Hardcoded)
// ========================================================================

echo "🧮 MANUAL CALCULATIONS:\n";
echo "=======================\n\n";

$manual_results = [];

// Basic metrics manual calculation
echo "1️⃣ Basic Metrics (Manual):\n";
echo "-------------------------\n";

// True Positives (diagonal elements)
$manual_tp_A = 80;
$manual_tp_B = 70;
$manual_tp_C = 75;
echo "TP_A = {$manual_tp_A}, TP_B = {$manual_tp_B}, TP_C = {$manual_tp_C}\n";

// False Positives (sum of column excluding diagonal)
$manual_fp_A = 15 + 5;   // B->A + C->A = 20
$manual_fp_B = 10 + 20;  // A->B + C->B = 30
$manual_fp_C = 10 + 15;  // A->C + B->C = 25
echo "FP_A = {$manual_fp_A}, FP_B = {$manual_fp_B}, FP_C = {$manual_fp_C}\n";

// False Negatives (sum of row excluding diagonal)
$manual_fn_A = 10 + 10;  // A->B + A->C = 20
$manual_fn_B = 15 + 15;  // B->A + B->C = 30
$manual_fn_C = 5 + 20;   // C->A + C->B = 25
echo "FN_A = {$manual_fn_A}, FN_B = {$manual_fn_B}, FN_C = {$manual_fn_C}\n";

// Support (sum of row)
$manual_support_A = 80 + 10 + 10; // 100
$manual_support_B = 15 + 70 + 15; // 100
$manual_support_C = 5 + 20 + 75;  // 100
echo "Support_A = {$manual_support_A}, Support_B = {$manual_support_B}, Support_C = {$manual_support_C}\n\n";

// Precision manual calculation
echo "2️⃣ Precision (Manual):\n";
echo "---------------------\n";

$manual_precision_A = $manual_tp_A / ($manual_tp_A + $manual_fp_A); // 80 / (80 + 20) = 0.8
$manual_precision_B = $manual_tp_B / ($manual_tp_B + $manual_fp_B); // 70 / (70 + 30) = 0.7
$manual_precision_C = $manual_tp_C / ($manual_tp_C + $manual_fp_C); // 75 / (75 + 25) = 0.75

echo "Precision_A = {$manual_tp_A}/({$manual_tp_A}+{$manual_fp_A}) = {$manual_precision_A}\n";
echo "Precision_B = {$manual_tp_B}/({$manual_tp_B}+{$manual_fp_B}) = {$manual_precision_B}\n";
echo "Precision_C = {$manual_tp_C}/({$manual_tp_C}+{$manual_fp_C}) = {$manual_precision_C}\n\n";

$manual_results['precision_raw_A'] = $manual_precision_A;
$manual_results['precision_raw_B'] = $manual_precision_B;
$manual_results['precision_raw_C'] = $manual_precision_C;

// Precision averaging
$manual_precision_macro = ($manual_precision_A + $manual_precision_B + $manual_precision_C) / 3;
$manual_precision_weighted = ($manual_precision_A * $manual_support_A + $manual_precision_B * $manual_support_B + $manual_precision_C * $manual_support_C) / ($manual_support_A + $manual_support_B + $manual_support_C);
$manual_precision_micro = ($manual_tp_A + $manual_tp_B + $manual_tp_C) / ($manual_tp_A + $manual_tp_B + $manual_tp_C + $manual_fp_A + $manual_fp_B + $manual_fp_C);

echo "Precision Macro = ({$manual_precision_A}+{$manual_precision_B}+{$manual_precision_C})/3 = {$manual_precision_macro}\n";
echo "Precision Weighted = ({$manual_precision_A}*{$manual_support_A}+{$manual_precision_B}*{$manual_support_B}+{$manual_precision_C}*{$manual_support_C})/300 = {$manual_precision_weighted}\n";
echo "Precision Micro = ".($manual_tp_A + $manual_tp_B + $manual_tp_C)."/".($manual_tp_A + $manual_tp_B + $manual_tp_C + $manual_fp_A + $manual_fp_B + $manual_fp_C)." = {$manual_precision_micro}\n\n";

$manual_results['precision_macro'] = $manual_precision_macro;
$manual_results['precision_weighted'] = $manual_precision_weighted;
$manual_results['precision_micro'] = $manual_precision_micro;

// Recall manual calculation
echo "3️⃣ Recall (Manual):\n";
echo "------------------\n";

$manual_recall_A = $manual_tp_A / ($manual_tp_A + $manual_fn_A); // 80 / (80 + 20) = 0.8
$manual_recall_B = $manual_tp_B / ($manual_tp_B + $manual_fn_B); // 70 / (70 + 30) = 0.7
$manual_recall_C = $manual_tp_C / ($manual_tp_C + $manual_fn_C); // 75 / (75 + 25) = 0.75

echo "Recall_A = {$manual_tp_A}/({$manual_tp_A}+{$manual_fn_A}) = {$manual_recall_A}\n";
echo "Recall_B = {$manual_tp_B}/({$manual_tp_B}+{$manual_fn_B}) = {$manual_recall_B}\n";
echo "Recall_C = {$manual_tp_C}/({$manual_tp_C}+{$manual_fn_C}) = {$manual_recall_C}\n\n";

$manual_results['recall_raw_A'] = $manual_recall_A;
$manual_results['recall_raw_B'] = $manual_recall_B;
$manual_results['recall_raw_C'] = $manual_recall_C;

// Recall averaging
$manual_recall_macro = ($manual_recall_A + $manual_recall_B + $manual_recall_C) / 3;
$manual_recall_weighted = ($manual_recall_A * $manual_support_A + $manual_recall_B * $manual_support_B + $manual_recall_C * $manual_support_C) / ($manual_support_A + $manual_support_B + $manual_support_C);
$manual_recall_micro = ($manual_tp_A + $manual_tp_B + $manual_tp_C) / ($manual_tp_A + $manual_tp_B + $manual_tp_C + $manual_fn_A + $manual_fn_B + $manual_fn_C);

echo "Recall Macro = ({$manual_recall_A}+{$manual_recall_B}+{$manual_recall_C})/3 = {$manual_recall_macro}\n";
echo "Recall Weighted = ({$manual_recall_A}*{$manual_support_A}+{$manual_recall_B}*{$manual_support_B}+{$manual_recall_C}*{$manual_support_C})/300 = {$manual_recall_weighted}\n";
echo "Recall Micro = ".($manual_tp_A + $manual_tp_B + $manual_tp_C)."/".($manual_tp_A + $manual_tp_B + $manual_tp_C + $manual_fn_A + $manual_fn_B + $manual_fn_C)." = {$manual_recall_micro}\n\n";

$manual_results['recall_macro'] = $manual_recall_macro;
$manual_results['recall_weighted'] = $manual_recall_weighted;
$manual_results['recall_micro'] = $manual_recall_micro;

// F1-Score manual calculation
echo "4️⃣ F1-Score (Manual):\n";
echo "--------------------\n";

$manual_f1_A = 2 * ($manual_precision_A * $manual_recall_A) / ($manual_precision_A + $manual_recall_A);
$manual_f1_B = 2 * ($manual_precision_B * $manual_recall_B) / ($manual_precision_B + $manual_recall_B);
$manual_f1_C = 2 * ($manual_precision_C * $manual_recall_C) / ($manual_precision_C + $manual_recall_C);

echo "F1_A = 2*({$manual_precision_A}*{$manual_recall_A})/({$manual_precision_A}+{$manual_recall_A}) = {$manual_f1_A}\n";
echo "F1_B = 2*({$manual_precision_B}*{$manual_recall_B})/({$manual_precision_B}+{$manual_recall_B}) = {$manual_f1_B}\n";
echo "F1_C = 2*({$manual_precision_C}*{$manual_recall_C})/({$manual_precision_C}+{$manual_recall_C}) = {$manual_f1_C}\n\n";

$manual_results['f1_raw_A'] = $manual_f1_A;
$manual_results['f1_raw_B'] = $manual_f1_B;
$manual_results['f1_raw_C'] = $manual_f1_C;

// F1-Score averaging
$manual_f1_macro = ($manual_f1_A + $manual_f1_B + $manual_f1_C) / 3;
$manual_f1_weighted = ($manual_f1_A * $manual_support_A + $manual_f1_B * $manual_support_B + $manual_f1_C * $manual_support_C) / ($manual_support_A + $manual_support_B + $manual_support_C);
$manual_f1_micro = $manual_precision_micro; // In multiclass, micro F1 = micro precision = micro recall

echo "F1 Macro = ({$manual_f1_A}+{$manual_f1_B}+{$manual_f1_C})/3 = {$manual_f1_macro}\n";
echo "F1 Weighted = ({$manual_f1_A}*{$manual_support_A}+{$manual_f1_B}*{$manual_support_B}+{$manual_f1_C}*{$manual_support_C})/300 = {$manual_f1_weighted}\n";
echo "F1 Micro = Precision Micro = {$manual_f1_micro}\n\n";

$manual_results['f1_macro'] = $manual_f1_macro;
$manual_results['f1_weighted'] = $manual_f1_weighted;
$manual_results['f1_micro'] = $manual_f1_micro;

// ========================================================================
// STEP 3: Automated Calculations (Using Classes)
// ========================================================================

echo "🤖 AUTOMATED CALCULATIONS:\n";
echo "==========================\n\n";

$automated_results = [];

// Basic metrics automated
echo "1️⃣ Basic Metrics (Automated):\n";
echo "-----------------------------\n";

$tp_automated = $cm->truePositives();
$fp_automated = $cm->falsePositives();
$fn_automated = $cm->falseNegatives();
$support_automated = $cm->support();

echo "TP: ".json_encode($tp_automated)."\n";
echo "FP: ".json_encode($fp_automated)."\n";
echo "FN: ".json_encode($fn_automated)."\n";
echo "Support: ".json_encode($support_automated)."\n\n";

// Precision automated
echo "2️⃣ Precision (Automated):\n";
echo "-------------------------\n";

$precision_raw = new Precision();
$precision_macro = new Precision(AverageStrategy::MACRO);
$precision_micro = new Precision(AverageStrategy::MICRO);
$precision_weighted = new Precision(AverageStrategy::WEIGHTED);

$precision_raw_result = $precision_raw->measure($cm);
$precision_macro_result = $precision_macro->measure($cm);
$precision_micro_result = $precision_micro->measure($cm);
$precision_weighted_result = $precision_weighted->measure($cm);

echo "Raw: ".json_encode($precision_raw_result)."\n";
echo "Macro: {$precision_macro_result}\n";
echo "Micro: {$precision_micro_result}\n";
echo "Weighted: {$precision_weighted_result}\n\n";

$automated_results['precision_raw_A'] = $precision_raw_result['A'];
$automated_results['precision_raw_B'] = $precision_raw_result['B'];
$automated_results['precision_raw_C'] = $precision_raw_result['C'];
$automated_results['precision_macro'] = $precision_macro_result;
$automated_results['precision_micro'] = $precision_micro_result;
$automated_results['precision_weighted'] = $precision_weighted_result;

// Recall automated
echo "3️⃣ Recall (Automated):\n";
echo "----------------------\n";

$recall_raw = new Recall();
$recall_macro = new Recall(AverageStrategy::MACRO);
$recall_micro = new Recall(AverageStrategy::MICRO);
$recall_weighted = new Recall(AverageStrategy::WEIGHTED);

$recall_raw_result = $recall_raw->measure($cm);
$recall_macro_result = $recall_macro->measure($cm);
$recall_micro_result = $recall_micro->measure($cm);
$recall_weighted_result = $recall_weighted->measure($cm);

echo "Raw: ".json_encode($recall_raw_result)."\n";
echo "Macro: {$recall_macro_result}\n";
echo "Micro: {$recall_micro_result}\n";
echo "Weighted: {$recall_weighted_result}\n\n";

$automated_results['recall_raw_A'] = $recall_raw_result['A'];
$automated_results['recall_raw_B'] = $recall_raw_result['B'];
$automated_results['recall_raw_C'] = $recall_raw_result['C'];
$automated_results['recall_macro'] = $recall_macro_result;
$automated_results['recall_micro'] = $recall_micro_result;
$automated_results['recall_weighted'] = $recall_weighted_result;

// F1-Score automated
echo "4️⃣ F1-Score (Automated):\n";
echo "------------------------\n";

$f1_raw = new F1Score();
$f1_macro = new F1Score(AverageStrategy::MACRO);
$f1_micro = new F1Score(AverageStrategy::MICRO);
$f1_weighted = new F1Score(AverageStrategy::WEIGHTED);

$f1_raw_result = $f1_raw->measure($cm);
$f1_macro_result = $f1_macro->measure($cm);
$f1_micro_result = $f1_micro->measure($cm);
$f1_weighted_result = $f1_weighted->measure($cm);

echo "Raw: ".json_encode($f1_raw_result)."\n";
echo "Macro: {$f1_macro_result}\n";
echo "Micro: {$f1_micro_result}\n";
echo "Weighted: {$f1_weighted_result}\n\n";

$automated_results['f1_raw_A'] = $f1_raw_result['A'];
$automated_results['f1_raw_B'] = $f1_raw_result['B'];
$automated_results['f1_raw_C'] = $f1_raw_result['C'];
$automated_results['f1_macro'] = $f1_macro_result;
$automated_results['f1_micro'] = $f1_micro_result;
$automated_results['f1_weighted'] = $f1_weighted_result;

// ========================================================================
// STEP 4: Comparison and Validation
// ========================================================================

echo "🔍 VALIDATION RESULTS:\n";
echo "======================\n\n";

$tolerance = 0.0001; // Tolerance for floating point comparison
$all_passed = true;
$failed_tests = [];

echo "Comparing Manual vs Automated calculations (tolerance: {$tolerance}):\n";
echo str_repeat("-", 70)."\n";
printf("%-25s %-15s %-15s %-10s\n", "Metric", "Manual", "Automated", "Status");
echo str_repeat("-", 70)."\n";

foreach ($manual_results as $metric => $manual_value) {
    $automated_value = $automated_results[$metric];
    $difference = abs($manual_value - $automated_value);
    $passed = $difference <= $tolerance;

    if ( ! $passed) {
        $all_passed = false;
        $failed_tests[] = $metric;
    }

    $status = $passed ? "✅ PASS" : "❌ FAIL";
    printf("%-25s %-15.6f %-15.6f %-10s\n", $metric, $manual_value, $automated_value, $status);

    if ( ! $passed) {
        printf("  └─ Difference: %.8f\n", $difference);
    }
}

echo str_repeat("-", 70)."\n\n";

// Final summary
if ($all_passed) {
    echo "🎉 ALL TESTS PASSED! Manual and automated calculations match.\n";
    echo "✅ The implementation is mathematically correct.\n\n";
} else {
    echo "❌ SOME TESTS FAILED! Found discrepancies in:\n";
    foreach ($failed_tests as $failed_test) {
        echo "  - {$failed_test}\n";
    }
    echo "\n⚠️  Please check the implementation for errors.\n\n";
}

echo "📊 Summary:\n";
echo "Total metrics tested: ".count($manual_results)."\n";
echo "Passed: ".(count($manual_results) - count($failed_tests))."\n";
echo "Failed: ".count($failed_tests)."\n";
echo "Success rate: ".round(((count($manual_results) - count($failed_tests)) / count($manual_results)) * 100, 1)."%\n\n";

echo "🏁 Validation completed!\n";
