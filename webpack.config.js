const defaultConfig = require("@wordpress/scripts/config/webpack.config")

module.exports = {
  ...defaultConfig,
  entry: {
    index: "./src/index.js",
    navigation: "./src/navigation.js",
    customizer: "./src/customizer.js",
    "customizer-preview": "./src/customizer-preview.js",
  },
  module: {
    ...defaultConfig.module,
    rules: [...defaultConfig.module.rules],
  },
}
