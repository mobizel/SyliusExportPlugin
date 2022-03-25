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

namespace Tests\Mobizel\SyliusExportPlugin\Configuration;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Mobizel\SyliusExportPlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_does_not_break_if_not_customized(): void
    {
        $this->assertConfigurationIsValid(
            [
                [],
            ]
        );
    }

    /** @test */
    public function it_has_default_csv_settings(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            [
                'csv_settings' => [
                    'delimiter' => null,
                    'utf8_encoding' => true,
                ],
            ],
            'csv_settings'
        );
    }

    /** @test */
    public function its_settings_can_be_customized(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['csv_settings' => [
                    'delimiter' => ';',
                    'utf8_encoding' => false,
                ]],
            ],
            [
                'csv_settings' => [
                    'delimiter' => ';',
                    'utf8_encoding' => false,
                ],
            ],
            'csv_settings'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
