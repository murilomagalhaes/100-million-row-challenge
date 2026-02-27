<?php

namespace App;


final class Parser
{
    const string BASE_URL = 'https://stitcher.io';
    private array $urls = [];

    public function parse(string $inputPath, string $outputPath): void
    {
        $urlPathOffset = strlen(self::BASE_URL);

        $inputFile = fopen($inputPath, "r");

        gc_disable();

        while ($line = fgets($inputFile)) {
            $this->processLine($line, $urlPathOffset);
        }

        foreach ($this->urls as &$visits) {
            ksort($visits, SORT_NUMERIC);
            $visits = array_combine(
                array_map(
                    fn(int $dateKey): string => preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1-$2-$3', (string)$dateKey),
                    array_keys($visits)
                ),
                array_values($visits)
            );
        }

        gc_enable();

        fclose($inputFile);

        file_put_contents($outputPath, json_encode($this->urls, JSON_PRETTY_PRINT));

    }

    private function processLine(string $line, int $urlPathOffset): void
    {
        $path = substr($line, $urlPathOffset, -27);
        $date = substr($line, -26, 10);

        $dateKey = (int)str_replace('-', '', $date);

        $visit = &$this->urls[$path][$dateKey];
        ++$visit;
    }

}