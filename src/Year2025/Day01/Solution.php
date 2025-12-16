<?php

namespace Bizbozo\AdventOfCode\Year2025\Day01;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;


class Solution extends AbstractSolution
{

    /**
     * @return array<Rotation>
     */
    private function parseData(string $stream): array
    {
        return array_map(function ($item) {
            return new Rotation(
                substr($item, 0, 1) == 'L'
                    ? Directions::LEFT
                    : Directions::RIGHT,
                (int)substr($item, 1)
            );
        },
            explode(PHP_EOL, $stream)
        );
    }

    public function getTitle(): string
    {
        return "Day 1 - Secret Entrance";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $data = static::parseData($inputStream);

        $result = $this->runSimulation($data);
        $result2 = $this->runSimulation($data, true);

        return new SolutionResult(
            1,
            new UnitResult("The dial stops %s times at 0", [$result]),
            new UnitResult('When counting the clicks passing zero the answer is %s', [$result2])
        );
    }

    /**
     * @param array<Rotation> $data
     * @return void
     */
    private function runSimulation(array $data, $withZeroClick = false): int
    {
        $cursor = 50;
        $count = 0;
        foreach ($data as $item) {

            $count += $withZeroClick ? $item->zeroClicks($cursor) : 0;

            $cursor = match ($item->direction) {
                Directions::LEFT => (($cursor - $item->amount ) % 100 + 100) % 100,
                Directions::RIGHT => ($cursor + $item->amount) % 100
            };

            if ($cursor == 0) {
                $count++;
            }
        }

        return $count;
    }
}
