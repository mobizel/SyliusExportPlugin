import $ from "jquery";

$('[data-bulk-export]').each((el) => {
$(el).click((evt) => {
    evt.preventDefault();

    const actionButton = $(evt.currentTarget);
    const form = actionButton.closest('form');

    let url = $(form).attr('action');
    url += location.search;

    $(form).attr('action', url);

    $('[name="ids[]"]', form).each((idx, el) => $(el).remove());

    $('input.bulk-select-checkbox:checked').each((index, element) => {
      $(`<input type="hidden" name="ids[]" value="${element.value}">`).appendTo(form);
    });

    form.submit();
  });
});