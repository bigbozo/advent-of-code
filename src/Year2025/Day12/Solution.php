<?php

namespace Bizbozo\AdventOfCode\Year2025\Day12;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionInterface;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{


    private array $blocks;
    private array $boards;
    private mixed $board;

    private int $callCounter = 0;
    private int $cacheHits = 0;
    private array $cache = [];

    public function getTitle(): string
    {
        return "Day 12 - ";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        if (function_exists('xdebug_disable')) {
            xdebug_disable();
        }

        $this->parseData($inputStream);

        $maxAmount = max(array_map('max', array_column($this->boards, "amounts")));
        $maxDimension = max(array_map('max', array_column($this->boards, "dimensions")));

        $validRegions = $this->calcValidRegions();

        return new SolutionResult(
            12,
            new UnitResult("The 1st answer is %s", [$validRegions]),
            new UnitResult('The 2nd answer is %s', [0])
        );
    }

    private function parseData(string $stream): void
    {
        $codeBlocks = explode(PHP_EOL . PHP_EOL, trim($stream));
        $boards = array_pop($codeBlocks);

        $this->blocks =
            array_reduce(
                array_map(function ($codeBlock) {

                    $lines = explode(PHP_EOL, $codeBlock);
                    $id = explode(':', array_shift($lines))[0];
                    $place = substr_count($codeBlock, '#');

                    $variants = array_map(
                        fn($variant) => array_map(
                            fn($line) => bindec(strtr($line, ['O' => 0, '#' => 1])),
                            $variant
                        ),
                        $this->createVariants($lines)
                    );

                    return [$id, $variants, $place];

                }, $codeBlocks),

                function ($carry, $block) {
                    // create an associative array
                    $carry[$block[0]] = ['variants' => $block[1], 'need' => $block[2]];
                    return $carry;
                },
                []
            );

        $this->boards = array_map(
            function ($line) {
                [$dimensions, $amounts] = explode(": ", $line);
                $amounts = array_filter(array_map('intval', explode(" ", $amounts)));
                uksort($amounts, fn($a, $b) => count($this->blocks[$b]) <=> count($this->blocks[$a]));
                return [
                    'dimensions' => array_map('intval', explode("x", $dimensions)),
                    'amounts' => $amounts,
                ];
            },
            explode(PHP_EOL, $boards)
        );
        $this->boards = array_filter($this->boards, function ($board) {
            $needed = 0;
            foreach ($board['amounts'] as $key => $val) {
                $needed += $this->blocks[$key]['need'] * $val;
            }
            return $needed < array_product($board['dimensions']);
        });
        $this->blocks = array_map(fn($block) => $block['variants'], $this->blocks);
    }

    private function calcValidRegions(): int
    {
        $validCount = 0;
        foreach ($this->boards as $board) {
            $this->board = $board;
            $this->cache = [];
            $boardData = array_fill(0, $board['dimensions'][1], 0);
            $this->callCounter = 0;
            if ($this->canBePlaced($boardData, $board['amounts'])) {
                $validCount += 1;
            }
        }
        $this->cache = [];
        return $validCount;
    }

    private function canBePlaced(array $boardData, mixed $amounts): bool
    {
        $amounts = array_filter($amounts);

        $cacheKey = $this->getHashKey($boardData, $amounts);
        if (isset($this->cache[$cacheKey])) {
            $this->cacheHits++;
            return $this->cache[$cacheKey];
        }

        if ($this->callCounter++ > 1_000_000) {
            throw new \Exception('Too much');
        }
        if (count($amounts) == 0) return true;

        $amount = reset($amounts);
        $piece = key($amounts);

        $amounts[$piece]--;

        $block = $this->blocks[$piece];
        foreach ($block as $variantId => $variant) {
            for ($x = 0; $x < $this->board['dimensions'][0] - 2; $x++) {
                for ($y = 0; $y < $this->board['dimensions'][1] - 2; $y++) {
                    if ($this->tryPlacing($variant, $x, $y, $boardData, $amounts)) {
                        $this->cache[$cacheKey] = true;
                        return true;
                    };
                }
            }
        }
        $this->cache[$cacheKey] = false;
        return false;

    }

    private function createVariants(array $lines): array
    {
        $variants = array_fill(0, 8, array_fill(0, 3, array_fill(0, 3, 'O')));
        for ($x = 0; $x < 3; $x++) {
            for ($y = 0; $y < 3; $y++) {
                $char = substr($lines[$y], $x, 1) === '#' ? '#' : 'O';

                $variants[0][$y][$x] = $char;
                $variants[1][2 - $y][$x] = $char;
                $variants[2][$y][2 - $x] = $char;
                $variants[3][2 - $y][2 - $x] = $char;

                $variants[4][$x][$y] = $char;
                $variants[5][2 - $x][$y] = $char;
                $variants[6][2 - $x][2 - $y] = $char;
                $variants[7][$x][2 - $y] = $char;

            }
        }
        $variants = array_map(fn($variant) => array_map(fn($line) => implode("", $line), $variant), $variants);
        $variantHashes = array_map(fn($variant) => implode("", $variant), $variants);
        $variantHashes = array_unique($variantHashes);

        return array_map(
            fn($key) => $variants[$key],
            array_keys($variantHashes)
        );

    }

    private function tryPlacing(mixed $variant, int $x, int $y, array $boardData, mixed $amounts)
    {
        foreach ($variant as $dy => $variantLine) {
            $variantLine <<= $x;
            if ($boardData[$y + $dy] & $variantLine) {
                return false;
            }
            $boardData[$y + $dy] |= $variantLine;
        }
        return $this->canBePlaced($boardData, $amounts);
    }

    private function getHashKey(array $boardData, mixed $amounts)
    {
        // when called we know a definitive answer for this configuration
        // this answer is valid for all permutations of this configuration (mirroring)
        // so we build the hashkeys for all these configurations and return the smallest one
        // effectively answering the question for all similar configurations
        $mirrored = array_map(function ($num) {
            $value = 0;
            for ($i = 0; $i < $this->board['dimensions'][0]; $i++) {
                if ($num & 1) {
                    $value += 1;
                }
                $value <<= 1;
                $num >>= 1;
            }
            return $value;
        }, $boardData);
        $boardKey = min(
            array_map(
                fn($variant) => implode('-', $variant),
                [
                    $mirrored,
                    $boardData,
                    array_reverse($mirrored),
                    array_reverse($boardData)
                ])
        );

        return $boardKey
            . '|'
            . array_sum(
                array_map(
                    fn($amount, $key) => $amount * pow(10, $key),
                    $amounts,
                    array_keys($amounts)
                )
            );
    }


}
