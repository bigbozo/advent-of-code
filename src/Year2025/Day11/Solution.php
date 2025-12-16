<?php

namespace Bizbozo\AdventOfCode\Year2025\Day11;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{


    private array $data;
    private bool $isFft = false;
    private bool $isDac = false;
    private mixed $cache;

    public function getTitle(): string
    {
        return "Day 11 - Reactor";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $this->data = static::parseData($inputStream);

        $result1 = $this->countPaths();

        if ($inputStream2) $this->data = static::parseData($inputStream2);

        $result2 = $this->countPathsOverDacFft();

        // 93437681886819081 is too high

        return new SolutionResult(
            11,
            new UnitResult("The 1st answer is %s", [$result1]),
            new UnitResult('The 2nd answer is %s', [$result2])
        );
    }

    private function parseData(string $stream)
    {
        return array_reduce(
            array_map(

                function ($line) {
                    [$key, $outputs] = explode(": ", $line);
                    $outputs = explode(" ", $outputs);

                    return [$key, $outputs];
                },

                explode(PHP_EOL, rtrim($stream))
            ),

            function ($carry, $item) {

                if (!$item[0]) return $carry;

                $carry[$item[0]] = $item[1];

                return $carry;
            },
            []
        );
    }

    private function countPaths(string $from = "you"): int
    {
        if ($from == "out") return 1;

        $outputs = $this->data[$from];

        return array_sum(array_map([$this, 'countPaths'], $outputs));

    }

    private function countPathsOverDacFft(string $from = "svr")
    {
        if (isset($this->cache[$this->getCacheKey($from)])) return $this->cache[$this->getCacheKey($from)];

        if ($from == "out") return ($this->isDac && $this->isFft) ? 1 : 0;

        $outputs = $this->data[$from];
        $sum = 0;
        foreach ($outputs as $output) {
            if ($output === "dac") {
                $this->isDac = true;
            }
            if ($output === "fft") {
                $this->isFft = true;
            }

            $sum += $this->countPathsOverDacFft($output);

            if ($output === "dac") {
                $this->isDac = false;
            }
            if ($output === "fft") {
                $this->isFft = false;
            }

        }

        $this->cache[$this->getCacheKey($from)] = $sum;

        return $sum;


    }

    private function getCacheKey(string $from)
    {
        return sprintf("%s/%s/%s", $this->isDac, $this->isFft, $from);
    }
}
