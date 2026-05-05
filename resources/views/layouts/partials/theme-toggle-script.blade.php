(function initThemeToggle() {
    var themeToggle = document.getElementById('themeToggle');
    var iconSun = document.getElementById('themeIconSun');
    var iconMoon = document.getElementById('themeIconMoon');
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (iconSun && iconMoon) {
            var isDark = theme !== 'light';
            iconSun.classList.toggle('active', isDark);
            iconSun.classList.toggle('inactive', !isDark);
            iconMoon.classList.toggle('active', !isDark);
            iconMoon.classList.toggle('inactive', isDark);
        }
    }
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            applyTheme(next);
        });
        applyTheme(localStorage.getItem('theme') === 'light' ? 'light' : 'dark');
    }
})();
