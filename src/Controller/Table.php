<?php

declare(strict_types=1);

namespace App\Controller;

use App\Config\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class Table extends AbstractController
{
    /** @var Collection */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = collect($config->get());
    }

    /**
     * @Route("/")
     */
    public function table(Request $request)
    {
        $order = $request->get('order', 'name');

        if (in_array($order, ['forks', 'stargazers', 'updated', 'open_issues', 'opened_recently', 'closed_recently', 'commits_year', 'commits_month'], true)) {
            $data = $this->config->sortByDesc($order);
        } else {
            $data = $this->config->sortBy($order);
        }

        $context = [
            'data' => $data,
        ];

        return $this->render('index.html.twig', $context);
    }
}
