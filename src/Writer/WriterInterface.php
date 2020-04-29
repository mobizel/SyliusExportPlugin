<?php

/*
 * This file is part of Mobizel Sylius export plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mobizel\SyliusExportPlugin\Writer;

interface WriterInterface
{
    public function start(string $fileName): void;

    public function finish(): void;

    public function getContent(): string;

    public function write(array $data): void;
}
