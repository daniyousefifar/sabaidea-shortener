<?php

namespace App\Controllers\Api\v1\Domain;

use App\Controllers\Controller;
use App\Repositories\DomainRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class DomainController extends Controller
{
    protected DomainRepositoryInterface $domainRepository;

    public function __construct(ContainerInterface $container,LoggerInterface $logger, DomainRepositoryInterface $domainRepository)
    {
        parent::__construct($container, $logger);
        $this->domainRepository = $domainRepository;
    }
}