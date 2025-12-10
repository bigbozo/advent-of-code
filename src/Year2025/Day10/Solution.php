<?php

namespace Bizbozo\AdventOfCode\Year2025\Day10;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{


    private array $combinations;

    public function getTitle(): string
    {
        return "Day 10 - Factory";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        $data = static::parseData($inputStream);

        $fewestButtonPresses = $this->countFewestButtonPreses($data);


        return new SolutionResult(
            10,
            new UnitResult("The 1st answer is %s", [$fewestButtonPresses]),
            new UnitResult('The 2nd answer is %s', [0])
        );
    }


    private function parseData(string $stream)
    {
        $result = [];
        $lines = explode(PHP_EOL, $stream);
        foreach ($lines as $line) {
            $parts = explode(" ", $line);
            if (count($parts) < 3) continue;

            $lightDiagram = array_shift($parts);
            $joltage = array_pop($parts);

            $result[] = [
                'numLights' => strlen($lightDiagram) - 2,
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
                'joltage' => explode(',', substr($joltage, 1, -1)),
                'buttons' => array_map(function ($part) {
                    $lightsOfButton = array_map('intval', explode(",", substr($part, 1, -1)));
                    return array_sum(array_map(fn($light) => pow(2, $light), $lightsOfButton));
                }, $parts)
            ];
        }

        return $result;

    }

    private function countFewestButtonPreses(array $data): int
    {
        $buttonCount = 0;
        foreach ($data as $item) {
            $combinations = $this->getCombinations(count($item['buttons']));
            foreach ($combinations as $mask => $count) {
                if ($this->testButtonCombination($mask, $item)) {
                    break;
                };
            }
            $buttonCount += $count;
        }
        return $buttonCount;
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

    /**
     * @param int|string $mask
     * @param mixed $item
     * @param mixed $count
     * @return void
     */
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
        };
        return false;
    }
}
