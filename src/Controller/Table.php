<?php

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

        $context = [
            'data' => $this->config->sortBy($order)
        ];

        return $this->render('index.html.twig', $context);
    }
}