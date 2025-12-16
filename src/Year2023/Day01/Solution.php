<?php

namespace Bizbozo\AdventOfCode\Year2023\Day01;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{

    private function parseData(string $stream)
    {
        return 1;
    }

    public function getTitle(): string
    {
        return "Day 1 - ";
    }

    #[Override] public function solve(string $inputStream, string $inputStream2 = null): SolutionResult
    {

        $data = static::parseData($inputStream);

        return new SolutionResult(
            1,
            new UnitResult("The 1st answer is %s", [0]),
            new UnitResult('The 2nd answer is %s',[0])
        );
    }
}
