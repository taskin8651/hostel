function togglePass(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;

    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function initPasswordStrength() {
    const password = document.getElementById('password');
    const text = document.getElementById('strength-text');
    const bars = document.querySelectorAll('.strength-bar');

    if (!password || !text || !bars.length) return;

    password.addEventListener('input', function () {
        const val = this.value;
        let score = 0;

        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const colors = ['#EF4444', '#F59E0B', '#10B981', '#4F46E5'];
        const labels = ['Weak', 'Fair', 'Good', 'Strong'];

        bars.forEach((bar, index) => {
            bar.style.background = index < score ? colors[score - 1] : '#E2E8F0';
        });

        if (val.length === 0) {
            text.textContent = '';
            text.style.color = '#94A3B8';
        } else {
            text.textContent = labels[score - 1] || 'Weak';
            text.style.color = colors[score - 1] || '#EF4444';
        }
    });
}

function initAdminCheckboxes() {
    document.querySelectorAll('.role-checkbox-item, .admin-checkbox-item').forEach(item => {
        const checkbox = item.querySelector('input[type=checkbox]');

        if (!checkbox) return;

        const syncCheckedClass = () => {
            item.classList.toggle('checked', checkbox.checked);
        };

        syncCheckedClass();

        if (item.dataset.checkboxInitialized === 'true') return;

        item.dataset.checkboxInitialized = 'true';
        checkbox.addEventListener('change', syncCheckedClass);
    });

    document.querySelectorAll('[data-check-all]').forEach(button => {
        if (button.dataset.checkAllInitialized === 'true') return;

        button.dataset.checkAllInitialized = 'true';
        button.addEventListener('click', function () {
            const target = this.getAttribute('data-check-all') || '.role-checkbox-item';

            document.querySelectorAll(target).forEach(item => {
                const checkbox = item.querySelector('input[type=checkbox]');

                if (!checkbox) return;

                checkbox.checked = true;
                item.classList.add('checked');
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    document.querySelectorAll('[data-uncheck-all]').forEach(button => {
        if (button.dataset.uncheckAllInitialized === 'true') return;

        button.dataset.uncheckAllInitialized = 'true';
        button.addEventListener('click', function () {
            const target = this.getAttribute('data-uncheck-all') || '.role-checkbox-item';

            document.querySelectorAll(target).forEach(item => {
                const checkbox = item.querySelector('input[type=checkbox]');

                if (!checkbox) return;

                checkbox.checked = false;
                item.classList.remove('checked');
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initPasswordStrength();
    initAdminCheckboxes();
});
