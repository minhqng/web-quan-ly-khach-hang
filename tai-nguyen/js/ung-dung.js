document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });

    const followUpToggle = document.querySelector('#create_follow_up');
    if (followUpToggle) {
        const titleInput = document.querySelector('#task_title');
        const dueInput = document.querySelector('#task_due_at');
        const syncFollowUpRequiredState = () => {
            const isRequired = followUpToggle.checked;
            if (titleInput) titleInput.required = isRequired;
            if (dueInput) dueInput.required = isRequired;
        };

        followUpToggle.addEventListener('change', syncFollowUpRequiredState);
        syncFollowUpRequiredState();
    }

    document.querySelectorAll('[data-confirm-message]').forEach((element) => {
        element.addEventListener('click', (event) => {
            const message = element.getAttribute('data-confirm-message') || 'Bạn có chắc muốn thực hiện thao tác này?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
