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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class DownloadAccessor implements DownloadAccessorInterface
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getContent(): string
    {
        return $this->getSession()->getDriver()->getContent();
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function contentContains(string $content): bool {
        Assert::contains($this->getContent(), $content);
    }
}