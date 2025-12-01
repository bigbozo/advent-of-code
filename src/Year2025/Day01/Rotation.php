<?php

namespace Bizbozo\AdventOfCode\Year2025\Day01;

class Rotation
{
    public function __construct(
        public readonly Directions $direction,
        public readonly int        $amount,
    )
    {
    }

    public function zeroClicks($start): int
    {
        $clicks = 0;

        if ($this->amount > 100) {
            $clicks = (int)($this->amount / 100);
            $rest = $this->amount - $clicks * 100;
        } else {
            $rest = $this->amount;
        }
        $clicks += ($start != 0) * match ($this->direction) {
            Directions::LEFT => $rest > $start ? 1 : 0,
            Directions::RIGHT => $start + $rest > 100 ? 1: 0,
        };

        return $clicks;
    }
}