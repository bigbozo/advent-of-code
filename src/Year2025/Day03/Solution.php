<?php

namespace Bizbozo\AdventOfCode\Year2025\Day03;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Bizbozo\AdventOfCode\Utility\Parser;
use Override;

class Solution extends AbstractSolution
{

    private function parseData(string $stream)
    {
        return
            array_map(function ($line) {
                return [
                    'data' => str_split($line),
                    'line' => $line
                ];
            }, explode(PHP_EOL, $stream));
    }

    public function getTitle(): string
    {
        return "Day 3 - Lobby";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $data = static::parseData(trim($inputStream));

        $maxJoltage = $this->getMaxJoltage($data);
        $maxJoltageWithSafetyOverride = $this->getMaxJoltageWithSafetyOverride($data);

        return new SolutionResult(
            3,
            new UnitResult("The max output joltage is %s jolts", [$maxJoltage]),
            new UnitResult('The 2nd answer is %s', [$maxJoltageWithSafetyOverride])
        );
    }

    private function getMaxJoltage(array $data): int
    {
        $sum = 0;
        foreach ($data as $row) {
            $sum += $this->joltage($row['data'], 2);
        }
        return $sum;
    }

    private function getMaxJoltageWithSafetyOverride(array $data): int
    {
        $sum = 0;
        foreach ($data as $row) {
            $sum += $this->joltage($row['data'], 12);
        }
        return $sum;

    }

    /**
     * @param mixed $batteryRow
     * @param $line1
     * @return array
     */
    public function joltage(array $batteryRow, $length): string
    {
        $digit = max(array_slice($batteryRow, 0, count($batteryRow) - ($length - 1)));
        $first = array_search($digit, $batteryRow);
        if ($length > 1) {
            return $digit . $this->joltage(array_slice($batteryRow, $first + 1), $length - 1);
        }
        return $digit;
    }

}
