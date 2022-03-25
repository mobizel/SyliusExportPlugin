<?php

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin;

use Mobizel\SyliusExportPlugin\DependencyInjection\MobizelSyliusExportExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

if (class_exists(SyliusPluginTrait::class)) {
    final class MobizelSyliusExportPlugin extends Bundle
    {
        use SyliusPluginTrait;

        public function getContainerExtension(): ExtensionInterface
        {
            return new MobizelSyliusExportExtension();
        }
    }
} else {
    final class MobizelSyliusExportPlugin extends Bundle
    {
        public function getContainerExtension(): ExtensionInterface
        {
            return new MobizelSyliusExportExtension();
        }
    }
}

