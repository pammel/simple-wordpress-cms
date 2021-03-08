<?php

namespace pammel\SimpleWordpressCms;

interface ConfigInterface
{
    public function setWordpressUrl(string $wordpressUrl): ConfigInterface;

    public function setProjectUrl(string $projectUrl): ConfigInterface;

    /**
     *  If set true, all Wordpress-URLs in headerHtml and bodyHtml are replaced by project-URL. Except images-URLs, these remain the same, because they are stored on the Wordpress server.
     *
     * default: true
     */
    public function setAutoConvertWordpressUrlIntoProjectUrl(bool $autoConvertWordpressUrlIntoProjectUrl): ConfigInterface;

    public function setCssFilesAdditional(array $cssFilesAdditional): ConfigInterface;

    /**
     * default: 'wp-merged.css'
     */
    public function setCssMergedFilename(string $cssMergedFilename): ConfigInterface;

    public function setCssFolderLocal(string $cssFolderLocal): ConfigInterface;

    public function setCssFolderPublicUrl(string $cssFolderPublicUrl): ConfigInterface;

    /**
     * default = [':root' => '']
     * because :root don't work in shadowRoot
     */
    public function setCssPregReplace(array $cssPregReplace): ConfigInterface;

    public function setHtmlBodyPregReplace(array $htmlBodyPregReplace): ConfigInterface;

    public function setHtmlHeadPregReplace(array $htmlHeadPregReplace): ConfigInterface;

    /**
     * default:
     *
     * <div id="wpShadow"></div>
     * <div id="wpShadowContent">
     *    <%wpContent%>
     * </div>
     *
     * <script>
     *    let shadowContent = document.querySelector("#wpShadowContent");
     *
     *    let shadow = document.querySelector("#wpShadow");
     *    shadow.attachShadow({mode: "open", delegatesFocus: false});
     *    shadow.shadowRoot.append(shadowContent);
     *
     *    let el = document.createElement("link");
     *    el.setAttribute("rel", "stylesheet");
     *    el.setAttribute("type", "text/css");
     *    el.setAttribute("href", "<%cssMergedFile%>");
     *    shadow.shadowRoot.append(el);
     * </script>
     */
    public function setHtmlBodyTemplate(string $htmlBodyTemplate): ConfigInterface;

    /**
     * had tags from wordpress page which are included if calling method PageInterface::getHtmlHead()
     *
     * default: 'title, link[rel=canonical], meta[name=description], meta[property^=og], meta[name^=twitter], script[class=yoast-schema-graph]';
     */
    public function setHtmlHeadSelector(string $htmlHeadSelector): ConfigInterface;
}