<?php

declare(strict_types=1);

namespace Sphamster\ClassificationMetrics\Concerns;

trait IsStringable
{
    /**
     * Generates a header string for the matrix representation.
     *
     * This method creates a header row with padded labels from the implementing class.
     * It adds initial spacing and then concatenates all padded labels.
     *
     * @return string The formatted header string
     */
    private function header(): string
    {
        $paddedLabels = array_map($this->pad(...), $this->labels());

        return str_repeat(' ', 12).implode('', $paddedLabels);
    }

    /**
     * Generates an array of formatted row strings for the matrix representation.
     *
     * This method creates a string representation for each row in the matrix,
     * with the row label padded and followed by the formatted row values.
     *
     * @return array<string> Array of formatted row strings
     */
    private function rows(): array
    {
        return array_map(
            fn (int $i, string $label): string => $this->pad($label, 12).$this->row($i),
            array_keys($this->labels()),
            $this->labels()
        );
    }

    /**
     * Generates a formatted string for a specific row in the matrix.
     *
     * This method takes a row index, retrieves the corresponding values from the matrix,
     * formats each value with padding, and concatenates them into a single string.
     *
     * @param int $row_index The index of the row to format
     * @return string The formatted row string
     */
    private function row(int $row_index): string
    {
        $values = array_map(
            fn (int $value): string => $this->pad((string)$value),
            $this->matrix()[$row_index]
        );

        return implode('', $values);
    }

    /**
     * Pads a text string to a specified width.
     *
     * This method uses mb_str_pad to ensure proper handling of multi-byte characters,
     * padding the text with spaces on the left to reach the specified width.
     *
     * @param string $text The text to pad
     * @param int $width The width to pad to (default: 8)
     * @return string The padded text
     */
    private function pad(string $text, int $width = 8): string
    {
        return mb_str_pad($text, $width, ' ', STR_PAD_LEFT);
    }

    /**
     * Generates a string representation of the matrix.
     *
     * This method combines the header and rows with line breaks to create
     * a formatted string representation of the entire matrix.
     *
     * @return string The string representation of the matrix
     */
    public function __toString(): string
    {
        $header = $this->header();
        $rows = $this->rows();

        return $header.PHP_EOL.implode(PHP_EOL, $rows).PHP_EOL;
    }

}
