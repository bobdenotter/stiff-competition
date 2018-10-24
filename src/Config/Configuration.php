<?php

namespace App\Config;

use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private $data = [];
    
    public function __construct()
    {
        $this->initialize();
    }

    private function initialize()
    {
        $this->data = Yaml::parseFile(dirname(dirname(__DIR__)) . '/data/table.yml');
    }

    public function get()
    {
        return $this->data;
    }
}