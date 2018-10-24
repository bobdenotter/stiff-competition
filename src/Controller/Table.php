<?php

namespace App\Controller;

use App\Config\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Table extends AbstractController
{
    /** @var Configuration */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config->get();
    }

    /**
     * @Route("/")
     */
    public function table()
    {
        $context = [
            'data' => $this->config
        ];

        return $this->render('index.html.twig', $context);
    }
}