<?php

namespace Bizbozo\AdventOfCode\Commands;

use Bizbozo\AdventOfCode\Traits\UsesInput;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSolution extends Command
{
    use UsesInput;

    private int $day;
    private int $year;

    protected function configure()
    {
        $this->setDescription('Create a stub for day x of AdventOfCode')
            ->setName('generate')
            ->addArgument('day', InputArgument::REQUIRED, 'Day')
            ->addArgument('year', InputArgument::OPTIONAL, 'Year', $_ENV['YEAR'] ?? null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int

    {

        $this->day = (int)$input->getArgument('day');
        $this->year = (int)$input->getArgument('year');


        if ($this->day < 1 || $this->day > 25) {
            $output->writeln(['Day out of range']);
            return Command::FAILURE;
        }

        if (date('d') < $this->day && date('Y') <= $this->year) {
            $output->writeln(['Input-File not ready. You are too early',]);
            return Command::FAILURE;
        }

        $this->generateSolution();
        $this->addBenchmark();


        $inputFilename = $this->getInputFilename($this->year, $this->day);
        $testInputFilenames = $this->getTestInputFilenames($this->year, $this->day);
        if (file_exists($inputFilename)) {
            $output->writeln(['Input-File exists. Aborting',]);
            return Command::FAILURE;
        } else {
            try {
                $data = $this->fetchInputData();
                if ($data) {
                    $dir = dirname($inputFilename);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    file_put_contents($inputFilename, $data);
                }
                touch($testInputFilenames[0]);
            } catch (GuzzleException $e) {
                $output->writeln([$e->getMessage()]);
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;

    }


    /**
     * @throws GuzzleException
     */
    private function fetchInputData(): string
    {
        $client = new Client();

        if (!$_ENV['APIKEY']) {
            return false;
        }

        $jar = CookieJar::fromArray(
            [
                'session' => $_ENV['APIKEY'],
            ],
            'adventofcode.com'
        );

        $res = $client->request('GET', sprintf("https://adventofcode.com/%s/day/%d/input", $this->year, $this->day), [
            'cookies' => $jar
        ]);
        return $res->getBody()->getContents();
    }

    private function getSolutionFilename()
    {
        return sprintf("%s/../Year%s/Day%s/Solution.php", __DIR__, $this->year, $this->leadingZero($this->day));
    }

    private function generateSolution()
    {
        $code = $this->parseTemplate('Solution.template');

        $filename = $this->getSolutionFilename();
        if (!file_exists($filename)) {
            mkdir(dirname($filename), recursive: true);
            file_put_contents($filename, $code);
        }
    }

    private function addBenchmark()
    {
        // check if benchclass for year exists

        $class = "\Bigbozo\AdventOfCode\Tests\Benchmark\AdventOfCodeBench" . $this->year;
        $filename = __DIR__ . '/../../tests/Benchmark/AdventOfCodeBench' . $this->year . '.php';

        if (!file_exists($filename)) {
            $code = $this->parseTemplate('benchmarkClass.template');
            file_put_contents($filename, $code);
        }
        $code = $this->parseTemplate('benchmark.template');
        $data = file_get_contents($filename);
        if (strpos($data, 'benchDay' . $this->leadingZero($this->day)) !== false) return;

        $insertPosition = strrpos($data, '}');

        $data = substr($data, 0, $insertPosition);
        $data .= PHP_EOL . $code . PHP_EOL . '}' . PHP_EOL;
        file_put_contents($filename, $data);
    }

    /**
     * @return string
     */
    private function parseTemplate($template): string
    {
        $template = file_get_contents(__DIR__ . '/../../templates/' . $template);
        return strtr(
            $template,
            [
                '###day###' => $this->day,
                '###DAY###' => $this->leadingZero($this->day),
                '###YEAR###' => $this->year,
            ]
        );
    }


}
