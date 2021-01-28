import { registerPlugin } from "@wordpress/plugins"
import { PluginSidebar, PluginSidebarMoreMenuItem } from "@wordpress/edit-post"
import { __ } from "@wordpress/i18n"
import { PanelBody, CheckboxControl } from "@wordpress/components"
import { withSelect, withDispatch } from "@wordpress/data"

let PluginMetaFields = (props) => {
  console.log(props)
  return (
    <>
      <PanelBody
        title={__("Meta Fields Panel", "textdomain")}
        icon="admin-post"
        intialOpen={true}
      >
        <CheckboxControl
          label={__("Skip title", "textdomain")}
          checked={!!props.skip_title_metafield}
          onChange={(checked) => props.onMetaFieldChange(checked)}
        />
      </PanelBody>
    </>
  )
}

PluginMetaFields = withSelect((select) => {
  return {
    skip_title_metafield: select("core/editor").getEditedPostAttribute("meta")[
      "_headlesswp_skip_title_metafield"
    ],
  }
})(PluginMetaFields)

PluginMetaFields = withDispatch((dispatch) => {
  return {
    onMetaFieldChange: (value) => {
      console.log("onMetaFieldChange", value)
      dispatch("core/editor").editPost({
        meta: { _headlesswp_skip_title_metafield: value },
      })
    },
  }
})(PluginMetaFields)

registerPlugin("headlesswp-sidebar", {
  icon: (
    <svg viewBox="0 0 225.59 209.12">
      <path
        fill="#8919f9"
        d="M432.39,198.11q-26,0-36.54,13.43t-10.54,42.61q0,45.93,26.86,45.92a56.59,56.59,0,0,0,7.22-.28q-3.47,0-5.63-12.14a144.65,144.65,0,0,1-2.17-25.41q0-13.29,7.22-17.19t20.08-3.9q12.86,0,17.91.14a95.19,95.19,0,0,1,11.55,1.16,29.46,29.46,0,0,1,10,3,15,15,0,0,1,8.08,13.87v42.17q0,22.53-8.08,36T448,353.22q-31.2,3.18-95.32,3.18-25.71,0-44.05-8.09t-28.45-22.53q-18.5-26.86-18.49-68.31t26.58-73.8Q314.83,151.33,359,149q30.33-1.74,43.91-1.73,49.4,0,70.19,6.06,14.14,4,14.15,22.82,0,25.13-14.73,25.13-4.62-.28-18.49-1.73T432.39,198.11Z"
        transform="translate(-261.68 -147.28)"
      />
    </svg>
  ),
  render: () => {
    const postType = wp.data.select("core/editor").getCurrentPostType()
    if (postType !== "page") {
      return null
    }
    return (
      <>
        <PluginSidebarMoreMenuItem target="headlesswp-sidebar">
          {__("Meta Options", "textdomain")}
        </PluginSidebarMoreMenuItem>
        <PluginSidebar
          name="headlesswp-sidebar"
          title={__("Meta Options", "textdomain")}
        >
          <PluginMetaFields />
        </PluginSidebar>
      </>
    )
  },
})
