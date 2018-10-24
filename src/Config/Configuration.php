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

    private function initialize()
    {
        $this->data = Yaml::parseFile($this->filename);
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function set(array $data)
    {
        $this->data = $data;
    }

    /**
     * Write Yaml data.
     */
    public function write()
    {
        $yaml = Yaml::dump($this->data);

        file_put_contents($this->filename, $yaml);
    }
}
