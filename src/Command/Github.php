<?php

namespace App\Command;


use App\Config\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Github extends Command
{
    protected static $defaultName = 'app:github';

    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Lists all the existing users')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command lists all the users registered in the application:
  <info>php %command.full_name%</info>
By default the command only displays the 50 most recent users. Set the number of
results to display with the <comment>--max-results</comment> option:
  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>
In addition to displaying the user list, you can also send this information to
the email address specified in the <comment>--send-to</comment> option:
  <info>php %command.full_name%</info> <comment>--send-to=fabien@symfony.com</comment>
HELP
            )
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addOption('max-results', null, InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
            ->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dump($this->configuration);
        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['ID', 'Full Name', 'Username', 'Email', 'Roles'],
            []
        );


    }
}