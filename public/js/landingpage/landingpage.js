document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("section");
    const navLinks = document.querySelectorAll(".nav-link");

    const setActiveLink = () => {
        let currentSection = null;

        sections.forEach((section) => {
            const rect = section.getBoundingClientRect();

            if (rect.top <= 100 && rect.bottom >= 100) {
                currentSection = section.getAttribute("id");
            }
        });

        navLinks.forEach((link) => {
            link.classList.remove("active");
        });

        if (currentSection) {
            const activeLink = document.querySelector(`a[href="#${currentSection}"]`);
            if (activeLink) {
                activeLink.classList.add("active");
            }
        }
    };

    window.addEventListener("scroll", setActiveLink);

    setActiveLink();
});