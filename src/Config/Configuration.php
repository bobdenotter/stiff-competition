<?php

declare(strict_types=1);

namespace App\Config;

use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private $data = [];
    private $filename;

    public function __construct()
    {
        $this->filename = dirname(dirname(__DIR__)) . '/data/table.yml';
        $this->initialize();
    }

    private function initialize(): void
    {
        $this->data = Yaml::parseFile($this->filename);
    }

    public function get(): array
    {
        return $this->data;
    }

    public function set(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Write Yaml data.
     */
    public function write(): void
    {
        $yaml = Yaml::dump($this->data);

        file_put_contents($this->filename, $yaml);
    }
}
