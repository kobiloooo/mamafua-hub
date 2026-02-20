async function postForm(url, form) {
    const data = new FormData(form);
    const response = await fetch(url, { method: 'POST', body: data });
    return response.json();
}

const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await postForm('../api/auth.php?action=login', loginForm);
        alert(res.message);
        if (res.success) window.location.href = 'index.php?page=dashboard';
    });
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    const roleSelect = document.getElementById('roleSelect');
    const mamafuaFields = document.getElementById('mamafuaFields');
    roleSelect.addEventListener('change', () => mamafuaFields.classList.toggle('d-none', roleSelect.value !== 'mamafua'));
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await postForm('../api/auth.php?action=register', registerForm);
        alert(res.message);
        if (res.success) window.location.href = 'index.php?page=login';
    });
}

const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    const plan = document.getElementById('pricingPlan');
    const amountInput = document.getElementById('amountInput');
    plan.addEventListener('change', () => amountInput.value = plan.options[plan.selectedIndex].dataset.amount);

    bookingForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await postForm('../api/bookings.php?action=create', bookingForm);
        alert(res.message);
    });
}

const bookingHistory = document.getElementById('bookingHistory');
if (bookingHistory) {
    fetch('../api/bookings.php?action=mine')
        .then((r) => r.json())
        .then((res) => {
            if (!res.success) {
                bookingHistory.innerHTML = `<div class="alert alert-warning">${res.message}</div>`;
                return;
            }
            if (!res.data.length) {
                bookingHistory.innerHTML = '<p class="mb-0">No bookings yet.</p>';
                return;
            }
            bookingHistory.innerHTML = `<div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Service</th><th>Status</th><th>Payment</th></tr></thead><tbody>${res.data.map((b) => `<tr><td>${b.booking_date} ${b.start_time}</td><td>${b.service_type}</td><td>${b.status}</td><td>${b.payment_status}</td></tr>`).join('')}</tbody></table></div>`;
        });
}

async function logout() {
    const formData = new FormData();
    const res = await fetch('../api/auth.php?action=logout', { method: 'POST', body: formData });
    const json = await res.json();
    alert(json.message);
    window.location.href = 'index.php?page=home';
}


const documentForm = document.getElementById('documentForm');
if (documentForm) {
    documentForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const res = await postForm('../api/mamafua.php', documentForm);
        alert(res.message);
    });
}
