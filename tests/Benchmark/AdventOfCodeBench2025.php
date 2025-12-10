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
     * @Revs(100)
     */
    public function benchDay05(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day05\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 5)));
    }


    /**
     * @Revs(200)
     */
    public function benchDay06(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day06\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 6)));
    }


    /**
     * @Revs(1000)
     */
    public function benchDay07(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day07\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 7)));
    }


    /**
     * @Revs(1)
     */
    public function benchDay08(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day08\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 8)));
    }


    /**
     * @Revs(1)
     */
    public function benchDay09(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day09\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 9)));
    }


    /**
     * @Revs(1000)
     */
    public function benchDay10(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day10\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 10)));
    }

}
