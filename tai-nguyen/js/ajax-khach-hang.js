document.addEventListener('DOMContentLoaded', () => {
    if (typeof initDuplicateChecks === 'function') initDuplicateChecks();
    if (typeof initCustomerListAjax === 'function') initCustomerListAjax();
});

function appBaseUrl() {
    return window.APP_BASE_URL || '/quanly_khachhang/';
}

function textElement(tag, text) {
    const element = document.createElement(tag);
    element.textContent = text || '';
    return element;
}

async function readJsonResponse(response) {
    const text = await response.text();

    try {
        return text ? JSON.parse(text) : {};
    } catch {
        throw new Error('Phản hồi máy chủ không phải JSON hợp lệ.');
    }
}

function debounce(callback, wait) {
    let timer = null;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => callback(...args), wait);
    };
}
