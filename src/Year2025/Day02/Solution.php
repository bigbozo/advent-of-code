<?php

namespace Bizbozo\AdventOfCode\Year2025\Day02;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    private $invalidIds = [];

    private function parseData(string $stream): array
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
        $this->invalidIds = [];

        $invalidCount = 0;
        foreach ($data as $block) {
            $invalidBlockCount = $this->invalidsInBlockSum($block[0], $block[1], 2);
            $invalidCount += $invalidBlockCount;
        }

        $invalidCount2 = $invalidCount;
        foreach ($data as $block) {
            for ($i = 3; $i <= strlen($block[1]); $i++) {
                $invalidsInBlockSum = $this->invalidsInBlockSum($block[0], $block[1], $i);
                $invalidCount2 += $invalidsInBlockSum;
            }
        }

        return new SolutionResult(
            2,
            new UnitResult("The sum of all invalid IDs: %s", [$invalidCount]),
            new UnitResult('The sum of all quirky IDs is %s', [$invalidCount2])
            // wrong 14581078894
        );
    }

    private function invalidsInBlockSum(int $start, int $end, $repetitions): int
    {
        $startLength = strlen($start);

        if ($startLength % $repetitions) {
            $nextValidLength = $startLength - ($startLength % $repetitions) + $repetitions;
            $start = intval(pow(10, $nextValidLength - 1));
            return $this->invalidsInBlockSum($start, $end, $repetitions);
        }

        if ($start > $end) return 0;


        $endLength = strlen($end);
        if ($endLength % $repetitions) {
            $lastValidLength = $endLength - ($endLength % $repetitions);
            $end = pow(10, $lastValidLength) - 1;
            return $this->invalidsInBlockSum($start, $end, $repetitions);
        }

        $idSum = 0;

        $length = $startLength / $repetitions;

        if ($startLength == $endLength) {
            $minLeft = intval(substr($start, 0, $length));
            $maxLeft = intval(substr($end, 0, $length));

            for ($i = $minLeft; $i <= $maxLeft; $i++) {

                $value = $i;
                for ($j = 1; $j < $repetitions; $j++) {
                    $value = $value * pow(10, $length) + $i;
                }

                $clampedId = $value <= $end
                    ? (
                    $value >= $start
                        ? $value
                        : 0
                    )
                    : 0;
                if ($clampedId) {
                    if (!isset($this->invalidIds[$clampedId])) {
                        $idSum += $clampedId;
                    }
                    $this->invalidIds[$clampedId]=true;
                }
            }
            return $idSum;
        }

        // end is longer than start
        // id-sum for start-length
        $idSum = $this->invalidsInBlockSum($start, pow(10, $startLength + 1) - 1, $repetitions);


        // id-sum between start- and end-length
        if (($endLength - $startLength) / $repetitions > 1) {

            for ($i = $startLength + $repetitions; $i < $endLength; $i++) {
                // all numbers with length i and given left half-string
                // have exactly one right half which makes it symmetric
                // e.g. length 4:
                // every number in the format <10-99><same> is invalid
                // sum of 10 + 11 + ... + 98 + 99 = (10 + 99) + (11 + 98) + ... (54 + 55) = 109 * (54 - 10 + 1)
                $left = pow(10, $i);
                $right = pow(10, $i + $repetitions - 1) - 1;

                $tupleSum = ($left + $right) * (($right - $left + 1) / 2);
                $value = $tupleSum;
                for ($j = 1; $j < $repetitions; $j++) {
                    $value = $value * pow(10, $length) + $tupleSum;
                }
                $idSum += $value;
            }
        }

        // id-sum with end-length
        $idSum += $this->invalidsInBlockSum(pow(10, $endLength - 1), $end, $repetitions);

        return $idSum;
    }

}
