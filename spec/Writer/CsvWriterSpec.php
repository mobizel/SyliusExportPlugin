<?php

namespace spec\Mobizel\SyliusExportPlugin\Writer;

use Mobizel\SyliusExportPlugin\Writer\CsvWriter;
use PhpSpec\ObjectBehavior;
use Port\Csv\CsvWriter as PortCsvWriter;
use Prophecy\Argument;

class CsvWriterSpec extends ObjectBehavior
{
    function let(PortCsvWriter $writer): void
    {
        $this->beConstructedWith($writer);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CsvWriter::class);
    }

    function it_can_start_writing(PortCsvWriter $writer): void
    {
        $filename = 'php://output';

        $writer->setStream(Argument::type('resource'))->shouldBeCalled();
        $this->start($filename);
    }

    function it_can_finish_writing(PortCsvWriter $writer)
    {
        $writer->finish()->shouldBeCalled();

        $this->finish();
    }

    function it_can_get_content(PortCsvWriter $writer)
    {
        $file = fopen('test.txt', 'w+');
        $writer->getStream()->willReturn($file);

        $writer->setCloseStreamOnFinish(true)->shouldBeCalled();
        $writer->getStream()->shouldBeCalled();
        $writer->finish()->shouldBeCalled();


        $this->getContent();
    }

    function it_can_write_data(PortCsvWriter $writer)
    {
        $data = ['data'];

        $writer->writeItem($data)->shouldBeCalled();

        $this->write($data);
    }
}
