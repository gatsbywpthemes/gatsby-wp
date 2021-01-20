/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
;(function () {
  "use strict"

  const togglers = document.querySelectorAll(
    '[id][data-bs-toggle="dropdown"], [data-bs-target][data-bs-toggle="collapse"]'
  )

  for (const toggler of togglers) {
    const target =
      toggler.getAttribute("data-bs-toggle") === "collapse"
        ? document.querySelector(toggler.getAttribute("data-bs-target"))
        : document.querySelector(`[aria-labelledby="${toggler.id}"]`)
    if (target) {
      toggler.addEventListener("click", function () {
        if (toggler.getAttribute("aria-expanded") === "true") {
          toggler.setAttribute("aria-expanded", "false")
        } else {
          toggler.setAttribute("aria-expanded", "true")
        }
        target.classList.toggle("show")
      })
    }
  }
})()
