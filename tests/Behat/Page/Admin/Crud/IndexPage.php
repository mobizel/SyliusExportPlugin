<?php

/*
 * This file is part of rd_082_s_sylius_export_plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Page\Admin\Crud;

use \Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
/**
 * @author Kévin Régnier <kevin@mobizel.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function bulkExport(): void
    {
        $this->getElement('bulk_actions')->pressButton('Export');
    }
}
