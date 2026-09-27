</div>
<script>
    const alertcontainer = document.getElementById("alert-container");
    let alart = setTimeout(() => {
        alertcontainer.classList.add("hidden");
    }, 3000);
</script>

<script>
    function toggleDropdown(menuId) {
        // Close all dropdowns first
        document.querySelectorAll("ul[id^='menu-']").forEach(menu => {
            if (menu.id !== "menu-" + menuId) {
                menu.classList.add("hidden");
                let arrow = document.getElementById("arrow-" + menu.id.split("-")[1]);
                if (arrow) arrow.classList.remove("rotate-180");
            }
        });

        // Toggle selected dropdown
        let menu = document.getElementById("menu-" + menuId);
        let arrow = document.getElementById("arrow-" + menuId);
        if (menu.classList.contains("hidden")) {
            menu.classList.remove("hidden");
            if (arrow) arrow.classList.add("rotate-180");
        } else {
            menu.classList.add("hidden");
            if (arrow) arrow.classList.remove("rotate-180");
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const btn = document.getElementById("dropdownDividerButton");
        const menu = document.getElementById("dropdownDivider");

        btn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    });
</script>
</body>

</html>
