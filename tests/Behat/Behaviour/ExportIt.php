<?php

declare(strict_types=1);

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

/**
 * @method NodeElement getElement(string $name, array $parameters = [])
 */
trait ExportIt
{
    public function bulkExport(): void
    {
        $this->getElement('bulk_actions')->pressButton('Export');
    }
}
