# Classification Metrics for PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sphamster/classification-metrics.svg?style=flat-square)](https://packagist.org/packages/sphamster/classification-metrics)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sphamster/classification-metrics/pull-request.yml?branch=master&label=tests&style=flat-square)](https://github.com/sphamster/classification-metrics/actions?query=workflow%3Arun-tests+branch%3Amain)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/dependency-v/sphamster/classification-metrics/php.svg?style=flat-square)](composer.json)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat-square)](https://phpstan.org/)
[![Code Coverage](https://img.shields.io/codecov/c/github/sphamster/classification-metrics?style=flat-square)](https://codecov.io/gh/sphamster/classification-metrics)

A PHP package for computing confusion matrices and classification metrics for machine learning models.

## Installation

You can install the package via composer:

```bash
composer require sphamster/classification-metrics
```

## Requirements

- PHP 8.1 or higher

## Usage

### Creating a Confusion Matrix

You can create a confusion matrix directly from predictions:

```php
use Sphamster\ClassificationMetrics\ConfusionMatrix;

// Your ground truth labels
$true_labels = ['cat', 'dog', 'cat', 'bird', 'dog', 'bird'];

// Your model's predictions
$predicted_labels = ['cat', 'dog', 'dog', 'bird', 'cat', 'bird'];

// Optional: specify the order of labels (if omitted, will use unique values from true_labels)
$labels = ['cat', 'dog', 'bird'];

// Create the confusion matrix
$confusion_matrix = ConfusionMatrix::fromPredictions($true_labels, $predicted_labels, $labels);
```

Or you can create it directly from a matrix:

```php
$labels = ['cat', 'dog', 'bird'];
$matrix = [
    [5, 1, 0],  // cat:  5 correct, 1 as dog, 0 as bird
    [2, 8, 1],  // dog:  2 as cat, 8 correct, 1 as bird
    [0, 0, 6]   // bird: 0 as cat, 0 as dog, 6 correct
];

$confusion_matrix = new ConfusionMatrix($labels, $matrix);
```

### Extracting Basic Metrics

The confusion matrix provides methods to extract basic metrics:

```php
// Get true positives for all classes
$tp = $confusion_matrix->truePositives();
// Or for a specific class
$tp_cat = $confusion_matrix->truePositives('cat');

// Similarly for false positives, false negatives, and true negatives
$fp = $confusion_matrix->falsePositives();
$fn = $confusion_matrix->falseNegatives();
$tn = $confusion_matrix->trueNegatives();
```

### Computing Classification Metrics

The package provides implementations for common classification metrics:

#### Precision

```php
use Sphamster\ClassificationMetrics\Metrics\Precision;
use Sphamster\ClassificationMetrics\Enums\AverageStrategy;

// Get precision for each class
$precision = new Precision();
$class_precision = $precision->measure($confusion_matrix);

// Get macro-averaged precision
$macro_precision = (new Precision(AverageStrategy::MACRO))->measure($confusion_matrix);

// Get micro-averaged precision
$micro_precision = (new Precision(AverageStrategy::MICRO))->measure($confusion_matrix);

// Get weighted-averaged precision
$weighted_precision = (new Precision(AverageStrategy::WEIGHTED))->measure($confusion_matrix);
```

#### Recall

```php
use Sphamster\ClassificationMetrics\Metrics\Recall;

// Get recall for each class
$recall = new Recall();
$class_recall = $recall->measure($confusion_matrix);

// Similarly, you can use AverageStrategy for macro, micro, and weighted averaging
```

#### F1 Score

```php
use Sphamster\ClassificationMetrics\Metrics\F1Score;

// Get F1 score for each class
$f1 = new F1Score();
$class_f1 = $f1->measure($confusion_matrix);

// Similarly, you can use AverageStrategy for macro, micro, and weighted averaging
```

## Averaging Strategies

The package supports three averaging strategies for multi-class metrics:

- **Macro**: Calculate metrics for each label and find their unweighted mean. This does not take label imbalance into account.
- **Micro**: Calculate metrics globally by counting the total true positives, false negatives, and false positives.
- **Weighted**: Calculate metrics for each label and find their average weighted by support (the number of true instances for each label).

## Testing

```bash
composer test
```

## Code Quality

The package includes tools for maintaining code quality:

```bash
# Run code style fixer
composer lint

# Run static analysis
composer test:types

# Run refactoring tool
composer refactor

# Run all checks
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Author

- [Andrea Civita](https://github.com/andreacivita)
