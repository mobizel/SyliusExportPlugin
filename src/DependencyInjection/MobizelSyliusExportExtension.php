<?php

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin\DependencyInjection;

use Mobizel\SyliusExportPlugin\Controller\BulkExportAction;
use Mobizel\SyliusExportPlugin\Exporter\ExporterInterface;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class MobizelSyliusExportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $this->loadResources($container);
    }

    private function loadResources(ContainerBuilder $container): void
    {
        $resources = $container->hasParameter('sylius.resources') ? $container->getParameter('sylius.resources') : [];
        $loadedResources = $container->getParameter('sylius.resources');

        foreach ($loadedResources as $alias => $resourceConfig) {
            $metadata = Metadata::fromAliasAndConfiguration($alias, $resourceConfig);

            $resources[$alias] = $resourceConfig;
            $container->setParameter('sylius.resources', $resources);

            $this->addBulkExportController($container, $metadata);
        }
    }

    protected function addBulkExportController(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        $definition = new Definition(BulkExportAction::class);
        $definition
            ->setPublic(true)
            ->setArguments([
                $this->getMetadataDefinition($metadata),
                new Reference('sylius.resource_controller.request_configuration_factory'),
                new Reference($metadata->getServiceId('repository')),
                new Reference('sylius.resource_controller.resources_collection_provider'),
                new Reference('sylius.resource_controller.event_dispatcher'),
                new Reference('sylius.resource_controller.authorization_checker'),
                new Reference(ExporterInterface::class),
            ])
            ->addTag('controller.service_arguments');

        $container->setDefinition(BulkExportAction::class, $definition);
    }

    protected function getMetadataDefinition(MetadataInterface $metadata): Definition
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference('sylius.resource_registry'), 'get'])
            ->setArguments([$metadata->getAlias()])
        ;

        return $definition;
    }
}
