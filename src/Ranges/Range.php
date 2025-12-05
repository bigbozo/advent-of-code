<?php

namespace Bizbozo\AdventOfCode\Ranges;


class Range
{

    public int $start;
    public int $end;

    public function __construct(int $start, int $end)
    {

        if ($start > $end) {
            throw new InvalidArgumentException('start has to be lowwr then end');
        }

        $this->start = $start;
        $this->end = $end;
    }

    public function contains(int $int)
    {
        return $int >= $this->start && $int <= $this->end;
    }

    /**
     * generated with Jetbrains AI with Prompt
     *
     *      calculate the intersection between $range and $this
     *
     *
     * @param Range $range
     * @return Range|null
     */

    public function intersect(Range $range): ?Range
    {
        $start = max($this->start, $range->start);
        $end = min($this->end, $range->end);

        if ($start <= $end) {
            return new Range($start, $end);
        }

        return null;
    }

    /**
     *  generated with Jetbrains AI with Prompt
     *
     *     calculate the difference between $range and $this
     * @param Range $range
     * @return array|null
     */
    public function difference(Range $range): ?array
    {
        if ($this->start > $range->end) return [$this];
        if ($this->end < $range->start) return [$this];

        $start = max($this->start, $range->start);
        $end = min($this->end, $range->end);

        if ($start > $this->start) {
            $diff[] = new Range($this->start, $start - 1);
        }

        if ($this->end > $end) {
            $diff[] = new Range($end + 1, $this->end);
        }

        return $diff ?? null;
    }

    public function size()
    {
        return $this->end - $this->start + 1;
    }

    public function shift(mixed $offset)
    {
        return new self(
            $this->start + $offset,
            $this->end + $offset
        );
    }


}
