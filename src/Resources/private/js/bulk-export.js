const form = document.querySelector('#bulk-export');
const exportButton = form.querySelector('button');

exportButton.addEventListener('click', (evt) => {
    evt.preventDefault();

    let url = form.getAttribute('action');
    url += location.search;

    form.setAttribute('action', url);

    form.querySelectorAll('[name="ids[]"]').forEach((el) => el.parentNode.removeChild(el));

    document.querySelectorAll('input.bulk-select-checkbox:checked').forEach((element) => {
        const input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'ids[]');
        input.setAttribute('value', element.value);

        form.appendChild(input);
    });

    form.submit();
});

$.fn.extend({
    checkAll() {
        this.each((idx, el) => {
            const $checkboxAll = $(el);
            const $checkboxes = $($checkboxAll.attr('data-js-bulk-checkboxes'));
            const $buttons = $($checkboxAll.attr('data-js-bulk-buttons'));

            const isAnyChecked = () => {
                let checked = false;
                $checkboxes.each((i, checkbox) => {
                    if (checkbox.checked) checked = true;
                });
                return checked;
            };

            const buttonsPropRefresh = () => {
                $buttons.find('button:not(.js-bulk-export)').prop('disabled', !isAnyChecked());
            };

            $checkboxAll.on('change', () => {
                $checkboxes.prop('checked', $(this).is(':checked'));
                buttonsPropRefresh();
            });

            $checkboxes.on('change', () => {
                $checkboxAll.prop('checked', isAnyChecked());
                buttonsPropRefresh();
            });

            buttonsPropRefresh();
        });
    },
});
