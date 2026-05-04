document.addEventListener('DOMContentLoaded', () => {
    const board = document.querySelector('[data-task-board]');
    if (!board) return;

    const feedback = board.querySelector('[data-task-feedback]');
    board.querySelectorAll('[data-task-status-select]').forEach((select) => {
        select.dataset.previousValue = select.value;
        select.addEventListener('change', () => updateTaskStatus(select, feedback));
    });
});

async function updateTaskStatus(select, feedback) {
    const formData = new FormData();
    formData.append('id', select.getAttribute('data-task-id') || '');
    formData.append('status', select.value);

    select.disabled = true;
    setTaskFeedback(feedback, 'Đang cập nhật...');

    try {
        const response = await fetch(`${window.APP_BASE_URL || '/quanly_khachhang/'}xu-ly-ajax/cap-nhat-trang-thai-cong-viec.php`, {
            method: 'POST',
            body: formData,
        });
        const data = await response.json();
        if (!data.thanh_cong) throw new Error(data.thong_bao || 'Không thể cập nhật.');

        select.dataset.previousValue = select.value;
        updateTaskBadge(select, data.du_lieu);
        setTaskFeedback(feedback, data.thong_bao || 'Đã cập nhật');
    } catch {
        select.value = select.dataset.previousValue || select.value;
        setTaskFeedback(feedback, 'Không thể cập nhật trạng thái.');
    } finally {
        select.disabled = false;
    }
}

function updateTaskBadge(select, data) {
    const row = select.closest('[data-task-row]');
    const badge = row?.querySelector('[data-task-status-badge]');
    if (!badge || !data) return;

    badge.textContent = data.status_label || '';
    badge.className = `badge ${data.badge_class || 'badge-soft-primary'}`;
}

function setTaskFeedback(feedback, message) {
    if (feedback) feedback.textContent = message;
}
