<?php

declare(strict_types=1);

namespace Bizbozo\AdventOfCode\Year2025\Day04;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{

    /**
     * @var array|array[]
     */
    private array $data;
    private int $width;
    private int $height;

    private function parseData(string $stream): array
    {
        return array_map('str_split', explode(PHP_EOL, $stream));
    }

    public function getTitle(): string
    {
        return "Day 4 - Printing Department";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $data = static::parseData($inputStream);

        $this->data = $data;
        $this->width = count($this->data);
        $this->height = count($this->data[0]);
        $accessible = $this->getAccessibleRoles();

        $accessibleRoles = $accessible;

        $totalAccessibleRoles = 0;
        while ($accessible) {
            $totalAccessibleRoles += $accessible;
            $accessible = $this->getAccessibleRoles();
        }

        $this->paint();

        return new SolutionResult(
            4,
            new UnitResult("There are %s paper-rolls accessible in the first round", [$accessibleRoles]),
            new UnitResult('%s paper-rolls can be removed in total', [$totalAccessibleRoles])
        );
    }

    private function getAccessibleRoles(): int
    {
        $accessibleRemoved = $this->data;
        $accessible = 0;
        foreach ($this->data as $x => $row) {
            foreach ($row as $y => $char) {
                if ($char === '@') {
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

    public function paint(): void
    {
        $img = imagecreate($this->width, $this->height);
        $bg = imagecolorallocate($img, 0, 0, 0);
        $green = imagecolorallocate($img, 0, 255, 0);
        foreach ($this->data as $x => $row) {
            foreach ($row as $y => $char) {
                if ($this->get($x, $y) === '@') {
                    imagesetpixel($img, $x, $y, $green);
                }
            }
        }
        if (!is_dir('out')) mkdir('out');
        imagepng($img, "docs/2025-04-paperrolls.png");
    }

}
