<?php

declare(strict_types=1);

namespace Bizbozo\AdventOfCode\Year2025\Day04;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    /**
     * @var array|array[]
     */
    private array $data;

    private function parseData(string $stream): array
    {
        return array_map(fn($item) => str_split($item), explode(PHP_EOL, $stream));
    }

    public function getTitle(): string
    {
        return "Day 4 - Printing Department";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        $data = static::parseData($inputStream);

        $this->data = $data;
        $accessible = $this->getAccessibleRoles();

        $accessibleRoles = $accessible;

        $totalAccessibleRoles = 0;
        while ($accessible) {
            $totalAccessibleRoles += $accessible;
            $accessible = $this->getAccessibleRoles();
        }


        return new SolutionResult(
            4,
            new UnitResult("The 1st answer is %s", [$accessibleRoles]),
            new UnitResult('The 2nd answer is %s', [$totalAccessibleRoles])
        );
    }

    private function getAccessibleRoles(): int
    {
        $accessibleRemoved = $this->data;
        $accessible = 0;
        foreach ($this->data as $x=>$row) {
            foreach($row as $y => $char) {
                if ($this->get($x, $y) === '@') {
                    $neighbours = $this->countNeighbours($x, $y);
                    if ($neighbours < 4) {
                        $accessible += 1;
                        $accessibleRemoved[$x][$y] = '.';
                    }
                }
            }
        }
        $this->data = $accessibleRemoved;
        return $accessible;
    }

    private function countNeighbours(int $x, int $y): int
    {
        $neighbours = 0;
        for ($i = -1; $i <= 1; $i++) {
            for ($j = -1; $j <= 1; $j++) {
                if ($i || $j) {
                    if ($this->get($x + $i, $y + $j) === '@') {
                        $neighbours++;
                    }
                }
            }
        }
        return $neighbours;
    }

    private function get(int $x, int $y): string
    {
        if (isset($this->data[$x])) {
            if (isset($this->data[$x][$y])) {
                return $this->data[$x][$y];
            }
        }
        return '-';
    }


}
