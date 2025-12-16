<?php

namespace Bizbozo\AdventOfCode\Solutions;

interface SolutionInterface
{
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true ): SolutionResult;
    public function getTitle(): string;

    public function setStyle(\Symfony\Component\Console\Style\SymfonyStyle $style);
}
