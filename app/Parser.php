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

        while ($line = fgets($inputFile)) {
            $this->processLine($line, $urlPathOffset);
        }

        foreach ($this->urls as &$path) {
            ksort($path, SORT_STRING);
        }

        fclose($inputFile);

        $this->writeJson($outputPath);
    }

    private function processLine(string $line, int $urlPathOffset): void
    {
        $delimiterPos = strpos($line, ',');

        $path = substr($line, $urlPathOffset, $delimiterPos - $urlPathOffset);
        $date = substr($line, $delimiterPos + 1, 10);

        if (isset($this->urls[$path][$date])) {
            $this->urls[$path][$date]++;
            return;
        }

        $this->urls[$path][$date] = 1;
    }

    private function writeJson(string $outputPath): void
    {
        $outputFile = fopen($outputPath, 'w');

        fwrite($outputFile, json_encode($this->urls, JSON_PRETTY_PRINT));

        fclose($outputFile);
    }


}