<?php

/*
 * This file is part of Mobizel Sylius export plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin\Routing;

use Gedmo\Sluggable\Util\Urlizer;
use Mobizel\SyliusExportPlugin\Controller\BulkExportAction;
use Sylius\Bundle\ResourceBundle\Routing\Configuration;
use Sylius\Bundle\ResourceBundle\Routing\RouteFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidParameterTypeException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;
use Sylius\Bundle\ResourceBundle\Routing\ResourceLoader as BaseResourceLoader;

final class ResourceLoader implements LoaderInterface
{
    /** @var RegistryInterface */
    private $resourceRegistry;

    /** @var RouteFactoryInterface */
    private $routeFactory;

    /** @var BaseResourceLoader */
    private $resourceLoader;

    public function __construct(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory
    ) {
        $this->resourceRegistry = $resourceRegistry;
        $this->routeFactory = $routeFactory;
        $this->resourceLoader = $resourceLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null): RouteCollection
    {
        $processor = new Processor();
        $configurationDefinition = new Configuration();

        $configuration = Yaml::parse($resource);
        $configuration = $processor->processConfiguration($configurationDefinition, ['routing' => $configuration]);

        $routes = $this->resourceLoader->load($resource, $type);

        if ((!empty($configuration['only']) && !in_array('bulkExport', $configuration['only']))
            || (!empty($configuration['except']) && in_array('bulkExport', $configuration['except']))) {
            return $routes;
        }

        $isApi = $type === 'sylius.resource_api';

        /** @var MetadataInterface $metadata */
        $metadata = $this->resourceRegistry->get($configuration['alias']);

        $rootPath = sprintf('/%s/', $configuration['path'] ?? Urlizer::urlize($metadata->getPluralName()));

        $bulkDeleteRoute = $this->createRoute($metadata, $configuration, $rootPath . 'bulk-export', 'bulkExport', ['POST'], $isApi);
        $routes->add($this->getRouteName($metadata, $configuration, 'bulk_export'), $bulkDeleteRoute);

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null): bool
    {
        return $this->resourceLoader->supports($resource, $type);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress InvalidReturnType Symfony docblocks are messing with us
     */
    public function getResolver()
    {
        return $this->resourceLoader->getResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->resourceLoader->setResolver($resolver);
    }

    private function createRoute(
        MetadataInterface $metadata,
        array $configuration,
        string $path,
        string $actionName,
        array $methods,
        bool $isApi = false
    ): Route
    {
        $defaults = [
            '_controller' => sprintf('sylius.controller.%s.export', $metadata->getName()),
        ];

        if ($isApi) {
            $defaults['_sylius']['serialization_groups'] = ['Default'];
        }
        if ($isApi) {
            $defaults['_sylius']['csrf_protection'] = false;
        }
        if (isset($configuration['grid'])) {
            $defaults['_sylius']['grid'] = $configuration['grid'];
        }
        if (isset($configuration['serialization_version'])) {
            $defaults['_sylius']['serialization_version'] = $configuration['serialization_version'];
        }
        if (isset($configuration['section'])) {
            $defaults['_sylius']['section'] = $configuration['section'];
        }
        if (!empty($configuration['criteria'])) {
            $defaults['_sylius']['criteria'] = $configuration['criteria'];
        }
        if (array_key_exists('filterable', $configuration)) {
            $defaults['_sylius']['filterable'] = $configuration['filterable'];
        }

        if (isset($configuration['permission'])) {
            $defaults['_sylius']['permission'] = $configuration['permission'];
        }
        if (isset($configuration['vars']['all'])) {
            $defaults['_sylius']['vars'] = $configuration['vars']['all'];
        }

        if (isset($configuration['vars'][$actionName])) {
            $vars = $configuration['vars']['all'] ?? [];
            $defaults['_sylius']['vars'] = array_merge($vars, $configuration['vars'][$actionName]);
        }

        $defaults['_sylius']['paginate'] = false;

        $exportFormat = $configuration['vars']['export_format'] ?? 'csv';
        $defaults['_sylius']['vars']['export_format'] = $exportFormat;

        return $this->routeFactory->createRoute($path, $defaults, [], [], '', [], $methods);
    }

    private function getRouteName(MetadataInterface $metadata, array $configuration, string $actionName): string
    {
        $sectionPrefix = isset($configuration['section']) ? $configuration['section'] . '_' : '';

        return sprintf('%s_%s%s_%s', $metadata->getApplicationName(), $sectionPrefix, $metadata->getName(), $actionName);
    }
}
