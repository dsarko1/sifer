(() => {
    const root = document.documentElement;
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeLabel = document.querySelector('[data-theme-label]');
    const preferenceKey = 'sifer-theme';

    let savedTheme = null;
    try {
        savedTheme = window.localStorage.getItem(preferenceKey);
    } catch {
        savedTheme = null;
    }

    const setTheme = (theme) => {
        const nextTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', nextTheme);
        if (themeLabel) {
            themeLabel.textContent = nextTheme === 'dark' ? 'Tema claro' : 'Tema oscuro';
        }
        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(nextTheme === 'dark'));
        }
    };

    setTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(nextTheme);
            try {
                window.localStorage.setItem(preferenceKey, nextTheme);
            } catch {
                // The visual toggle still works when storage is unavailable.
            }
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm');
            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
})();
