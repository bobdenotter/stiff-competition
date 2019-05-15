<?php

declare(strict_types=1);

namespace App\Controller;

use App\Config\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class Details extends AbstractController
{
    /** @var Collection */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = collect($config->get());
    }

    /**
     * @Route("/{slug}")
     */
    public function details(Request $request, string $slug)
    {

        $context = [
            'data' => $this->config->where('slug', $slug)->first(),
        ];

        return $this->render('details.html.twig', $context);
    }
}
