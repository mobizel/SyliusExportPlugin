<?php

declare(strict_types=1);

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Behaviour;

use FriendsOfBehat\PageObjectExtension\Page\Page;
use Sylius\Behat\Service\JQueryHelper;

/**
 * @mixin Page
 */
trait ExportIt
{
    public function bulkExport(): void
    {
        $this->getElement('bulk_actions')->clickLink('Export');
        JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        sleep(5);
    }
}
