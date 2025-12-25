import './bootstrap';

// Dark mode key
const darkModeKey = 'pivor-dark-mode';

// Toggle dark mode - exposed globally
window.toggleDarkMode = function() {
    const isDark = document.documentElement.classList.contains('dark');
    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem(darkModeKey, 'false');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem(darkModeKey, 'true');
    }
};

// Mobile sidebar - exposed globally
window.toggleSidebar = function() {
    const sidebar = document.getElementById('mobile-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (sidebar && backdrop) {
        sidebar.classList.toggle('hidden');
        backdrop.classList.toggle('hidden');
    }
};

window.closeSidebar = function() {
    const sidebar = document.getElementById('mobile-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (sidebar && backdrop) {
        sidebar.classList.add('hidden');
        backdrop.classList.add('hidden');
    }
};
