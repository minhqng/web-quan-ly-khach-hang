function initDuplicateChecks() {
    const form = document.querySelector('[data-customer-form]');
    if (!form) return;

    const customerId = form.getAttribute('data-customer-id') || '';
    form.querySelectorAll('[data-duplicate-field]').forEach((input) => {
        let timer = null;
        const runCheck = () => checkDuplicateField(form, input, customerId);
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(runCheck, 450);
        });
        input.addEventListener('blur', runCheck);
    });
}

async function checkDuplicateField(form, input, customerId) {
    const field = input.getAttribute('data-duplicate-field');
    const target = form.querySelector(`[data-duplicate-target="${field}"]`);
    const value = input.value.trim();

    if (!target || value === '') {
        setDuplicateState(target, '');
        return;
    }

    const params = new URLSearchParams({ truong: field, gia_tri: value, bo_qua_id: customerId });

    try {
        target.textContent = 'Đang kiểm tra...';
        const response = await fetch(`${appBaseUrl()}xu-ly-ajax/kiem-tra-trung-khach-hang.php?${params}`);
        const data = await readJsonResponse(response);
        if (!response.ok || !data.thanh_cong) throw new Error(data.thong_bao || '');
        setDuplicateState(target, data.thong_bao || '', Boolean(data.bi_trung), data.thanh_cong && !data.bi_trung);
    } catch {
        setDuplicateState(target, '');
    }
}

function setDuplicateState(target, message, duplicate = false, available = false) {
    if (!target) return;
    target.textContent = message;
    target.classList.toggle('is-duplicate', duplicate);
    target.classList.toggle('is-available', available && message !== '');
}
