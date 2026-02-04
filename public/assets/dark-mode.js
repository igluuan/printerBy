document.addEventListener('DOMContentLoaded', () => {
    const themeSwitcher = document.getElementById('theme-switcher');
    const darkModeToggle = document.getElementById('dark-mode-toggle');

    // Check for saved theme preference, or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';

    if (currentTheme === 'dark') {
        themeSwitcher.classList.add('dark-mode');
        darkModeToggle.querySelector('i').classList.remove('bi-moon-fill');
        darkModeToggle.querySelector('i').classList.add('bi-sun-fill');
    }

    darkModeToggle.addEventListener('click', () => {
        if (themeSwitcher.classList.contains('dark-mode')) {
            themeSwitcher.classList.remove('dark-mode');
            darkModeToggle.querySelector('i').classList.remove('bi-sun-fill');
            darkModeToggle.querySelector('i').classList.add('bi-moon-fill');
            localStorage.setItem('theme', 'light');
        } else {
            themeSwitcher.classList.add('dark-mode');
            darkModeToggle.querySelector('i').classList.remove('bi-moon-fill');
            darkModeToggle.querySelector('i').classList.add('bi-sun-fill');
            localStorage.setItem('theme', 'dark');
        }
    });
});
