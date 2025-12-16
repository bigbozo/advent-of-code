<?php

namespace Bizbozo\AdventOfCode\Year2025\Day10;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{


    const float EPSILON = 0.05;
    private array $combinations;

    public function getTitle(): string
    {
        return "Day 10 - Factory";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $data = static::parseData($inputStream);

        [$fewestButtonPresses, $data] = $this->countFewestButtonPresses($data);

        $fewestPresses = $this->solvePart2($data);


        return new SolutionResult(
            10,
            new UnitResult("At least %s button presses are required to correctly configure the lights", [$fewestButtonPresses]),
            new UnitResult('The joltage lever counter can be configured with at least %s button presses', [$fewestPresses])
        );
    }

    private function parseData(string $stream): array
    {
        $result = [];
        $lines = explode(PHP_EOL, $stream);
        foreach ($lines as $line) {
            $parts = explode(" ", $line);
            if (count($parts) < 3) continue;

            $lightDiagram = array_shift($parts);
            $joltage = array_pop($parts);

            $width = strlen($lightDiagram) - 2;
            $result[] = [
                'numLights' => $width,
                'lightDiagram' => array_sum(
                    array_map(
                        fn($item) => pow(2, $item),
                        array_keys(
                            array_filter(
                                str_split(
                                    substr($lightDiagram, 1, -1)
                                ),
                                fn($item) => $item === '#'
                            )
                        )
                    )
                ),
                'joltage' => array_map('intval', explode(',', substr($joltage, 1, -1))),
                'buttons' => array_map(function ($part) {
                    $lightsOfButton = array_map('intval', explode(",", substr($part, 1, -1)));
                    return array_sum(array_map(fn($light) => pow(2, $light), $lightsOfButton));
                }, $parts),
                // part 2
                'buttons2' => array_map(function ($part) use ($width) {
                    $b = array_map('intval', explode(",", substr($part, 1, -1)));

                    return array_map(
                        fn($line, $key) => in_array($key, $b) ? 1 : 0,
                        array_fill(0, $width, 0),
                        range(0, $width - 1)
                    );
                }, $parts)


            ];
        }
        return $result;

    }

    private function countFewestButtonPresses(array $data): array
    {
        $buttonCount = 0;
        foreach ($data as $item) {
            $combinations = $this->getCombinations(count($item['buttons']));
            $minCount = 1000;
            foreach ($combinations as $mask => $count) {
                if ($this->testButtonCombination($mask, $item)) {
                    $minCount = min($minCount, $count);
                }
            }
            $buttonCount += $minCount;
        }
        return [$buttonCount, $data];
    }
    private function getCombinations(mixed $countButtons)
    {
        if ($this->combinations[$countButtons] ?? false) {
            return $this->combinations[$countButtons];
        }
        $upper = pow(2, $countButtons);
        $patterns = array_map('str_split',
            array_map('decbin',
                range(0, $upper - 1)
            )
        );
        $numbers = array_map(
            fn($amounts) => $amounts[1] ?? 0,
            array_map('array_count_values', $patterns)
        );

        asort($numbers);

        $this->combinations[$countButtons] = $numbers;

        return $numbers;
    }
    public function testButtonCombination(int $mask, array $item): bool
    {
        $state = 0;
        $button = 0;
        while ($mask) {
            if ($mask & 1) {
                $state = $state ^ $item['buttons'][$button];
            }
            $mask >>= 1;
            $button++;
        }
        if ($state == $item['lightDiagram']) {
            return true;
        }
        return false;
    }

    private function solvePart2(mixed $data)
    {

        $buttonPresses = 0;

        foreach ($data as $id => $machine) {
            $matrix = $this->normalizeMatrix($machine['buttons2'], $machine['joltage']);
            $countButtonPressesForMatrix = $this->countButtonPressesForMatrix($matrix, $machine['joltage']);
            $buttonPresses += $countButtonPressesForMatrix;
        }

        return $buttonPresses;
    }
    private function normalizeMatrix(mixed $buttons, mixed $joltages): array
    {

        $joltageCount = count($joltages);
        $matrix = [];
        for ($counter = 0; $counter < $joltageCount; $counter++) {
            $row = [];
            foreach ($buttons as $button) {
                $row[] = $button[$counter] ?? 0 ? 1 : 0;
            }
            $row[] = $joltages[$counter];
            $matrix[] = $row;
        }

        $matrix2 = [];
        $colId = 0;
        while (count($matrix)) {

            if ($colId > $joltageCount) break;

            $rows = [];
            for ($c = $colId; $c < count($buttons); $c++) {
                $rows = array_filter($matrix, fn($row) => round($row[$c], 3) ?? 0);
                if (count($rows)) break;
            }

            if (!count($rows)) {
                return $matrix2;
            }

            if ($c > $colId) {
                $matrix = $this->switchCols($matrix, $colId, $c);
                $matrix2 = $this->switchCols($matrix2, $colId, $c);
                continue;
            }


            uasort($rows, fn($a, $b) => count(array_filter($b)) <=> count(array_filter($a)));

            $foundId = array_key_first($rows);

            $currentRow = array_first($rows);
            $currentValue = $currentRow[$colId];
            $currentRow = array_map(fn($col) => $col / $currentValue, $currentRow);
            unset($matrix[$foundId]);
            foreach ($rows as $rowId => $row) {
                if ($rowId != $foundId) {
                    // 2 0 4    => 2 / 1 0 2
                    // -1 0 5    => -1/2 => -1 + 2 * 1/2
                    $scale = $row[$colId];
                    foreach ($currentRow as $col => $val) {
                        $matrix[$rowId][$col] -= $val * $scale;
                    }
                }
            }
            $matrix2[] = $currentRow;
            $colId++;




        }
        return $matrix2;
    }
    private function switchCols(array $matrix, int $col1, int $col2): array
    {
        return array_map(function ($row) use ($col1, $col2) {
            $swap = $row[$col1];
            $row[$col1] = $row[$col2];
            $row[$col2] = $swap;
            return $row;
        }, $matrix);

    }
    private function countButtonPressesForMatrix(array $matrix, mixed $joltage)
    {
        $maxPressesPossible = max($joltage);
        $minPresses = INF;
        $numberOfUnknowns = count($matrix[0]) - 1 - count($matrix);

        if ($numberOfUnknowns > 0) {
            for ($i = 0; $i <= $maxPressesPossible; $i++) {
                $line = array_merge(
                    array_fill(0, count($matrix), 0),
                    [1],
                    array_fill(0, $numberOfUnknowns - 1, 0),
                    [$i]
                );
                $minPresses = min($minPresses, $this->countButtonPressesForMatrix(array_merge($matrix, [$line]), $joltage));
            }

        } else {
            if ($numberOfUnknowns < 0) {
                $matrix = array_slice($matrix, 0, count($matrix) + $numberOfUnknowns);
            }
            return $this->solveEquation($matrix);

        }
        return $minPresses;
    }

    public function solveEquation(array $matrix): int|float
    {
        $known = array_fill(0, count($matrix), 0);
        $values = [];
        foreach ($matrix as &$line) {
            $values[] = array_pop($line);
        }
        unset($line);

        //
        for ($buttonId = count($matrix) - 1; $buttonId >= 0; $buttonId--) {

            $line = $matrix[$buttonId];
            // the first $buttonId is actually a row id but as we work along the diagonal coincidentally the same
            $lineValue = $values[$buttonId] - array_sum(array_map(fn($coefficient, $value) => $coefficient * $value, $line, $known));

            // has to be whole numbers, but could be slightly off due to rounding errors
            if (abs($lineValue - round($lineValue)) >= self::EPSILON) {
                return INF;
            }
            // no negative presses; testing for <0 instead of EPSILON cost me a massive time of my life,
            // the bug changed only 1 of 176 results off by 2
            if ($lineValue < -self::EPSILON) return INF;


            $known[$buttonId] = $lineValue;
        }
        return array_sum($known);
    }

    private function output(array $matrix): void
    {
        $this->style->writeln(
            implode(PHP_EOL, array_map(
                fn($row) => implode('', array_map(
                    fn($col) => str_pad(sprintf('%3.2f', $col), 8, ' ', STR_PAD_LEFT),
                    $row)),
                $matrix
            ))
        );
        $this->style->writeln('');
    }

}
