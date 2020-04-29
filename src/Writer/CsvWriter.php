<?php

/*
 * This file is part of Mobizel Sylius export plugin.
 *
 * (c) Mobizel.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mobizel\SyliusExportPlugin\Writer;

use Port\Csv\CsvWriter as PortCsvWriter;
/**
 * @author Kévin Régnier <kevin@mobizel.com>
 */
class CsvWriter implements WriterInterface
{
    /** @var  PortCsvWriter */
    private $writer;

    public function __construct(PortCsvWriter $writer)
    {
        $this->writer = $writer;
    }

    public function start(string $filename): void
    {
        $file = fopen($filename, 'w+');
        if (!$file) {
            throw new \Exception('File open failed.');
        }

        $this->writer->setStream($file);
    }

    public function getContent(): string
    {
        $this->writer->setCloseStreamOnFinish(true);

        rewind($this->writer->getStream());
        $contents = stream_get_contents($this->writer->getStream());
        if (false === $contents) {
            throw new \Exception(sprintf('Data stream could not be opened'));
        }

        $this->finish();

        return $contents;
    }

    public function write(array $data): void
    {
        $this->writer->writeItem($data);
    }

    public function finish(): void
    {
        $this->writer->finish();
    }
}
