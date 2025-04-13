(function () {
  /**
   * Gets a cookie by its name.
   *
   * @param {string} name The name of the cookie to retrieve.
   * @return {?string} The value of the cookie, or null if not found.
   */
  function getCookie(name) {
    const match = document.cookie.match(
      new RegExp("(^| )" + name + "=([^;]+)")
    );
    return match ? decodeURIComponent(match[2]) : null;
  }

  /**
   * Sets a cookie with the given name, value, and lifetime in days.
   *
   * @param {string} name The name of the cookie to set.
   * @param {string} value The value of the cookie.
   * @param {number} [days=1] The lifetime of the cookie in days.
   */
  function setCookie(name, value, days = 1) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie =
      name +
      "=" +
      encodeURIComponent(value) +
      ";expires=" +
      d.toUTCString() +
      ";path=/";
  }

  document.addEventListener("DOMContentLoaded", function () {
    const overlay = document.getElementById("gal-overlay");
    const guestButton = overlay?.querySelector(".btn-primary");
    const elementToRemove = document.querySelector("body");
    const overlayText = overlay?.querySelector("p"); // First <p> with view info.

    if (!overlay || !overlayText) return;

    // Lock scroll and show overlay.
    document.body.style.overflow = "hidden";
    overlay.style.opacity = 0;
    overlay.style.transition = "opacity 0.4s ease";
    overlay.style.display = "flex";
    setTimeout(() => (overlay.style.opacity = 1), 10);

    const maxViews = parseInt(overlay.getAttribute("data-limit")) || 0;
    let views = parseInt(getCookie("guest_views")) || 0;

    if (views >= maxViews) {
      // Limit reached
      overlayText.textContent = "🚫 You’ve reached your guest viewing limit.";
      if (guestButton) guestButton.remove();

      elementToRemove.innerHTML = overlay.innerHTML;

      overlay.remove();
    } else {
      // Countdown then allow guest view.
      let countdown = 3;
      const originalText = "View as Guest";

      guestButton.disabled = true;
      guestButton.textContent = `${originalText} (${countdown})`;

      const interval = setInterval(() => {
        countdown--;
        if (countdown <= 0) {
          clearInterval(interval);
          guestButton.disabled = false;
          guestButton.textContent = originalText;
        } else {
          guestButton.textContent = `${originalText} (${countdown})`;
        }
      }, 1000);

      guestButton.addEventListener("click", function () {
        views++;
        setCookie("guest_views", views, 1);
        overlay.style.opacity = 0;
        setTimeout(() => {
          overlay.style.display = "none";
          document.body.style.overflow = "";
        }, 400);
      });
    }
  });
})();
