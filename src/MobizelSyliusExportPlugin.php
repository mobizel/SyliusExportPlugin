<?php

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

if (class_exists(SyliusPluginTrait::class)) {
    final class MobizelSyliusExportPlugin extends Bundle
    {
        use SyliusPluginTrait;
    }
} else {
    final class MobizelSyliusExportPlugin extends Bundle
    {
    }
}

