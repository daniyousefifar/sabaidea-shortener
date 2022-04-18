<?php

namespace App\Controllers\Api\v1\Link;

use App\Controllers\Controller;
use App\Repositories\LinkRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class LinkController extends Controller
{
    protected LinkRepositoryInterface $linkRepository;

    public function __construct(ContainerInterface $container,LoggerInterface $logger, LinkRepositoryInterface $linkRepository)
    {
        parent::__construct($container, $logger);
        $this->linkRepository = $linkRepository;
    }
}