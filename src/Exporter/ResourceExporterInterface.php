<?php

/*
 * This file is part of sylius_export_plugin.
 *
 * (c) Mobizel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mobizel\SyliusExportPlugin\Exporter;

use Sylius\Component\Grid\View\GridViewInterface;

interface ResourceExporterInterface
{
    public function export(GridViewInterface $gridView): string;
}