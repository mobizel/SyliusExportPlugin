<?php

/*
 * This file is part of Mobizel Sylius export plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mobizel\SyliusExportPlugin\Controller;

use Mobizel\SyliusExportPlugin\Exporter\ResourceExporterInterface;
use Mobizel\SyliusExportPlugin\Exporter\ResourceExporterRegistry;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Kévin Régnier <kevin@mobizel.com>
 */
class BulkExportAction
{
    protected MetadataInterface $metadata;

    protected RequestConfigurationFactoryInterface $requestConfigurationFactory;

    protected RepositoryInterface $repository;

    protected ResourcesCollectionProviderInterface $resourcesCollectionProvider;

    protected EventDispatcherInterface $eventDispatcher;

    protected AuthorizationCheckerInterface $authorizationChecker;

    private ResourceExporterRegistry $exporterRegistry;

    public function __construct(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        ResourceExporterRegistry $exporterRegistry
    ) {
        $this->metadata = $metadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->repository = $repository;
        $this->resourcesCollectionProvider = $resourcesCollectionProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationChecker = $authorizationChecker;
        $this->exporterRegistry = $exporterRegistry;
    }

    public function __invoke(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->getParameters()->has('grid')) {
            throw new MissingMandatoryParametersException('parameter grid not found');
        }

        $this->isGrantedOr403($configuration, ResourceActions::BULK_EXPORT);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(ResourceActions::BULK_EXPORT, $configuration, $resources);

        $exporter = $this->getExporter($this->getExportFormat($configuration));
        $fileContent = $exporter->export($resources);
        $fileName = $this->getFileName($configuration);

        $response = new Response();
        $response->headers->set('Content-Type', $exporter->getContentType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($fileContent);

        return $response;
    }

    protected function getExporter(?string $format): ResourceExporterInterface
    {
        return $this->exporterRegistry->getExporter($format);
    }

    protected function getExportFormat(RequestConfiguration $configuration): ?string
    {
        $vars = $configuration->getParameters()->get('vars');
        return $vars['export_format'] ?? null;
    }

    protected function getFileName(RequestConfiguration $configuration): string
    {
        $metadata = $configuration->getMetadata();

        return sprintf(
            'export_%s_%s_%s.csv',
            $metadata->getApplicationName(),
            $metadata->getPluralName(),
            (new \DateTime())->format('d-m-Y_H-i')
        );
    }

    /**
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403(RequestConfiguration $configuration, string $permission): void
    {
        if (!$configuration->hasPermission()) {
            return;
        }

        $permission = $configuration->getPermission($permission);

        if (!$this->authorizationChecker->isGranted($configuration, $permission)) {
            throw new AccessDeniedException();
        }
    }
}
