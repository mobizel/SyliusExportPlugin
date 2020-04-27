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
use Symfony\Component\Finder\Finder;
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

    public function clearFiles() :void
    {
        $driver = $this->getSession()->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $finder = new Finder();

            $finder->sortByModifiedTime();

           foreach ($finder->in($this->getDownloadDir())->getIterator() as $file) {
               unlink($file->getRealPath());
           }
        }
    }

    public function getContent(string $filePattern = null): string
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof Selenium2Driver) {
            return $this->getFileContent($filePattern);
        }
        return $driver->getContent();
    }

    private function getFileContent(string $filePattern): string
    {
        $finder = new Finder();

        $finder->name($filePattern.'*')->sortByModifiedTime();

        $file = $finder->in($this->getDownloadDir())->getIterator()->current();

        return $file->getContents();
    }

    private function getDownloadDir(): string
    {
        $driver = $this->getSession()->getDriver();

        if ($driver instanceof Selenium2Driver) {
            $driverReflection = new \ReflectionClass($driver);
            $reflectionProperty = $driverReflection->getProperty('desiredCapabilities');
            $reflectionProperty->setAccessible(true);
            $capabilities = $reflectionProperty->getValue($driver);

            return $capabilities['chrome.prefs']['download']['default_directory'];
        }

        throw new \Exception('Only to use with selenium2Driver');
    }

    public function getSession(): Session
    {
        return $this->session;
    }
}