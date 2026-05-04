function createCustomerRow(customer) {
    const row = document.createElement('tr');
    row.append(
        cell(customerNameBlock(customer)),
        cell(customerContactBlock(customer)),
        cell(typeChip(customer)),
        cellText(customer.assigned_user_name),
        cell(statusBadge(customer)),
        cell(rowActions(customer), 'text-end')
    );
    return row;
}

function cell(content, className = '') {
    const td = document.createElement('td');
    if (className) td.className = className;
    td.append(content);
    return td;
}

function cellText(text) {
    const td = document.createElement('td');
    td.textContent = text || '';
    return td;
}

function customerNameBlock(customer) {
    const div = document.createElement('div');
    div.className = 'customer-name-cell';
    div.append(textElement('strong', customer.full_name), textElement('span', customer.subtitle));
    div.append(textElement('small', `${customer.interaction_count} tương tác · Lịch tới: ${customer.next_task_label}`));
    return div;
}

function customerContactBlock(customer) {
    const div = document.createElement('div');
    div.className = 'customer-contact-cell';
    div.append(textElement('span', customer.phone), textElement('span', customer.email));
    return div;
}

function typeChip(customer) {
    const span = textElement('span', customer.customer_type_name);
    span.className = 'customer-type-chip';
    span.style.setProperty('--type-color', customer.customer_type_color || '#64748b');
    return span;
}

function statusBadge(customer) {
    const span = textElement('span', customer.status_label);
    span.className = `badge ${customer.status_badge_class}`;
    return span;
}

function rowActions(customer) {
    const wrap = document.createElement('div');
    wrap.className = 'customer-row-actions';
    wrap.append(actionLink('Xem', customer.detail_url, 'btn-outline-primary'));
    if (customer.is_deleted) {
        wrap.append(actionForm(customer.restore_url, customer.id, 'Khôi phục', 'btn-outline-success'));
    } else {
        wrap.append(actionLink('Sửa', customer.edit_url, 'btn-outline-secondary'));
        wrap.append(actionForm(customer.soft_delete_url, customer.id, 'Xóa mềm', 'btn-outline-danger', 'Xóa mềm khách hàng này khỏi danh sách đang chăm sóc?'));
    }
    return wrap;
}

function actionLink(label, href, styleClass) {
    const link = textElement('a', label);
    link.href = href;
    link.className = `btn btn-sm ${styleClass}`;
    return link;
}

function actionForm(action, id, label, styleClass, confirmMessage = '') {
    const form = document.createElement('form');
    form.method = 'post';
    form.action = action;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = id;
    const button = textElement('button', label);
    button.type = 'submit';
    button.className = `btn btn-sm ${styleClass}`;
    if (confirmMessage) {
        button.addEventListener('click', (event) => {
            if (!window.confirm(confirmMessage)) event.preventDefault();
        });
    }
    form.append(input, button);
    return form;
}
