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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\CreatePageInterface;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface as CustomerIndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Admin\Customer\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Tests\Mobizel\SyliusExportPlugin\Behat\Service\Accessor\DownloadAccessor;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    /** @var CustomerIndexPageInterface */
    private $indexPage;

    /** @var DownloadAccessor */
    private $downloadAccessor;

    /**
     * @param IndexPageInterface $indexPage
     * @param DownloadAccessor $downloadAccessor
     */
    public function __construct(
        IndexPageInterface $indexPage,
        DownloadAccessor $downloadAccessor
    ) {
        $this->indexPage = $indexPage;
        $this->downloadAccessor = $downloadAccessor;
    }

    /**
     * @Given I want to export customers
     */
    public function iWantExportCustomers()
    {
        $this->indexPage->bulkExport();
    }

    /**
     * @Then I should download a csv file with :amountOfCustomers customers
     */
    public function iShouldDownloadACsvFileWithCustomers(int $amountOfCustomers)
    {
        $content = $this->downloadAccessor->getContent();

        $csv = str_getcsv($content);

        Assert::eq(count($csv), $amountOfCustomers + 1);
    }

    /**
     * @Then the csv file should contains :email
     * @param string $email
     */
    public function theCsvFileShouldContains(string $email)
    {
        Assert::true($this->downloadAccessor->contentContains($email));
    }
}
