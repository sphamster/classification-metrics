<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Contracts;

use Sphamster\ClassificationMetrics\ConfusionMatrix;

interface Metric
{
    /**
     * @return array<string, float>|float
     */
    public function measure(ConfusionMatrix $confusion_matrix): array|float; //todo: in trait, con chiamata a funzione per capire che chiamata fare
}
