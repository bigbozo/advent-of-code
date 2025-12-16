<?php

namespace Bizbozo\AdventOfCode\Year2025\Day08;

use Bizbozo\AdventOfCode\Solutions\AbstractSolution;
use Bizbozo\AdventOfCode\Solutions\SolutionResult;
use Bizbozo\AdventOfCode\Solutions\UnitResult;
use Override;

class Solution extends AbstractSolution
{

    public function getTitle(): string
    {
        return "Day 8 - Playground";
    }

    #[Override]
    public function solve(string $inputStream, ?string $inputStream2 = null, ?bool $isTest = true): SolutionResult
    {

        $points = static::parseData($inputStream);

        $connections = $this->calculateConnections($points);
        return new SolutionResult(
            8,
            new UnitResult("The 1st answer is %s", [$this->threeCircuitsProduct($points, $connections)]),
            new UnitResult('The 2nd answer is %s', [$this->wallDistance($points, $connections)])
        );
    }

    /**
     * @param string $stream
     * @return array<Junctionbox>
     */
    private function parseData(string $stream): array
    {
        $coordinates = array_map(fn($item) => array_map('intval', explode(',', $item)), explode(PHP_EOL, trim($stream)));

        $result = [];
        foreach ($coordinates as $id => $coordinate) {
            $result[] = new Junctionbox($id, $coordinate[0], $coordinate[1], $coordinate[2]);
        }

        return $result;

    }

    /**
     * @param array $points
     * @return array
     */
    public function calculateConnections(array $points): array
    {
        for ($i = 0; $i < count($points) - 1; $i++) {
            for ($j = $i + 1; $j < count($points); $j++) {
                $distances[] = [
                    'from' => (int)$points[$i]->id,
                    'to' => (int)$points[$j]->id,
                    'distance' => $points[$i]->distanceTo($points[$j])
                ];
            }
        }
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        return $distances;
    }

    /**
     * @param array $points
     * @param array $connections
     * @return array
     */
    public function threeCircuitsProduct(array $points, array $connections): int
    {
        $junctionBoxesToProcess = count($points) === 20 ? 10 : 1000;
        foreach ($connections as $connection) {
            $points = $this->connectBoxes($points, $connection);
            $junctionBoxesToProcess--;
            if (!$junctionBoxesToProcess) {
                break;
            }
        }

        $circuits = array_count_values(array_map(fn($p) => $p->circuit, $points));
        rsort($circuits);

        return array_product(array_slice($circuits, 0, 3));
    }

    /**
     * @param array $points
     * @param mixed $connection
     * @return array
     */
    public function connectBoxes(array $points, mixed $connection): array
    {
        $from = $points[$connection['from']];
        $to = $points[$connection['to']];

        if ($from->circuit != $to->circuit) {
            $toCircuit = $to->circuit;
            $fromCircuit = $from->circuit;
            $points = array_map(function ($item) use ($fromCircuit, $toCircuit) {
                if ($item->circuit === $fromCircuit) $item->circuit = $toCircuit;
                return $item;
            }, $points);
        }
        return $points;
    }

    public function wallDistance(array $points, array $connections): int
    {
        $connection = reset($connections);
        foreach ($connections as $connection) {
            $points = $this->connectBoxes($points, $connection);
            $circuits = array_count_values(array_map(fn($p) => $p->circuit, $points));
            if (count($circuits) === 1) break;
        }

        return $points[$connection['from']]->x * $points[$connection['to']]->x;

    }
}
