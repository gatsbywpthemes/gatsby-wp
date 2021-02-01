/* global wp */
/* eslint-disable */
"use strict"
const plugins = headlesswp_setup_scriptparams.all_plugins
const requiredPlugins = plugins.filter((el) => el.required)
const triggerAll = document.querySelector("#headlesswp-install-all")
const triggerRequired = document.querySelector("#headlesswp-install-required")

const fetchSettings = (slug, nonce) => ({
  method: "POST",
  credentials: "same-origin",
  headers: {
    "Content-Type": "application/x-www-form-urlencoded",
    "Cache-Control": "no-cache",
  },
  body: new URLSearchParams({
    plugin: slug,
    action: "headlesswp_setup_plugin_installer",
    nonce,
  }),
})
const loop = async (plugins, trigger) => {
  let withErrors = false
  const notification = trigger.parentNode.querySelector(".headlesswp-feedback")
  const progressFeedback = trigger.parentNode.querySelector(
    ".js-progress-feedback "
  )
  progressFeedback.classList.remove("js-headlesswp--hidden")
  trigger.classList.add("js-headlesswp--hidden")
  for (const { slug, name, nonce } of plugins) {
    const url = slug === "contact-form-7" ? ajaxurl + "" : ajaxurl
    console.log(notification)
    notification.insertAdjacentHTML(
      "beforeend",
      "<div class='" +
        headlesswp_setup_scriptparams.classes.progress +
        "'>Checking <strong>" +
        name +
        "</strong></div>"
    )
    try {
      const response = await fetch(url, fetchSettings(slug, nonce))
      const feedback = await response.json()
      console.log(feedback)
      notification.insertAdjacentHTML(
        "beforeend",
        "<div class='" +
          headlesswp_setup_scriptparams.classes.success +
          "'><strong>" +
          name +
          ":</strong> - " +
          feedback.data.message +
          "</div>"
      )
    } catch (error) {
      console.log(error)
      notification.insertAdjacentHTML(
        "beforeend",
        "<div class='" +
          headlesswp_setup_scriptparams.classes.fail +
          "'><strong>" +
          name +
          ":</strong> - " +
          error +
          "</div>"
      )
      withErrors = true
    }
  }
  progressFeedback.classList.add("js-headlesswp--hidden")
  if (!withErrors) {
    setTimeout(
      () => window.location.replace(headlesswp_setup_scriptparams.current_page),
      1000
    )
  }
}
triggerAll?.addEventListener("click", () => {
  loop(plugins, triggerAll)
  console.log("finished")
})
triggerRequired?.addEventListener("click", () => {
  loop(requiredPlugins, triggerRequired)
})
