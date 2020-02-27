<?php

namespace spec\Mobizel\SyliusExportPlugin\Routing;

use Gedmo\Sluggable\Util\Urlizer;
use Mobizel\SyliusExportPlugin\Routing\ResourceLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Routing\RouteFactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class ResourceLoaderSpec extends ObjectBehavior
{
    function let(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory
    )
    {
        $this->beConstructedWith($resourceLoader, $resourceRegistry, $routeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceLoader::class);
    }

    function it_implements_loader_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_adds_bulk_export_route(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routes,
        Route $route,
        MetadataInterface $metadata
    )
    {
        $resource = <<<EOD
alias: resource
section: admin
EOD;
        $type = 'sylius.resource';

        $metadata->getPluralName()->willReturn('resources');
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('resource');
        $resourceLoader->load($resource, $type)->willReturn($routes);
        $resourceRegistry->get(Argument::any())->willReturn($metadata);
        $routeFactory->createRoute(Argument::any(), Argument::any(), [], [], '', [], ['POST'])->willReturn($route);

        $resourceLoader->load($resource, $type)->shouldBeCalled();
        $resourceRegistry->get(Argument::any())->shouldBeCalled();
        $metadata->getPluralName()->shouldBeCalled();

        $routeFactory->createRoute('/resources/bulk-export', Argument::type('array'), [], [], '', [], ['POST'])->shouldBeCalled();
        $routes->add('sylius_admin_resource_bulk_export', Argument::type(Route::class))->shouldBeCalled();

        $this->load($resource, $type);
    }

    function it_adds_bulk_export_route_if_action_name_contained_in_only_option(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routes,
        Route $route,
        MetadataInterface $metadata
    )
    {
        $resource = <<<EOD
alias: resource
section: admin
only: ['bulkExport']
EOD;
        $type = 'sylius.resource';

        $metadata->getPluralName()->willReturn('resources');
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('resource');
        $resourceLoader->load($resource, $type)->willReturn($routes);
        $resourceRegistry->get(Argument::any())->willReturn($metadata);
        $routeFactory->createRoute(Argument::any(), Argument::any(), [], [], '', [], ['POST'])->willReturn($route);

        $resourceLoader->load($resource, $type)->shouldBeCalled();
        $resourceRegistry->get(Argument::any())->shouldBeCalled();
        $metadata->getPluralName()->shouldBeCalled();

        $routeFactory->createRoute('/resources/bulk-export', Argument::type('array'), [], [], '', [], ['POST'])->shouldBeCalled();
        $routes->add('sylius_admin_resource_bulk_export', Argument::type(Route::class))->shouldBeCalled();

        $this->load($resource, $type);
    }

    function it_cannot_add_bulk_export_route_if_action_name_not_contained_in_only_option(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routes,
        Route $route,
        MetadataInterface $metadata
    )
    {
        $resource = <<<EOD
alias: resource
section: admin
only: ['index']
EOD;
        $type = 'sylius.resource';

        $metadata->getPluralName()->willReturn('resources');
        $metadata->getApplicationName()->willReturn('sylius');
        $resourceLoader->load($resource, $type)->willReturn($routes);

        $resourceLoader->load($resource, $type)->shouldBeCalled();
        $resourceRegistry->get(Argument::any())->shouldNotBeCalled();
        $metadata->getPluralName()->shouldNotBeCalled();
        $routeFactory->createRoute('/resources/bulk-export', Argument::type('array'), [], [], '', [], ['POST'])->shouldNotBeCalled();
        $routes->add('sylius_admin_resource_bulk_export', Argument::type(Route::class))->shouldNotBeCalled();

        $this->load($resource, $type);
    }

    function it_adds_bulk_export_route_if_action_name_not_contained_in_except_option(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routes,
        Route $route,
        MetadataInterface $metadata
    )
    {
        $resource = <<<EOD
alias: resource
section: admin
except: ['index']
EOD;
        $type = 'sylius.resource';

        $metadata->getPluralName()->willReturn('resources');
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('resource');
        $resourceLoader->load($resource, $type)->willReturn($routes);
        $resourceRegistry->get(Argument::any())->willReturn($metadata);
        $routeFactory->createRoute(Argument::any(), Argument::any(), [], [], '', [], ['POST'])->willReturn($route);

        $resourceLoader->load($resource, $type)->shouldBeCalled();
        $resourceRegistry->get(Argument::any())->shouldBeCalled();
        $metadata->getPluralName()->shouldBeCalled();

        $routeFactory->createRoute('/resources/bulk-export', Argument::type('array'), [], [], '', [], ['POST'])->shouldBeCalled();
        $routes->add('sylius_admin_resource_bulk_export', Argument::type(Route::class))->shouldBeCalled();

        $this->load($resource, $type);
    }

    function it_cannot_add_bulk_export_route_if_action_name_contained_in_except_option(
        LoaderInterface $resourceLoader,
        RegistryInterface $resourceRegistry,
        RouteFactoryInterface $routeFactory,
        RouteCollection $routes,
        Route $route,
        MetadataInterface $metadata
    )
    {
        $resource = <<<EOD
alias: resource
section: admin
except: ['bulkExport']
EOD;
        $type = 'sylius.resource';

        $metadata->getPluralName()->willReturn('resources');
        $metadata->getApplicationName()->willReturn('sylius');
        $resourceLoader->load($resource, $type)->willReturn($routes);

        $resourceLoader->load($resource, $type)->shouldBeCalled();
        $resourceRegistry->get(Argument::any())->shouldNotBeCalled();
        $metadata->getPluralName()->shouldNotBeCalled();
        $routeFactory->createRoute('/resources/bulk-export', Argument::type('array'), [], [], '', [], ['POST'])->shouldNotBeCalled();
        $routes->add('sylius_admin_resource_bulk_export', Argument::type(Route::class))->shouldNotBeCalled();

        $this->load($resource, $type);
    }
}
