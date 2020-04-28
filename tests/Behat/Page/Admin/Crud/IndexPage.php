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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use \Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Kévin Régnier <kevin@mobizel.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function bulkExport(): void
    {
        $this->getElement('bulk_actions')->pressButton('Export');

        $this->getSession()->wait('5000');
    }
}
