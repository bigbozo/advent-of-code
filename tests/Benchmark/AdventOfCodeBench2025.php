<?php

namespace Bizbozo\AdventOfCode\Tests\Benchmark;

use Bizbozo\AdventOfCode\Traits\UsesInput;

class AdventOfCodeBench2025
{
    use UsesInput;







    /**
     * @Revs(1000)
     */
    public function benchDay01(): void
    {
        (new \Bizbozo\AdventOfCode\Year2025\Day01\Solution)
            ->solve(file_get_contents($this->getInputFilename(2025, 1)));
    }

}
