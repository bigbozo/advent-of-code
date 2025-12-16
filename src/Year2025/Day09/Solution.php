<?php

namespace Bizbozo\AdventOfCode\Year2025\Day09;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{

    private array $outerPoints;

    public function getTitle(): string
    {
        return "Day 9 - Movie Theater";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $points = static::parseData(rtrim($inputStream));

        $areas = $this->calculateAreas($points);

        usort($areas, fn($a, $b) => $a['area'] <=> $b['area']);

        $area = array_pop($areas);

        $largestArea = $area;
        $this->outerPoints = $this->createOuterPoints($points);

        while (!$this->isInPolygon($largestArea, $points) && count($areas)) {
            $largestArea = array_pop($areas);
        }

        return new SolutionResult(
            9,
            new UnitResult("The 1st answer is %s", [$area['area']]),
            new UnitResult('The 2nd answer is %s', [$largestArea['area']])
        );
    }


    private function parseData(string $stream): array
    {

        return array_map(
            function ($line) {
                $nums = explode(",", $line);
                return ['x' => $nums[0], 'y' => $nums[1]];
            },
            explode(PHP_EOL, $stream)
        );
    }

    private function calculateAreas(array $points): array
    {
        $areas = [];
        for ($i = 0; $i < count($points) - 1; $i++) {
            $p1 = $points[$i];
            for ($j = $i + 1; $j < count($points); $j++) {
                $p2 = $points[$j];
                $areas[] = [
                    'p1' => $i,
                    'p2' => $j,
                    'area' => (abs($p1['x'] - $p2['x']) + 1) * (abs($p1['y'] - $p2['y']) + 1)
                ];
            }
        }
        return $areas;
    }

    private function isInPolygon(mixed $area, array $points): bool
    {
        $p1 = $points[$area['p1']];
        $p2 = $points[$area['p2']];

        for ($i = 0; $i < count($points); $i++) {
            if ($this->boxIntersects($p1, $p2, $this->outerPoints[$i], $this->outerPoints[($i + 1) % count($points)])) {
                return false;
            }
        }
        return true;
    }

    private function boxIntersects(mixed $corner1, mixed $corner2, mixed $polyFrom, mixed $polyTo): bool
    {
        $min = [
            'x' => min($corner1['x'], $corner2['x']),
            'y' => min($corner1['y'], $corner2['y'])
        ];
        $max = [
            'x' => max($corner1['x'], $corner2['x']),
            'y' => max($corner1['y'], $corner2['y'])
        ];

        // inset square to remove equality handling
        $points = [
            ['x' => $min['x'] + .5, 'y' => $min['y'] + .5],
            ['x' => $max['x'] - .5, 'y' => $min['y'] + .5],
            ['x' => $max['x'] - .5, 'y' => $max['y'] - .5],
            ['x' => $min['x'] + .5, 'y' => $max['y'] - .5],
        ];


        for ($i = 0; $i < 4; $i++) {
            $from = $points[$i];
            $to = $points[($i + 1) % count($points)];
            if ($this->intersects($from, $to, $polyFrom, $polyTo)) {
                return true;
            }
        }
        return false;

    }

    function intersects($p1, $p2, $p3, $p4)
    {
        // parallel
        if ($p1['x'] == $p2['x'] && $p3['x'] == $p4['x']) return false;
        if ($p1['y'] == $p2['y'] && $p3['y'] == $p4['y']) return false;

        // orthogonal
        $aMinX = min($p1['x'], $p2['x']);
        $aMinY = min($p1['y'], $p2['y']);
        $aMaxX = max($p1['x'], $p2['x']);
        $aMaxY = max($p1['y'], $p2['y']);

        $bMinX = min($p3['x'], $p4['x']);
        $bMinY = min($p3['y'], $p4['y']);
        $bMaxX = max($p3['x'], $p4['x']);
        $bMaxY = max($p3['y'], $p4['y']);

        if ($aMinX == $aMaxX) {
            // a = vertical, b = horizontal
            return $aMinY < $bMinY && $aMaxY > $bMinY && $bMinX < $aMinX && $bMaxX > $aMaxX;
        }

        // a = horizontal, b = vertikal
        return $aMinX < $bMinX && $bMinX < $aMaxX && $bMinY < $aMinY && $bMaxY > $aMaxY;
    }

    private function createOuterPoints(array $points): array
    {
        for ($i = 0; $i < count($points)-1; $i++) {
            $polyFrom = &$points[$i];
            $polyTo = &$points[($i + 1) % count($points)];

            if ($polyFrom['x'] === $polyTo['x']) {
                // vertical
                if ($polyFrom['y'] < $polyTo['y']) {
                    // moving down; border is right, include rightmost tile
                    $polyFrom['x']++;
                    $polyTo['x']++;
                } else {
                    if ($polyFrom['x'] > $polyTo['x']) {
                        // moving left; border is bottom, include bottommost tile
                        $polyFrom['y']++;
                        $polyTo['y']++;
                    }
                }

            }
        }
        return $points;
    }
}