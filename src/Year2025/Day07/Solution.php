<?php

namespace Bizbozo\AdventOfCode\Year2025\Day07;

use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution implements SolutionInterface
{

    private array $lines;

    /** @var array<string,int> */
    private array $cache;
    private int $start;

    private function parseData(string $stream)
    {
        // put stream into matrix
        $lines = array_map('str_split', explode(PHP_EOL, $stream));
        // find start
        $this->start = array_search('S', $lines[0]);
        // remove noise
        $this->lines = array_values(array_filter($lines, fn($item) => array_search('^', $item) !== false));

    }

    public function getTitle(): string
    {
        return "Day 7 - Laboratories";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        static::parseData($inputStream);

        return new SolutionResult(
            7,
            new UnitResult("The beam will be split %s times", [$this->getNumberOfSplits()]),
            new UnitResult('A single Tachyon ends on %s timelines', [$this->getNumberOfTimelines($this->start, 0)])
        );
    }

    public function getNumberOfSplits(): int
    {
        $splits = 0;
        $activeColumns = [$this->start => true];
        foreach ($this->lines as $line) {
            $activeColumnsNext = $activeColumns;
            foreach ($line as $column => $char) {
                if ($char === '^' && ($activeColumns[$column] ?? false)) {
                    $activeColumnsNext[$column - 1] = true;
                    $activeColumnsNext[$column + 1] = true;
                    $activeColumnsNext[$column] = false;
                    $splits++;
                };
            }

            $activeColumns = $activeColumnsNext;
        }
        return $splits;
    }

    private function getNumberOfTimelines(int $start, int $level)
    {

        if (isset($this->cache[$this->getCacheKey($start, $level)])) {
            // already calculated
            return $this->cache[$this->getCacheKey($start, $level)];
        }
        if ($level >= count($this->lines)) {
            return 1;
        }

        $line = $this->lines[$level];

        if (($line[$start] ?? '') === '^')
            $value =
                $this->getNumberOfTimelines($start + 1, $level + 1) +
                $this->getNumberOfTimelines($start - 1, $level + 1);
        else
            $value = $this->getNumberOfTimelines($start, $level + 1);

        $this->cache[$this->getCacheKey($start, $level)] = $value;

        return $value;
    }

    /**
     * @param int $start
     * @param int $level
     * @return string
     */
    public function getCacheKey(int $start, int $level): string
    {
        return $start . '-' . $level;
    }
}
