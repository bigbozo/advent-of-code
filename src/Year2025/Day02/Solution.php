<?php

namespace Bizbozo\AdventOfCode\Year2025\Day02;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    private function parseData(string $stream)
    {
        return array_map(
            fn(string $tuple) => array_map('intval', explode("-", $tuple)),
            explode(',', $stream)
        );
    }

    public function getTitle(): string
    {
        return "Day 2 - Gift Shop";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        $data = static::parseData($inputStream);

        $invalidCount = 0;
        foreach ($data as $block) {
            $invalidBlockCount = $this->invalidsInBlockSum($block[0], $block[1]);
            $invalidCount += $invalidBlockCount;
        }

        return new SolutionResult(
            2,
            new UnitResult("The sum of all invalid IDs: %s", [$invalidCount]),
            new UnitResult('The 2nd answer is %s', [0])
        );
    }

    private function invalidsInBlockSum(int $start, int $end): int
    {
        $startLength = strlen($start);

        if ($startLength % 2) {
            $start = pow(10, $startLength);
            return $this->invalidsInBlockSum($start, $end);
        }

        if ($start > $end) return 0;


        $endLength = strlen($end);
        if ($endLength % 2) {
            $end = pow(10, ($endLength - 1)) - 1;
            return $this->invalidsInBlockSum($start, $end);
        }

        $idSum = 0;

        if ($startLength == $endLength) {
            $minLeft = intval(substr($start, 0, $startLength / 2));
            $maxLeft = intval(substr($end, 0, $endLength / 2));

            for ($i = $minLeft; $i <= $maxLeft; $i++) {
                $value = $i * pow(10, $startLength / 2) + $i;
                $idSum += $value <= $end
                    ? (
                    $value >= $start
                        ? $value
                        : 0
                    )
                    : 0;
            }
            return $idSum;
        }

        // end is longer than start
        $idSum = $this->invalidsInBlockSum($start, pow(10, $startLength + 1) - 1);

        // id-sum between start- and end-length
        if ($endLength - $startLength > 2) {
            for ($i = $startLength + 2; $i < $endLength; $i++) {
                // all numbers with length i and given left half string
                // have exactly one right half which makes it symmetric
                // e.g. length 4:
                // every number in the format <10-99><same> is invalid
                // sum of 10 + 11 + ... + 98 + 99 = (10 + 99) + (11 + 98) + ... (54 + 55) = 109 * (54 - 10 + 1)
                $left = pow(10, $i);
                $right = pow(10, $i + 1) - 1;
                $idSum += ($left + $right) * (($right - $left + 1) / 2);
            }
        }

        // id-sum with end-length
        $idSum += $this->invalidsInBlockSum(pow(10, $endLength - 1), $end);

        return $idSum;
    }

    private function reverse(int $i): int
    {
        return intval(strrev($i));
    }
}
