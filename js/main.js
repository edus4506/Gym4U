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

  document.addEventListener("DOMContentLoaded", () => {
    initHomeSlider();
    initLoginFormEnhancements();
  });
})();
