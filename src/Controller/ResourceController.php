<?php

/*
 * This file is part of sylius_export_plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mobizel\SyliusExportPlugin\Controller;

use Mobizel\SyliusExportPlugin\Exporter\ExporterInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController as BaseResourceController;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class ResourceController extends BaseResourceController
{
    /** @var ExporterInterface */
    private $exporter;

    public function setExporter(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
    }

    public function bulkExportAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->getParameters()->has('grid')) {
            throw new MissingMandatoryParametersException('parameter grid not found');
        }

        $this->isGrantedOr403($configuration, ResourceActions::BULK_EXPORT);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple(ResourceActions::BULK_EXPORT, $configuration, $resources);

        $fileContent = $this->exporter->export($resources);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$this->getFileName($configuration).'"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($fileContent);

        return $response;
    }

    protected function getFileName(RequestConfiguration $configuration): string
    {
        $metadata = $configuration->getMetadata();

        return sprintf(
            'export_%s_%s_%s.csv',
            $metadata->getApplicationName(),
            $metadata->getName(),
            (new \DateTime())->format('d-m-Y_H-m')
        );
    }
}
