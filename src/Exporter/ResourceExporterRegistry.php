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

namespace Mobizel\SyliusExportPlugin\Exporter;

use Mobizel\SyliusExportPlugin\Exporter\Exception\ExporterAlreadyExistException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class ResourceExporterRegistry
{
    /** @var array  */
    private $exporters;

    /** @var string */
    private $fallbackFormat;

    /**
     * ResourceImporterRegistry constructor.
     * @param iterable $exporters
     * @param string $fallbackFormat
     * @throws ExporterAlreadyExistException
     */
    public function __construct(iterable $exporters, string $fallbackFormat)
    {
        foreach ($exporters as $exporter) {
            if (! $exporter instanceof ResourceExporterInterface) {
                throw new InvalidTypeException(
                    sprintf("Exporter must be of type %s, %s class found", ResourceExporterInterface::class, get_class($exporter))
                );
            }

            $format = $exporter->getFormat();

            if (isset($this->exporters[$format])) {
                throw new ExporterAlreadyExistException(
                    sprintf('There is already an exporter defined from format %s: %s', $format, get_class($this->exporters[$format]))
                );
            }

            $this->exporters[$format] = $exporter;
        }

        $this->fallbackFormat = $fallbackFormat;
    }

    /**
     * @param string $format
     * @return bool
     * @throws \Exception
     */
    public function getExporter(?string $format): ResourceExporterInterface
    {
        $exporter = $this->exporters[$format] ?? null;

        if (null === $exporter) {
            $exporter = $this->exporters[$this->fallbackFormat] ?? null;
        }

        if (null === $exporter) {
            throw new \Exception(
                sprintf('No exporter found for format %s, and no exporter found for fallback format %s', $format, $this->fallbackFormat)
            );
        }

        return $exporter;
    }
}
