<?php

namespace Bizbozo\AdventOfCode\Tests\Benchmark;

use Bizbozo\AdventOfCode\Traits\UsesInput;

class AdventOfCodeBench2025
{
    use UsesInput;







    /**
     * @Revs(100)
     */
    public function benchDay01(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day01\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 1)));
    }


    /**
     * @Revs(1000)
     */
    public function benchDay02(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day02\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 2)));
    }


    /**
     * @Revs(100)
     */
    public function benchDay03(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day03\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 3)));
    }


    /**
     * @Revs(3)
     */
    public function benchDay04(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day04\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 4)));
    }


    /**
     * @Revs(1000)
     */
    public function benchDay05(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day05\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 5)));
    }

}
