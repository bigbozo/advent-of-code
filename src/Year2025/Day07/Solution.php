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

    private function parseData(string $stream)
    {
        return array_map('str_split', explode(PHP_EOL, $stream));
    }

    public function getTitle(): string
    {
        return "Day 7 - Laboratories";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null): SolutionResult
    {

        $lines = static::parseData($inputStream);

        $splits = $this->getNumberOfSplits($lines);

        $start = array_search('S', $lines[0]);
        $this->lines = array_values(array_filter($lines, fn($item) => array_search('^', $item) !== false));

        $numOfTimelines = $this->getNumberOfTimelines($start, 0);


        return new SolutionResult(
            7,
            new UnitResult("The beam will be split %s times", [$splits]),
            new UnitResult('A single Tachyon ends on %s timelines', [$numOfTimelines])
        );
    }

    public function getNumberOfSplits(array $lines): int
    {
        $splits = 0;
        $activeColumns = [];
        foreach ($lines as $line) {
            $activeColumnsNext = $activeColumns;
            foreach ($line as $column => $char) {
                switch ($char) {
                    case 'S':
                        $activeColumnsNext[$column] = true;
                        break;
                    case '^':
                        if ($activeColumns[$column] ?? false) {
                            $activeColumnsNext[$column - 1] = true;
                            $activeColumnsNext[$column + 1] = true;
                            $activeColumnsNext[$column] = false;
                            $splits++;
                        };
                        break;

                }
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
