<?php

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin;

use Mobizel\SyliusExportPlugin\DependencyInjection\Compiler\ServicesPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class MobizelSyliusExportPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ServicesPass());
    }
}
