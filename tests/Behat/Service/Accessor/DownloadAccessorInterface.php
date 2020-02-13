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

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Service\Accessor;

use Behat\Mink\Session;
use Symfony\Component\HttpFoundation\Response;

interface DownloadAccessorInterface
{
    public function getSession(): Session;

    public function getContent(): string;
}