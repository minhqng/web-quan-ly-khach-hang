function initCustomerListAjax() {
    const form = document.querySelector('[data-customer-filter-form]');
    const body = document.querySelector('[data-customer-table-body]');
    if (!form || !body) return;

    const status = document.querySelector('[data-customer-ajax-status]');
    const debouncedLoad = debounce(() => loadCustomers(form, 1), 380);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadCustomers(form, 1);
    });
    form.querySelector('[name="tu_khoa"]')?.addEventListener('input', debouncedLoad);
    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => loadCustomers(form, 1));
    });
    document.querySelector('[data-customer-pagination]')?.addEventListener('click', (event) => {
        const link = event.target.closest('[data-page]');
        if (!link) return;
        event.preventDefault();
        loadCustomers(form, Number(link.getAttribute('data-page') || 1));
    });

    if (status) status.textContent = 'AJAX sẵn sàng';
}

async function loadCustomers(form, page) {
    const body = document.querySelector('[data-customer-table-body]');
    const status = document.querySelector('[data-customer-ajax-status]');
    const params = new URLSearchParams(new FormData(form));
    params.set('trang', String(page));

    try {
        setCustomerLoading(true, status);
        const response = await fetch(`${appBaseUrl()}xu-ly-ajax/loc-khach-hang.php?${params}`);
        const data = await response.json();
        if (!data.thanh_cong) throw new Error(data.thong_bao || 'Không thể tải danh sách.');

        renderCustomerRows(body, data.khach_hang || []);
        renderCustomerPagination(data.phan_trang || {});
        document.querySelector('[data-customer-total]').textContent = data.tong_dong || 0;
        window.history.replaceState(null, '', `${window.location.pathname}?${params}`);
        if (status) status.textContent = 'Đã cập nhật';
    } catch {
        if (status) status.textContent = 'Không thể tải dữ liệu AJAX';
    } finally {
        setCustomerLoading(false, status);
    }
}

function renderCustomerRows(body, customers) {
    body.replaceChildren();
    if (customers.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 6;
        cell.className = 'text-center text-muted py-5';
        cell.textContent = 'Không tìm thấy khách hàng phù hợp.';
        row.append(cell);
        body.append(row);
        return;
    }
    customers.forEach((customer) => body.append(createCustomerRow(customer)));
}

function renderCustomerPagination(page) {
    const nav = document.querySelector('[data-customer-pagination]');
    if (!nav) return;

    const current = Number(page.trang || 1);
    const total = Number(page.tong_trang || 1);
    nav.replaceChildren(
        pageButton('Trước', current - 1, Boolean(page.co_trang_truoc)),
        textElement('span', `Trang ${current}/${total}`),
        pageButton('Sau', current + 1, Boolean(page.co_trang_sau))
    );
}

function pageButton(label, page, enabled) {
    const link = textElement('a', label);
    link.href = '#';
    link.className = `btn btn-outline-primary ${enabled ? '' : 'disabled'}`;
    link.dataset.page = String(page);
    return link;
}

function setCustomerLoading(isLoading, status) {
    document.querySelector('[data-customer-list-region]')?.classList.toggle('is-loading', isLoading);
    if (isLoading && status) status.textContent = 'Đang tải...';
}
