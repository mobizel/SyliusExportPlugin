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
