<?php

namespace App\Command;


use App\Config\Configuration;
use Cocur\Slugify\Slugify;
use Github\Client;
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

    /** @var Client */
    private $client;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration->get();
        $this->client = new Client();

        $token = getenv('GITHUB_SECRET');
        if (!isset($token)) {
            dd("Github token is not set.");
        }
        $this->client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Update stuff from Github, using the API')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> updates stuff from Github, using the API
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
        dump($this->configuration);

        $slugify = Slugify::create();

        $results = [];
        foreach ($this->configuration as $item) {

            $reponame = $this->getReponame($item['repository']);

            echo ".";

            $info = $this->getInfo($reponame);

            $commits = $this->getCommits($reponame);

            $row = [
                'name' => $item['name'],
                'slug' => $slugify->slugify($item['name']),
                'description' => $info['description'],
                'open_issues' => $info['open_issues_count'],
                'opened_recently' => $this->getRecentlyOpenedIssues($reponame),
                'closed_recently' => $this->getRecentlyClosedIssues($reponame),
                'stargazers' => $info['stargazers_count'],
                'forks' => $info['forks_count'],
                'license' => $info['license']['spdx_id'],
                'commits_year' => $commits['year'],
                'commits_month' => $commits['month'],
            ];


            $results[] = $row;
        }

        echo "\n";

        dump($results);

        $header = array_keys($results[0]);

        $io = new SymfonyStyle($input, $output);
        $io->table(
            $header,
            $results
        );
    }

    private function getReponame($url)
    {
        $url = parse_url($url);
        return ltrim($url['path'], '/');
    }

//    private function getTotalOpenIssues($reponame)
//    {
//        $query = sprintf('repo:%s is:open', $reponame);
//        $issues = $this->client->api('search')->issues($query);
//
//        return $issues['total_count'];
//    }


    private function getRecentlyOpenedIssues($reponame)
    {
        $query = sprintf('repo:%s is:open created:>%s',
            $reponame,
            date('Y-m-d', strtotime('-30 days'))
        );
        $issues = $this->client->api('search')->issues($query);

        return $issues['total_count'];
    }

    private function getRecentlyClosedIssues($reponame)
    {
        $query = sprintf('repo:%s is:closed closed:>%s',
            $reponame,
            date('Y-m-d', strtotime('-30 days'))
        );
        $issues = $this->client->api('search')->issues($query);

        return $issues['total_count'];
    }

    private function getInfo($reponame)
    {
        $reponame = explode('/', $reponame);

        return $this->client->api('repo')->show($reponame[0], $reponame[1]);
    }

    private function getCommits($reponame)
    {
        $reponame = explode('/', $reponame);

        $commits = collect($this->client->api('repo')->activity($reponame[0], $reponame[1]));

        $res = [
            'year' => $commits->sum('total'),
            'month' => $commits->slice(-4)->sum('total')
        ];

        return $res;

    }

}