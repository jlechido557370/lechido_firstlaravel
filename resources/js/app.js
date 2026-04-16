// ── Dark Mode ──────────────────────────────────────────────────────────────
(function () {
    const stored = localStorage.getItem("theme");
    const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)",
    ).matches;
    if (stored === "dark" || (!stored && prefersDark)) {
        document.documentElement.classList.add("dark");
    }
})();

document.addEventListener("DOMContentLoaded", function () {
    // ── Theme Toggle ──────────────────────────────────────────────────────────
    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
        btn.addEventListener("click", function () {
            const isDark = document.documentElement.classList.toggle("dark");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            // Update all toggle button icons
            document.querySelectorAll("[data-theme-icon]").forEach((icon) => {
                icon.textContent = isDark ? "☀" : "☾";
            });
            document.querySelectorAll("[data-theme-label]").forEach((label) => {
                label.textContent = isDark ? "Light mode" : "Dark mode";
            });
        });
    });

    // Set initial icon state
    const isDark = document.documentElement.classList.contains("dark");
    document.querySelectorAll("[data-theme-icon]").forEach((icon) => {
        icon.textContent = isDark ? "☀" : "☾";
    });
    document.querySelectorAll("[data-theme-label]").forEach((label) => {
        label.textContent = isDark ? "Light mode" : "Dark mode";
    });

    // ── User Dropdown ─────────────────────────────────────────────────────────
    const dropdownTrigger = document.getElementById("user-dropdown-trigger");
    const dropdownMenu = document.getElementById("user-dropdown-menu");
    const dropdownChevron = document.getElementById("user-dropdown-chevron");

    if (dropdownTrigger && dropdownMenu) {
        dropdownTrigger.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.contains("opacity-100");
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        document.addEventListener("click", function () {
            closeDropdown();
        });

        dropdownMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

    function openDropdown() {
        if (!dropdownMenu) return;
        dropdownMenu.classList.remove(
            "opacity-0",
            "pointer-events-none",
            "translate-y-[-4px]",
        );
        dropdownMenu.classList.add(
            "opacity-100",
            "pointer-events-auto",
            "translate-y-0",
        );
        if (dropdownChevron) dropdownChevron.style.transform = "rotate(180deg)";
    }

    function closeDropdown() {
        if (!dropdownMenu) return;
        dropdownMenu.classList.remove(
            "opacity-100",
            "pointer-events-auto",
            "translate-y-0",
        );
        dropdownMenu.classList.add(
            "opacity-0",
            "pointer-events-none",
            "translate-y-[-4px]",
        );
        if (dropdownChevron) dropdownChevron.style.transform = "rotate(0deg)";
    }

    // ── Mobile Sidebar (Admin) ─────────────────────────────────────────────────
    const sidebarOpenBtn = document.getElementById("sidebar-open");
    const sidebarCloseBtn = document.getElementById("sidebar-close");
    const sidebar = document.getElementById("admin-sidebar");
    const sidebarOverlay = document.getElementById("sidebar-overlay");

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        if (sidebarOverlay) sidebarOverlay.classList.remove("hidden");
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove("translate-x-0");
        sidebar.classList.add("-translate-x-full");
        if (sidebarOverlay) sidebarOverlay.classList.add("hidden");
    }

    if (sidebarOpenBtn) sidebarOpenBtn.addEventListener("click", openSidebar);
    if (sidebarCloseBtn)
        sidebarCloseBtn.addEventListener("click", closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener("click", closeSidebar);

    // ── Admin: Book Modal ──────────────────────────────────────────────────────
    const bookModal = document.getElementById("book-modal");
    const bookModalTitle = document.getElementById("book-modal-title");
    const bookModalClose = document.getElementById("book-modal-close");
    const bookModalCancel = document.getElementById("book-modal-cancel");
    const bookForm = document.getElementById("book-form");
    const bookFormMethod = document.getElementById("book-form-method");

    window.openAddBookModal = function () {
        if (!bookModal || !bookForm) return;
        bookModalTitle.textContent = "Add Book";
        bookForm.reset();
        bookForm.action = bookForm.dataset.storeUrl;
        if (bookFormMethod) bookFormMethod.value = "POST";
        openModal(bookModal);
    };

    window.openEditBookModal = function (book) {
        if (!bookModal || !bookForm) return;
        bookModalTitle.textContent = "Edit Book";
        bookForm.action = book.updateUrl;
        if (bookFormMethod) bookFormMethod.value = "PUT";
        bookForm.querySelector('[name="title"]').value = book.title;
        bookForm.querySelector('[name="author"]').value = book.author;
        bookForm.querySelector('[name="isbn"]').value = book.isbn;
        bookForm.querySelector('[name="published_year"]').value =
            book.published_year;
        bookForm.querySelector('[name="category"]').value = book.category;
        openModal(bookModal);
    };

    if (bookModalClose)
        bookModalClose.addEventListener("click", () => closeModal(bookModal));
    if (bookModalCancel)
        bookModalCancel.addEventListener("click", () => closeModal(bookModal));
    if (bookModal) {
        bookModal.addEventListener("click", function (e) {
            if (e.target === bookModal) closeModal(bookModal);
        });
    }

    // ── Delete confirmation inline toggle ─────────────────────────────────────
    window.confirmDelete = function (id) {
        const normal = document.getElementById(`actions-${id}`);
        const confirm = document.getElementById(`confirm-${id}`);
        if (normal && confirm) {
            normal.classList.add("hidden");
            confirm.classList.remove("hidden");
        }
    };

    window.cancelDelete = function (id) {
        const normal = document.getElementById(`actions-${id}`);
        const confirm = document.getElementById(`confirm-${id}`);
        if (normal && confirm) {
            normal.classList.remove("hidden");
            confirm.classList.add("hidden");
        }
    };

    // ── Helpers ────────────────────────────────────────────────────────────────
    function openModal(modal) {
        modal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }

    function closeModal(modal) {
        modal.classList.add("hidden");
        document.body.style.overflow = "";
    }

    // Auto-dismiss flash messages
    setTimeout(function () {
        document.querySelectorAll("[data-flash]").forEach((el) => {
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 300);
        });
    }, 4000);
});
