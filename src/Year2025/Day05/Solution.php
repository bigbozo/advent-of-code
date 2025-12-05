<?php

namespace Bizbozo\AdventOfCode\Year2025\Day05;

use Bizbozo\AdventOfCode\Ranges\Range;
use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    private function parseData(string $stream)
    {
        list($patterns, $numbers) = explode(PHP_EOL . PHP_EOL, $stream);

        return [
            array_map(fn($item) => explode('-', $item), explode(PHP_EOL, $patterns)),
            explode(PHP_EOL, $numbers)
        ];
    }

    public function getTitle(): string
    {
        return "Day 5 - Cafeteria";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        [$patterns, $numbers] = static::parseData($inputStream);

        $freshIngredients = 0;
        foreach ($numbers as $number) {
            $fresh = false;
            foreach ($patterns as $pattern) {
                if ($number >= $pattern[0] && $number <= $pattern[1]) {
                    $freshIngredients++;
                    break;
                }
            }
        }

        usort($patterns, fn($a, $b) => $a[0] <=> $b[0]);

        $right = 0;
        $total = 0;
        foreach ($patterns as $pattern) {

            if ($pattern[0] <= $right) {
                if ($pattern[1] <= $right) {
                    continue;
                }
                $pattern[0] = $right + 1;
            }
            $total += $pattern[1] - $pattern[0] + 1;
            $right = $pattern[1];

        }

        return new SolutionResult(
            5,
            new UnitResult("%s ingredients are fresh", [$freshIngredients]),
            new UnitResult('%s ids are considered fresh', [$total])
        );
    }
}
