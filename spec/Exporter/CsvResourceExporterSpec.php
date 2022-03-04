<?php

namespace spec\Mobizel\SyliusExportPlugin\Exporter;

use Mobizel\SyliusExportPlugin\Exporter\CsvResourceExporter;
use Mobizel\SyliusExportPlugin\Writer\WriterInterface;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CsvResourceExporterSpec extends ObjectBehavior
{
    function let(
        TranslatorInterface $translator,
        ServiceRegistryInterface $fieldsRegistry,
        GridRendererInterface $gridRenderer,
        WriterInterface $writer
    ) {
        $this->beConstructedWith($translator, $fieldsRegistry, $gridRenderer, $writer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CsvResourceExporter::class);
    }

    function it_can_export(
        ResourceGridView $gridView,
        Grid $grid,
        Field $field,
        Pagerfanta $paginator,
        \stdClass $resource,
        GridRendererInterface $gridRenderer,
        WriterInterface $writer,
        TranslatorInterface $translator
    ) {
        $field->isEnabled()->willReturn(true);
        $field->getLabel()->willReturn('data');
        $translator->trans('data')->willReturn('translated_data');

        $gridView->getDefinition()->willReturn($grid);
        $grid->getEnabledFields()->willReturn([$field]);

        $gridView->getData()->willReturn($paginator);
        $paginator->getNbPages()->willReturn(1);
        $paginator->getCurrentPageResults()->willReturn([$resource]);
        $gridRenderer->renderField($gridView, $field, $resource)->willReturn('data');

        $gridView->getDefinition()->shouldBeCalled();
        $grid->getEnabledFields()->shouldBeCalled();
        $writer->start('test.txt')->shouldBeCalled();
        $gridView->getData()->shouldBeCalled();
        $paginator->getNbPages()->shouldBeCalled();
        $paginator->setCurrentPage(1)->shouldBeCalled();
        $gridRenderer->renderField($gridView, $field, $resource)->shouldBeCalled();
        $writer->write(['translated_data'])->shouldBeCalled();
        $writer->write(['data'])->shouldBeCalled();
        $writer->getContent()->shouldBeCalled();

        $this->export($gridView, 'test.txt');
    }
}
