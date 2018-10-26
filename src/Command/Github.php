<?php

declare(strict_types=1);

namespace App\Command;

use App\Config\Configuration;
use App\Entity\Statistics;
use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Github\Client;
use Github\Exception\ApiLimitExceedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Github extends Command
{
    protected static $defaultName = 'app:github';

    /** @var Configuration */
    private $configuration;

    /** @var Client */
    private $client;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(Configuration $configuration, ObjectManager $objectManager)
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->client = new Client();

        $token = getenv('GITHUB_SECRET');
        if (!isset($token)) {
            dd('Github token is not set.');
        }
        $this->client->authenticate($token, null, Client::AUTH_HTTP_TOKEN);

        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Update stuff from Github, using the API')
            ->setHelp(
                <<<'HELP'
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
        $config = $this->configuration->get();

        $slugify = Slugify::create();

        $results = [];

        try {
            foreach (collect($config)->sortBy('updated') as $key => $item) {
                echo ' . ' . $item['name'];

                if (empty($item['repository'])) {
                    echo " - No repository defined \n";
                    continue;
                }

                if ($item['updated'] === date('Y-m-d')) {
                    echo " - Updated today already \n";
                    continue;
                }

                $reponame = $this->getReponame($item['repository']);

                $info = $this->getInfo($reponame);

                $commits = $this->getCommits($reponame);

		// Sometimes the commits aren't fetched correctly. If so, skip.
		if ( (int) $commits['year'] === 0) {
                    echo " - No commits were fetched \n";
                    continue;
		}

                $row = [
                    'name' => $item['name'],
                    'open_issues' => $info['open_issues_count'],
                    'opened_recently' => $this->getRecentlyOpenedIssues($reponame),
                    'closed_recently' => $this->getRecentlyClosedIssues($reponame),
                    'stargazers' => $info['stargazers_count'],
                    'forks' => $info['forks_count'],
                    'license' => $info['license']['spdx_id'],
                    'commits_year' => $commits['year'],
                    'commits_month' => $commits['month'],
                    'updated' => date('Y-m-d'),
                ];

                $statistics = new Statistics();
                $statistics->setName($slugify->slugify($item['name']))
                    ->setOpenIssues($row['open_issues'])
                    ->setOpenedRecently($row['opened_recently'])
                    ->setClosedRecently($row['closed_recently'])
                    ->setStargazers($row['stargazers'])
                    ->setForks($row['forks'])
                    ->setCommitsYear($row['commits_year'])
                    ->setCommitsMonth($row['commits_month'])
                    ->setTimestamp(Carbon::now());

                $this->objectManager->persist($statistics);

                $config[$key] = array_merge($item, $row);
                $config[$key]['description'] = $info['description'];
                $config[$key]['topics'] = $this->getTopics($reponame);

                $results[] = $row;
            }
        } catch (ApiLimitExceedException $exception) {
            echo "\nGithub API Limit reached!!\n";
        }

        echo "\n";

        $this->objectManager->flush();

        if (count($results) > 0) {
            $header = array_keys($results[0]);

            $io = new SymfonyStyle($input, $output);
            $io->table(
                $header,
                $results
            );
        }

        $this->configuration->set($config);
        $this->configuration->write();
    }

    private function getReponame($url)
    {
        $url = parse_url($url);

        return ltrim($url['path'], '/');
    }

    private function getRecentlyOpenedIssues($reponame)
    {
        $query = sprintf(
            'repo:%s is:open created:>%s',
            $reponame,
            date('Y-m-d', strtotime('-30 days'))
        );
        $issues = $this->client->api('search')->issues($query);

        return $issues['total_count'];
    }

    private function getRecentlyClosedIssues($reponame)
    {
        $query = sprintf(
            'repo:%s is:closed closed:>%s',
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

    private function getTopics($reponame)
    {
        $reponame = explode('/', $reponame);

        $topics = $this->client->api('repo')->topics($reponame[0], $reponame[1]);

        return $topics['names'];
    }

    private function getCommits($reponame)
    {
        $reponame = explode('/', $reponame);

        $commits = collect($this->client->api('repo')->activity($reponame[0], $reponame[1]));

        $res = [
            'year' => $commits->sum('total'),
            'month' => $commits->slice(-4)->sum('total'),
        ];

        return $res;
    }
}
