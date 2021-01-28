/* global wp */
/* eslint-disable */
import tinycolor from "tinycolor2"

const ratiosToCalculate = document.querySelectorAll("[data-prop1][data-prop2]")
const settings = []
ratiosToCalculate.forEach((el) => {
  settings.push(el.getAttribute("data-prop1").slice(2))
  settings.push(el.getAttribute("data-prop2").slice(2))
})
const setRatioInfo = (el) => {
  if (el) {
    const prop1 = el.getAttribute("data-prop1")
    const prop2 = el.getAttribute("data-prop2")
    const ratio1 = tinycolor.readability(
      getComputedStyle(document.documentElement).getPropertyValue(prop1),
      getComputedStyle(document.documentElement).getPropertyValue(prop2)
    )
    el.textContent = ratio1.toFixed(1)
    const wcag = el.closest(".wcag")
    if (ratio1 >= 7) {
      wcag.classList.add("wcag-aaas")
    } else {
      wcag.classList.remove("wcag-aaas")
      if (ratio1 >= 4.5) {
        wcag.classList.add("wcag-aaa")
      } else {
        wcag.classList.remove("wcag-aaa")
        if (ratio1 >= 3) {
          wcag.classList.add("wcag-aa")
        } else {
          wcag.classList.remove("wcag-aa")
        }
      }
    }
  }
}
ratiosToCalculate.forEach((el) => {
  setRatioInfo(el)
})
settings.forEach((s) => {
  wp.customize(s, function (setting) {
    setting.bind(function (hexValue) {
      const property = `--${s}`
      document.documentElement.style.setProperty(property, hexValue)
      const toRecalculate = Array.from(ratiosToCalculate).find(
        (el) =>
          el.getAttribute("data-prop1") === property ||
          el.getAttribute("data-prop2") === property
      )
      if (toRecalculate) {
        setRatioInfo(toRecalculate)
      }
    })
  })
})
