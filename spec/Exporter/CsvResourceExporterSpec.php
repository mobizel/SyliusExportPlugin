<?php

namespace spec\Mobizel\SyliusExportPlugin\Exporter;

use Mobizel\SyliusExportPlugin\Exporter\CsvResourceExporter;
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
        GridRendererInterface $gridRenderer
    ) {
        $this->beConstructedWith($translator, $fieldsRegistry, $gridRenderer);
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
        GridRendererInterface $gridRenderer
    ) {
        $field->isEnabled()->willReturn(true);
        $field->getLabel()->willReturn('data');

        $gridView->getDefinition()->willReturn($grid);
        $grid->getEnabledFields()->willReturn([$field]);

        $gridView->getData()->willReturn($paginator);
        $paginator->getNbPages()->willReturn(1);
        $paginator->getCurrentPageResults()->willReturn([$resource]);
        $gridRenderer->renderField($gridView, $field, $resource)->willReturn('data');

        $gridView->getDefinition()->shouldBeCalled();
        $grid->getEnabledFields()->shouldBeCalled();
        $gridView->getData()->shouldBeCalled();
        $paginator->getNbPages()->shouldBeCalled();
        $paginator->setCurrentPage(1)->shouldBeCalled();
        $gridRenderer->renderField($gridView, $field, $resource)->shouldBeCalled();

        $this->export($gridView);
    }
}
