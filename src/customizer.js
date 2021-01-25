import sortable from "html5sortable/dist/html5sortable.es.js"
function isValidHttpUrl(string) {
  let url

  try {
    url = new URL(string)
  } catch (_) {
    return false
  }

  return url.protocol === "http:" || url.protocol === "https:"
}

class DraggableInputs {
  constructor({
    elSelector,
    outputSelector,
    sortableSelector,
    sortableOptions = {},
    validationFunction = () => true,
    errorMessage = "Invalid format",
  }) {
    this.elSelector = elSelector
    this.sortableSelector = sortableSelector
    this.sortableOptions = sortableOptions
    this.outputSelector = outputSelector
    this.sortable = null
    this.validationFunction = validationFunction
    this.errorMessage = errorMessage
  }

  start() {
    if (
      this.sortableSelector &&
      document.querySelector(this.sortableSelector)
    ) {
      this.startSortable()
    }
    this.output = this.outputSelector
      ? document.querySelector(this.outputSelector)
      : null
    this.getElements()
    this.addListeners()
  }

  getElements() {
    this.elements = this.elSelector
      ? document.querySelectorAll(this.elSelector)
      : []
  }

  startSortable() {
    this.sortable = sortable(this.sortableSelector, this.sortableOptions)[0]
  }

  addListeners() {
    this.sortable?.addEventListener("sortupdate", () => {
      this.getElements()
      this.getAllInputs()
    })

    this.elements?.forEach((el) => {
      const input = el.querySelector("input")
      input.addEventListener("change", () => {
        this.onSubmit(input, el)
      })
      input.addEventListener("blur", () => {
        if (!input.value) {
          this.onSubmit(input, el)
        }
      })
      input.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
          this.onSubmit(input, el)
        }
      })
    })
  }

  isValid(value) {
    return this.validationFunction(value)
  }

  setError(input) {
    input.classList.add("invalid")
    input.parentNode.querySelector(
      ".validation-status"
    ).textContent = this.errorMessage
  }

  setOk(input) {
    input.classList.remove("invalid")
    input.parentNode.querySelector(".validation-status").textContent = ""
  }

  onSubmit(input, el) {
    if (input.value) {
      if (!this.isValid(input.value)) {
        this.setError(input)
      } else {
        this.setOk(input)
        this.moveUp(input, el)
      }
    } else {
      this.setOk(input)
      this.reset(input, el)
    }
  }

  getAllInputs() {
    console.log("getAllInputs", this.output)
    if (this.output) {
      const inputValues = []
      this.elements.forEach((el) => {
        const input = el.querySelector("input")
        if (input.value) {
          inputValues.push(input.getAttribute("data-setting"))
        }
      })
      this.output.value = inputValues
      this.output.dispatchEvent(new Event("change"))
    }
  }

  moveUp(input, el) {
    Array.prototype.slice.call(this.elements).find(function (empty) {
      return (
        empty.getAttribute("data-contains-setting") >
        input.getAttribute("data-setting")
      )
    })
    const notEmpties = Array.prototype.slice
      .call(this.elements)
      .filter((el) => el.classList.contains("not-empty"))

    if (notEmpties.length) {
      notEmpties[notEmpties.length - 1].after(el)
    } else {
      el.parentNode.prepend(el)
    }
    input.focus()
    el.classList.add("not-empty")
    this.getElements()
    this.getAllInputs()
  }

  reset(input, el) {
    el.classList.remove("not-empty")
    const firstAfter = Array.prototype.slice
      .call(this.elements)
      .filter((el) => !el.classList.contains("not-empty"))
      .find(function (empty) {
        return (
          empty.getAttribute("data-contains-setting") >
          input.getAttribute("data-setting")
        )
      })
    if (firstAfter) {
      firstAfter.before(el)
    } else {
      el.parentNode.append(el)
    }
    this.getElements()
    this.getAllInputs()
  }
}

wp.customize.bind("ready", function () {
  const sortableSocialLinks = new DraggableInputs({
    elSelector: ".customize-control-wp-gatsby_all_follows label",
    outputSelector: "#gatsby-wp-social_follow_order",
    sortableSelector: ".customize-control-wp-gatsby_all_follows .sortable",
    validationFunction: isValidHttpUrl,
    errorMessage: "This is not a valid url.",
    sortableOptions: {
      placeholder: "pill-ui-state-highlight",
      forcePlaceholderSize: true,
      handle: ".js-drag-handle",
      items: "label",
    },
  })

  sortableSocialLinks.start()
})
