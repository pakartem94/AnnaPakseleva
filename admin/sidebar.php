<aside class="admin-sidebar">
    <div class="admin-sidebar__header">
        <h2>Админ-панель</h2>
        <p class="admin-sidebar__user"><?= htmlspecialchars($_SESSION['admin_username']) ?></p>
    </div>
    <nav class="admin-sidebar__nav">
        <a href="dashboard.php" class="admin-sidebar__link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'admin-sidebar__link--active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            Главная
        </a>
        <a href="reviews.php" class="admin-sidebar__link <?= basename($_SERVER['PHP_SELF']) === 'reviews.php' ? 'admin-sidebar__link--active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Отзывы
        </a>
        <a href="portfolio.php" class="admin-sidebar__link <?= basename($_SERVER['PHP_SELF']) === 'portfolio.php' ? 'admin-sidebar__link--active' : '' ?>">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
            Портфолио
        </a>
        <a href="leads.php" class="admin-sidebar__link <?= basename($_SERVER['PHP_SELF']) === 'leads.php' ? 'admin-sidebar__link--active' : '' ?>">
            <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
            Заявки
        </a>
        <a href="change-password.php" class="admin-sidebar__link <?= basename($_SERVER['PHP_SELF']) === 'change-password.php' ? 'admin-sidebar__link--active' : '' ?>">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Смена пароля
        </a>
    </nav>
    <div class="admin-sidebar__footer">
        <a href="logout.php" class="admin-sidebar__link">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Выход
        </a>
    </div>
</aside>

