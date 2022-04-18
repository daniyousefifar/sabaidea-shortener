<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\DomainRepositoryInterface;
use App\Repositories\LinkRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Src\Actions\ActionError;

class LinkHandlerController extends Controller
{
    protected LinkRepositoryInterface $linkRepository;
    protected DomainRepositoryInterface $domainRepository;

    public function __construct(
        ContainerInterface        $container,
        LoggerInterface           $logger,
        LinkRepositoryInterface   $linkRepository,
        DomainRepositoryInterface $domainRepository
    )
    {
        parent::__construct($container, $logger);
        $this->linkRepository = $linkRepository;
        $this->domainRepository = $domainRepository;
    }

    protected function action(): Response
    {
        $code = $this->resolveArg('code');
        $link = $this->linkRepository->findLinkByCode($code);
        $host = $this->request->getUri()->getHost();

        if ($link['domain_id'] !== null) {
            try {
                $domain = $this->domainRepository->findDomainById((int)$link['domain_id']);

                if ($domain['domain'] !== $host) {
                    return $this->response->withHeader('Location', '/')->withStatus(302);
                }
            } catch (\Exception $e) {
                return $this->respondWithError('Something went wrong. try again.', ActionError::SERVER_ERROR, 500);
            }
        }

        return $this->response->withHeader('Location', $link['url'])->withStatus(301);
    }
}