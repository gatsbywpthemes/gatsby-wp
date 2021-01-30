/* global wp */
/* eslint-disable */
;(function ($) {
  "use strict"
  console.log("hello", yaga_setup_scriptparams)
  var defaultSuccess = function (response) {
    console.log(response)
  }

  var installer = {
    reloadOnSuccessfulEnd: true,
    notification: {},
    targetLink: {},
    installed: [],
    installs: {},
    init: function () {
      var self = this

      self.installs = yaga_setup_scriptparams.all_plugins.map(
        ({ slug, nonce }) => {
          return {
            method: "POST",
            credentials: "same-origin",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
              "Cache-Control": "no-cache",
            },
            body: new URLSearchParams({
              plugin: slug,
              action: "yaga_setup_plugin_installer",
              nonce,
            }),
          }
        }
      )

      console.log(self.installs)

      if ({} === self.targetLink) {
        return
      }

      self.doInstall = !_.isUndefined($(self.targetLink).attr("data-install"))
        ? "true" === $(self.targetLink).attr("data-install")
        : false
      if (!_.isUndefined($(self.targetLink).attr("data-reload"))) {
        self.reloadOnSuccessfulEnd =
          "true" === $(self.targetLink).attr("data-reload")
      }

      self.targetLink
        .addClass("pht-setup__link--deactivated")
        .parent()
        .siblings()
        .slideUp(100)

      $(".js-pht--hidden").addClass("js-pht--visible")

      installer.notification.html(
        "<p class='" +
          yaga_setup_scriptparams.classes.start +
          "'>" +
          yaga_setup_scriptparams.strings.start +
          "</p>"
      )

      installer.installPlugins()
    },
    installPlugins: function (i = 0) {
      var self = this
      if (self.installs[i]) {
        fetch(ajaxurl, self.installs[i])
          .then((response) => {
            console.log(response)
            return response.json()
          })
          .then((response) => {
            console.log(response)
            if (response.success) {
              self.installed.push(response.data.plugin)
            }

            self.notification.append(
              "<div class='" +
                yaga_setup_scriptparams.classes.progress +
                "'><h4>" +
                yaga_setup_scriptparams.all_plugins[i].name +
                ":</h4>" +
                response.data.message +
                "</div>"
            )

            self.installPlugins(i + 1)
          })
          .catch((err) => {
            console.log(err)
            self.stopAction()
            self.notification.append(
              "<div class='" +
                yaga_setup_scriptparams.classes.fail +
                "'><p>" +
                yaga_setup_scriptparams.strings.plugins_fail +
                "</p></div>"
            )

            return false
          })
      } else {
        if (i !== self.installed.length) {
          self.stopAction()
          self.notification.append(
            "<div class='" +
              yaga_setup_scriptparams.classes.fail +
              "'><p>" +
              yaga_setup_scriptparams.strings.plugins_fail +
              "</p></div>"
          )
          $(".js-pht--visible").removeClass("js-pht--visible")
          return
        } else {
          self.terminate()
        }
      }
    },

    stopAction: function () {
      var self = this
      $(".js-pht--visible").removeClass("js-pht--visible")
      self.targetLink.slideUp(200)
      $(".js-pht-start-feedback").slideUp(200)
    },
    terminate: function (arg) {
      var self = this
      self.stopAction()
      self.notification.append(
        "<div class='" +
          yaga_setup_scriptparams.classes.success +
          "'><p>" +
          yaga_setup_scriptparams.strings.finished +
          "<p>"
      )
      if (!self.reloadOnSuccessfulEnd) {
        return
      }
      if (_.isUndefined(arg)) {
        window.location.replace(yaga_setup_scriptparams.current_page)
      } else {
        window.location.replace(
          yaga_setup_scriptparams.current_page + "&" + arg
        )
      }
    },
  }

  $(document).ready(function () {
    $(".js-pht-setup__link").click(function () {
      installer.targetLink = $(this)
      installer.notification = $(".pht-feedback")
      installer.init()
    })
  })
})(jQuery)
