function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const isVisible = input.type === 'text';
    input.type = isVisible ? 'password' : 'text';
    button.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
    button.setAttribute('title', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
    button.innerHTML = isVisible
        ? '<svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>'
        : '<svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 5.1A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.2 3.9M6.3 6.3C3.6 8.2 2 12 2 12s3.5 7 10 7a10 10 0 0 0 3.2-.5"/><path d="M9.9 9.9a3 3 0 1 0 4.2 4.2"/></svg>';
}

function initSettingsPage() {
    const tabs = document.querySelectorAll('[data-settings-tab]');
    const sections = document.querySelectorAll('.settings-section');
    if (!tabs.length || !sections.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.dataset.settingsTab;
            sections.forEach(section => section.classList.toggle('active', section.id === `${tabName}-section`));
            tabs.forEach(item => item.classList.toggle('active', item === tab));
        });
    });

    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => togglePassword(button));
    });
}

function formatLocalDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function initReportsPage() {
    document.querySelectorAll('[data-report-range]').forEach(button => {
        button.addEventListener('click', () => {
            const today = new Date();
            const startDate = new Date(today);
            const range = button.dataset.reportRange;

            if (range === 'week') startDate.setDate(startDate.getDate() - 6);
            if (range === 'month') startDate.setDate(startDate.getDate() - 29);
            if (range === 'year') startDate.setMonth(0, 1);

            const params = new URLSearchParams({
                start_date: formatLocalDate(startDate),
                end_date: formatLocalDate(today),
            });
            window.location.href = `${window.location.pathname}?${params}`;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSettingsPage();
    initReportsPage();
});
