document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-message]').forEach((element) => {
        element.addEventListener('click', (event) => {
            const message = element.getAttribute('data-confirm-message') || 'Bạn có chắc muốn thực hiện thao tác này?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
