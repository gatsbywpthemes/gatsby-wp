const getAllCheckboxes = (el) => {
  const allInputs = el.querySelectorAll(".sortable-pill-checkbox")
  let inputValues = []
  for (const input of allInputs) {
    if (input.checked) {
      inputValues.push(input.value)
    }
  }
  const output = el.querySelector(".customize-control-sortable-pill-checkbox")
  output.value = inputValues
  output.dispatchEvent(new Event("change"))
}

wp.customize.bind("ready", function () {
  const sortables = sortable(".customize-control-pill_checkbox .sortable", {
    placeholder: "pill-ui-state-highlight",
    forcePlaceholderSize: true,
  })
  console.log(sortables)
  for (const s of sortables) {
    s.addEventListener("sortupdate", function (e) {
      getAllCheckboxes(e.currentTarget.parentNode)
    })
  }

  const checkboxInputs = document.querySelectorAll(".sortable-pill-checkbox")
  for (input of checkboxInputs) {
    input.addEventListener("change", (e) => {
      getAllCheckboxes(e.target.parentNode.parentNode.parentNode)
    })
  }
})
