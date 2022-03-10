<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Mobizel\SyliusExportPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface;
use Tests\Mobizel\SyliusExportPlugin\Behat\Page\Admin\Customer\IndexPage;
use Tests\Mobizel\SyliusExportPlugin\Behat\Service\Accessor\DownloadAccessor;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    private IndexPage $indexPage;

    private DownloadAccessor $downloadAccessor;

    private const FILE_PATTERN = 'export_sylius_customer';

    public function __construct(
        IndexPage $indexPage,
        DownloadAccessor $downloadAccessor
    ) {
        $this->indexPage = $indexPage;
        $this->downloadAccessor = $downloadAccessor;
    }

    /**
     * @Given I want to export customers
     */
    public function iWantExportCustomers(): void
    {
        $this->downloadAccessor->clearFiles();
        $this->indexPage->bulkExport();
    }

    /**
     * @When I check (also) the :email customer
     */
    public function iCheckTheCustomer(string $email): void
    {
        $this->indexPage->checkResourceOnPage(['email' => $email]);
    }

    /**
     * @Then I should download a csv file with :amountOfCustomers customer(s)
     */
    public function iShouldDownloadACsvFileWithCustomers(int $amountOfCustomers): void
    {
        $content = $this->downloadAccessor->getContent(self::FILE_PATTERN);

        $lines = explode(PHP_EOL, $content);

        Assert::eq(count($lines), $amountOfCustomers + 2);
    }

    /**
     * @Then the csv file should contains :email
     */
    public function theCsvFileShouldContains(string $email): void
    {

        Assert::contains($this->downloadAccessor->getContent(self::FILE_PATTERN), $email);
    }

    /**
     * @Then the csv file should not contains :email
     */
    public function theCsvFileShouldNotContains(string $email): void
    {
        Assert::notContains($this->downloadAccessor->getContent(self::FILE_PATTERN), $email);
    }

    /**
     * @Then I filter customers by value :search
     */
    public function iFilterCustomers(string $search): void
    {
        $this->indexPage->searchForCustomers($search);
    }
}
