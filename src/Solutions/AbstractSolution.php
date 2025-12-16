<?php

namespace Bizbozo\AdventOfCode\Solutions;

use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractSolution implements SolutionInterface
{

    protected SymfonyStyle $style;

    abstract public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult;

    abstract public function getTitle(): string;

    public function setStyle(SymfonyStyle $style)
    {
        $this->style = $style;
    }
}