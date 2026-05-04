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

function debounce(callback, wait) {
    let timer = null;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => callback(...args), wait);
    };
}
