<?php

namespace Bizbozo\AdventOfCode\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BenchmarkCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Run the benchmarks for the whole codebase')
            ->setName('benchmark');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project_dir = __DIR__ . '/../..';
        $csvFilePath = "$project_dir/docs/benchmark.csv";

        exec("php $project_dir/vendor/bin/phpbench run tests/Benchmark --report=default --output=csv > $csvFilePath");

        $fh = fopen($csvFilePath, "r");
        $rows = [];
        $widths = [];
        /** @noinspection PhpDeprecatedPassingNonEmptyEscapeToCsvFunctionInspection */
        while ($row = fgetcsv($fh, 4096, escape: '\\')) {
            if (!count($widths)) $widths = array_fill(0, count($row), 0);
            foreach ($row as $col => &$item) {
                if (is_numeric($item)) $item = number_format(round($item, 2), 2);
                $item = " $item ";
                $widths[$col] = max($widths[$col], strlen($item));
            }
            $rows[] = $row;
        };
        fclose($fh);
        @unlink($csvFilePath);
        $rows = array_map(function ($row) use ($widths) {
            foreach ($row as $col => $item) {
                $stream[] = str_pad($item, length: $widths[$col], pad_type: STR_PAD_LEFT, pad_string: " ");
            }
            return implode("|", $stream);
        }, $rows);
        $stream = "    # Benchmark Results" . PHP_EOL . PHP_EOL . implode(PHP_EOL, $rows);

        file_put_contents("$project_dir/docs/benchmark." . $_ENV['MACHINE'] . ".md", str_replace(PHP_EOL, PHP_EOL . '    ', $stream));


        return Command::SUCCESS;
    }
}
