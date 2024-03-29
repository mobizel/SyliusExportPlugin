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

use Behat\Mink\Driver\Selenium2Driver;
use DMore\ChromeDriver\ChromeDriver;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Behat\Page\Admin\Customer\IndexPage as BaseIndexPage;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface;
use Tests\Mobizel\SyliusExportPlugin\Behat\Behaviour\ExportIt;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    use ExportIt;

    public function getCustomerAccountStatus(CustomerInterface $customer): string
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);

        return $tableAccessor->getFieldFromRow($table, $row, 'enabled')->getText();
    }

    public function searchForCustomers(string $search): void
    {
        $this->openFilters();
        $this->getElement('filter_value')->setValue($search);
        $this->filter();
    }

    public function openFilters(): void
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof Selenium2Driver || $driver instanceof ChromeDriver) {
            $this->getElement('filters')->click();
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_value' => '#criteria_search_value',
            'filters' => '.accordion .title',
        ]);
    }
}

