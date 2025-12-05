<?php

namespace Bizbozo\AdventOfCode\Year2025\Day05;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    private function parseData(string $stream): array
    {
        list($ranges, $numbers) = explode(PHP_EOL . PHP_EOL, $stream);

        return [
            array_map(fn($item) => explode('-', $item), explode(PHP_EOL, $ranges)),
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

        [$ranges, $ingredientIds] = static::parseData($inputStream);

        $freshIngredients = 0;
        foreach ($ingredientIds as $id) {

            foreach ($ranges as $range) {
                if ($id >= $range[0] && $id <= $range[1]) {
                    $freshIngredients++;
                    break;
                }
            }
        }

        usort($ranges, fn($a, $b) => $a[0] <=> $b[0]);

        $rightBound = 0;
        $numberOfFreshIngredientIds = 0;

        foreach ($ranges as $range) {

            if ($range[0] <= $rightBound) {
                if ($range[1] <= $rightBound) {
                    continue;
                }
                $range[0] = $rightBound + 1;
            }

            $numberOfFreshIngredientIds += $range[1] - $range[0] + 1;
            $rightBound = $range[1];

        }

        return new SolutionResult(
            5,
            new UnitResult("%s ingredients are fresh", [$freshIngredients]),
            new UnitResult('%s ids are considered fresh', [$numberOfFreshIngredientIds])
        );
    }
}
