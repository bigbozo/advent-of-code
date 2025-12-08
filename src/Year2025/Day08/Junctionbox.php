<?php

namespace Bizbozo\AdventOfCode\Year2025\Day08;

class Junctionbox
{

    public int $circuit;

    public function __construct(
        public int $id,
        public int $x,
        public int $y,
        public int $z)
    {
        $this->circuit = $id;
    }

    public function distanceTo(Junctionbox $other): int
    {
        return pow($this->x - $other->x, 2) + pow($this->y - $other->y, 2) + pow($this->z - $other->z, 2);
    }


}