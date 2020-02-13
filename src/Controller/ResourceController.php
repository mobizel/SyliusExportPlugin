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

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController as BaseResourceController;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends BaseResourceController
{
    public function bulkExportAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::BULK_EXPORT);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $this->eventDispatcher->dispatchMultiple('bulk_export', $configuration, $resources);

        $fileName = sprintf('%s.%s.csv', 'export_commandes', (new \DateTime())->format('d-m-Y_H-m'));

        /** @var Pagerfanta $paginator */
        if ($resources instanceof ResourceGridView) {
            $paginator = $resources->getData();
        } else {
            $paginator = $resources;
        }

        if ($paginator->getNbPages() > 1) {
            $nbResult = $paginator->count();
            $paginator->setMaxPerPage($nbResult);
        }

        // $fileContent = $this->csvOrderExporter->export($paginator->getCurrentPageResults(), $request->getLocale());

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($fileContent);

        return $response;
    }
}
