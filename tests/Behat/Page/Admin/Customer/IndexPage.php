<?php

/*
 * This file is part of rd_082_s_sylius_export_plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Page\Admin\Customer;

use Sylius\Component\Customer\Model\CustomerInterface;
use Tests\Mobizel\SyliusExportPlugin\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getCustomerAccountStatus(CustomerInterface $customer): string
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);

        return $tableAccessor->getFieldFromRow($table, $row, 'enabled')->getText();
    }
}

