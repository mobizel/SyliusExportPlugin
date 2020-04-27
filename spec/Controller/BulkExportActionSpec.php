<?php

namespace spec\Mobizel\SyliusExportPlugin\Controller;

use Mobizel\SyliusExportPlugin\Controller\BulkExportAction;
use Mobizel\SyliusExportPlugin\Controller\ResourceActions;
use Mobizel\SyliusExportPlugin\Exporter\ResourceExporterInterface;
use Mobizel\SyliusExportPlugin\Exporter\ResourceExporterRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class BulkExportActionSpec extends ObjectBehavior
{
    function let(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        ResourceExporterRegistry $exporterRegistry
    ) {
        $this->beConstructedWith(
            $metadata,
            $requestConfigurationFactory,
            $repository,
            $resourcesCollectionProvider,
            $eventDispatcher,
            $authorizationChecker,
            $exporterRegistry
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BulkExportAction::class);
    }

    function it_can_export_resources
    (
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        ResourceExporterRegistry $exporterRegistry,
        RequestConfiguration $configuration,
        GridViewInterface $gridView,
        Request $request,
        Parameters $parameters,
        ResourceControllerEvent $event,
        ResourceExporterInterface $exporter
    ) {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->getParameters()->willReturn($parameters);
        $configuration->getMetadata()->willReturn($metadata);
        $configuration->hasPermission()->willReturn(false);
        $parameters->has('grid')->willReturn(true);
        $parameters->get('vars')->willReturn(['export_format' => 'csv']);

        $resourcesCollectionProvider->get($configuration, $repository)->willReturn($gridView);
        $eventDispatcher->dispatchMultiple(ResourceActions::BULK_EXPORT, $configuration, $gridView)->willReturn($event);

        $exporterRegistry->getExporter('csv')->willReturn($exporter);
        $exporter->getFormat()->willReturn('csv');
        $exporter->getContentType()->willReturn('text/csv');

        $metadata->getApplicationName()->willReturn('app');
        $metadata->getPluralName()->willReturn('customers');

        $requestConfigurationFactory->create($metadata, $request)->shouldBeCalled();
        $configuration->getParameters()->shouldBeCalled();
        $resourcesCollectionProvider->get($configuration, $repository)->shouldBeCalled();
        $eventDispatcher->dispatchMultiple(ResourceActions::BULK_EXPORT, $configuration, $gridView)->shouldBeCalled();
        $exporter->export($gridView)->shouldBeCalled();

        $this->__invoke($request);
    }

    function it_cannot_export_resources_without_grid_definition
    (
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        ResourceExporterRegistry $exporterRegistry,
        RequestConfiguration $configuration,
        GridViewInterface $gridView,
        Request $request,
        Parameters $parameters,
        ResourceControllerEvent $event,
        ResourceExporterInterface $exporter
    ) {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->getParameters()->willReturn($parameters);
        $parameters->has('grid')->willReturn(false);

        $requestConfigurationFactory->create($metadata, $request)->shouldBeCalled();
        $configuration->getParameters()->shouldBeCalled();
        $resourcesCollectionProvider->get($configuration, $repository)->shouldNotBeCalled();
        $eventDispatcher->dispatchMultiple(ResourceActions::BULK_EXPORT, $configuration, $gridView)->shouldNotBeCalled();
        $exporter->export($gridView)->shouldNotBeCalled();

        $this->shouldThrow(MissingMandatoryParametersException::class)->during('__invoke', [$request]);
    }
}
