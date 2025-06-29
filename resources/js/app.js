import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// DARK MODE TOGGLE BUTTON
var themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
var themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

if (
    localStorage.getItem("color-theme") === "dark" ||
    (!("color-theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

var themeToggleBtn = document.getElementById("theme-toggle");

// Update button icons based on current theme
function updateThemeToggleIcons() {
    if (document.documentElement.classList.contains('dark')) {
        themeToggleDarkIcon.classList.add('hidden');
        themeToggleLightIcon.classList.remove('hidden');
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
        themeToggleLightIcon.classList.add('hidden');
    }
}

// Initial icon state
updateThemeToggleIcons();

themeToggleBtn.addEventListener("click", function () {
    document.documentElement.classList.toggle('dark');

    // Update localStorage
    if (document.documentElement.classList.contains('dark')) {
        localStorage.setItem('color-theme', 'dark');
    } else {
        localStorage.setItem('color-theme', 'light');
    }

    // Update icons
    updateThemeToggleIcons();
});
