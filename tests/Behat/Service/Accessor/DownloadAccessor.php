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
use DMore\ChromeDriver\ChromeDriver;
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
        if ($driver instanceof Selenium2Driver || $driver instanceof ChromeDriver) {
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

        if ($driver instanceof Selenium2Driver || $driver instanceof ChromeDriver) {
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

        if ($driver instanceof ChromeDriver) {
            $driverReflection = new \ReflectionClass($driver);
            $optionsProperty = $driverReflection->getProperty('options');
            $optionsProperty->setAccessible(true);
            $options = $optionsProperty->getValue($driver);

            return $options['downloadPath'];
        }

        throw new \Exception('A Behat driver is needed.');
    }

    public function getSession(): Session
    {
        return $this->session;
    }
}
