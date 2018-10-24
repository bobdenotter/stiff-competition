<?php

declare(strict_types=1);

namespace App\Command;

use App\Config\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Panther\Client;

class Screenshot extends Command
{
    protected static $defaultName = 'app:screenshot';

    /** @var Configuration */
    private $configuration;

    /** @var string */
    private $basepath;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->basepath = dirname(dirname(__DIR__)) . '/screenshots/';
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Gather some screenshots')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command makes a bunch of screenshots
HELP
            )
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            // ->addOption('max-results', null, InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
            // ->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->configuration->get();

        $client = Client::createChromeClient();

        foreach (collect($config)->sortBy('updated') as $key => $item) {
            echo $item['name'] . ' - ';

            foreach (['site', 'docs'] as $type) {
                $crawler = $client->request('GET', $item[$type]);
                $filename = sprintf('%s%s_%s.png', $this->basepath, $item['name'], $type);
                $client->takeScreenshot($filename);
                echo '.';
            }
            echo "\n";
        }
    }
}
