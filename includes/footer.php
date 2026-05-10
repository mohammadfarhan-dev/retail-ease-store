</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> RetailEase Store. Full-Stack Web Application with Smart Assistant Integration.</p>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Mobile hamburger navbar
    const navToggle = document.getElementById("navToggle");
    const navMenu = document.getElementById("navMenu");

    if (navToggle && navMenu) {
        navToggle.addEventListener("click", function () {
            navMenu.classList.toggle("open");
            navToggle.classList.toggle("active");

            const isOpen = navMenu.classList.contains("open");
            navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });

        navMenu.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                navMenu.classList.remove("open");
                navToggle.classList.remove("active");
                navToggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    // Real-time search and filter for all filter forms
    document.querySelectorAll(".filter-form").forEach(function (form) {
        const submitButton = form.querySelector('button[type="submit"]');

        if (submitButton) {
            submitButton.style.display = "none";
        }

        const searchableFields = form.querySelectorAll('input[type="text"], input[type="search"], select');
        let typingTimer;

        searchableFields.forEach(function (field) {
            if (field.tagName.toLowerCase() === "select") {
                field.addEventListener("change", function () {
                    form.submit();
                });
            } else {
                field.addEventListener("input", function () {
                    clearTimeout(typingTimer);

                    typingTimer = setTimeout(function () {
                        form.submit();
                    }, 450);
                });
            }
        });
    });
});
</script>

</body>
</html>