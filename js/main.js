(() => {
  function initHomeSlider() {
    const slides = Array.from(document.querySelectorAll(".slider .slide"));
    if (slides.length <= 1) return;

    let currentIndex = 0;

    slides.forEach((slide, index) => {
      slide.style.display = index === 0 ? "block" : "none";
    });

    setInterval(() => {
      slides[currentIndex].style.display = "none";
      currentIndex = (currentIndex + 1) % slides.length;
      slides[currentIndex].style.display = "block";
    }, 4000);
  }

  function initLoginFormEnhancements() {
    const loginForm = document.querySelector(".login-form");
    if (!loginForm) return;

    const passwordInput = loginForm.querySelector("#password");
    if (!passwordInput) return;

    const toggleButton = document.createElement("button");
    toggleButton.type = "button";
    toggleButton.textContent = "Show password";
    toggleButton.className = "btn";
    toggleButton.style.marginTop = "0.5rem";

    toggleButton.addEventListener("click", () => {
      const showingPassword = passwordInput.type === "text";
      passwordInput.type = showingPassword ? "password" : "text";
      toggleButton.textContent = showingPassword ? "Show password" : "Hide password";
    });

    passwordInput.insertAdjacentElement("afterend", toggleButton);
  }

  function initClassFilters() {
    const filterForm = document.querySelector(".filter-group");
    if (!filterForm) return;

    const bookingTableBody = document.querySelectorAll(".schedule-box .tbody")[1];
    if (!bookingTableBody) return;

    const rows = Array.from(bookingTableBody.querySelectorAll("tr"));
    if (rows.length === 0) return;

    const noResults = document.createElement("p");
    noResults.className = "p";
    noResults.textContent = "No classes match the selected filters.";
    noResults.style.display = "none";
    filterForm.insertAdjacentElement("afterend", noResults);

    const getTypeKeywords = (type) => {
      const typeMap = {
        yoga: ["yoga", "mobility"],
        cardio: ["cycling", "cardio"],
        hiit: ["hiit", "conditioning"],
        functional: ["functional"],
        pilates: ["pilates", "core"],
        personal: ["personal"],
        toning: ["toning"]
      };
      return typeMap[type] || [];
    };

    filterForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const formData = new FormData(filterForm);
      const typeFilter = String(formData.get("type") || "").toLowerCase().trim();
      const trainerFilter = String(formData.get("trainer") || "").toLowerCase().trim();
      const dayFilter = String(formData.get("day") || "").toLowerCase().trim();
      const timeFilter = String(formData.get("time") || "").trim();

      let visibleCount = 0;

      rows.forEach((row) => {
        const className = row.querySelector("th")?.textContent?.toLowerCase() || "";
        const trainerName = row.querySelectorAll("td")[0]?.textContent?.toLowerCase() || "";
        const dayTime = row.querySelectorAll("td")[1]?.textContent || "";
        const [dayText, timeText] = dayTime.split(",").map((value) => value.trim());

        const dayValue = (dayText || "").toLowerCase();
        const matchesType =
          typeFilter === "" ||
          getTypeKeywords(typeFilter).some((keyword) => className.includes(keyword));
        const matchesTrainer = trainerFilter === "" || trainerName.includes(trainerFilter);
        const matchesDay = dayFilter === "" || dayValue === dayFilter;
        const matchesTime = timeFilter === "Any time" || dayTime.includes(timeFilter);

        const isVisible = matchesType && matchesTrainer && matchesDay && matchesTime;
        row.style.display = isVisible ? "" : "none";

        if (isVisible) visibleCount += 1;
      });

      noResults.style.display = visibleCount === 0 ? "block" : "none";
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    initHomeSlider();
    initLoginFormEnhancements();
    initClassFilters();
  });
})();
