<?php

namespace Bizbozo\AdventOfCode\Year2025\Day06;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{

    private function parseData(string $stream)
    {
        $lines =
            array_map(
                fn($item) => preg_split('/\\s+/', $item, flags: PREG_SPLIT_NO_EMPTY),
                explode(PHP_EOL, trim($stream))
            );
        $operators = array_pop($lines);
        $columns = [];
        foreach (array_keys($operators) as $columnKey) {
            $columns[] = array_column($lines, $columnKey);
        }

        return [
            $operators,
            $columns
        ];
    }

    /**
     * @param mixed $operators
     * @param mixed $columns
     * @return float|int
     */


    private function parseDataCephalopodStyle(string $stream): array
    {

        $operators = '';
        $lines = explode(PHP_EOL, $stream);
        while (!trim($operators)) {
            $operators = array_pop($lines);
        }

        if (!preg_match_all('/((\+|\*)\s+)/', $operators . ' ', $matches)) {
            throw new \Exception('Data format erroneous.');
        }

        $start = 0;
        $operators = [];
        $columns = [];

        foreach ($matches[1] as $columnKey => $column) {

            $operators[] = $matches[2][$columnKey];
            $length = strlen($column) - 1;
            $numberBlock = array_map(fn($line) => rtrim(substr($line, $start, $length)), $lines);

            $columns[] = $this->rotateBlock($length, $numberBlock);

            $start += $length + 1;

        }
        return [$operators, $columns];

    }

    private function rotateBlock(int $length, array $numberBlock): array
    {
        $finalNumbers = array_fill(0, $length, 0);
        for ($i = 0; $i < $length; $i++) {
            foreach ($numberBlock as $number) {
                $digit = intval(substr($number, $i, 1));
                if ($digit) {
                    $finalNumbers[$i] = $finalNumbers[$i] * 10 + $digit;
                }
            }
        }
        return $finalNumbers;
    }

    public function getTitle(): string
    {
        return "Day 6 - Trash Compactor";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        [$operators, $columns] = static::parseData($inputStream);
        $grandTotal = $this->getGrandTotal($operators, $columns);

        [$operators, $columns] = static::parseDataCephalopodStyle($inputStream);
        $grandTotalCephalopodStyle = $this->getGrandTotal($operators, $columns);

        return new SolutionResult(
            6,
            new UnitResult("The grand Total is %s", [$grandTotal]),
            new UnitResult('The 2nd answer is %s', [$grandTotalCephalopodStyle])
        );
    }

    private function getGrandTotal(mixed $operators, mixed $columns): int
    {
        $grandTotal = 0;
        foreach ($operators as $columnKey => $operator) {
            switch ($operator) {
                case '+':
                    $grandTotal += array_sum($columns[$columnKey]);
                    break;
                case '*':
                    $grandTotal += array_product($columns[$columnKey]);
            }
        }
        return $grandTotal;
    }

}
