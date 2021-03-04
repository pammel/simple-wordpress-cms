<?php

namespace pammel\SimpleWordpressCms;

class Config
{
    /**
     * @var string
     */
    private $wordpressUrl;

    /**
     * @var string
     */
    private $projectUrl;

    /**
     * If true, all Wordpress-URLs in headerHtml and bodyHtml are replaced by project-URL. Except images-URLs, these remain the same, because they are stored on the Wordpress server.
     *
     * @var bool
     */
    private $autoConvertWordpressUrlIntoProjectUrl = true;

    /**
     * @var array
     */
    private $cssFilesAdditional = [];

    /**
     * @var string
     */
    private $cssMergedFilename = 'wp-merged.css';

    /**
     * @var string
     */
    private $cssFolderLocal;

    /**
     * @var string
     */
    private $cssFolderPublicUrl;

    /**
     * default = [':root' => ''] because :root don't work in shadowRoot
     *
     * @var array
     */
    private $cssPregReplace = ['/:root/' => ''];

    /**
     * @var array
     */
    private $htmlBodyPregReplace = [];

    /**
     * @var array
     */
    private $htmlHeadPregReplace = [];

    /**
     * @var string
     */
    private $htmlBodyTemplate = '    
        <div id="wpShadow"></div>
        <div id="wpShadowContent">
            <%wpContent%>
        </div>
    
        <script>
            let shadowContent = document.querySelector("#wpShadowContent");
    
            let shadow = document.querySelector("#wpShadow");
            shadow.attachShadow({mode: "open", delegatesFocus: false});
            shadow.shadowRoot.append(shadowContent);
    
            let el = document.createElement("link");
            el.setAttribute("rel", "stylesheet");
            el.setAttribute("type", "text/css");
            el.setAttribute("href", "<%cssMergedFile%>");
            shadow.shadowRoot.append(el);
        </script>
    ';

    /**
     * @var string
     */
    private $htmlHeadSelector = 'title, meta[name=description], meta[property^=og], meta[name^=twitter], script[class=yoast-schema-graph]';

    public function getWordpressUrl(): string
    {
        return $this->wordpressUrl;
    }

    public function setWordpressUrl(string $wordpressUrl): Config
    {
        $this->wordpressUrl = $wordpressUrl;
        return $this;
    }

    public function getProjectUrl(): string
    {
        return $this->projectUrl;
    }

    public function setProjectUrl(string $projectUrl): Config
    {
        $this->projectUrl = $projectUrl;
        return $this;
    }

    public function isAutoConvertWordpressUrlIntoProjectUrl(): bool
    {
        return $this->autoConvertWordpressUrlIntoProjectUrl;
    }

    public function setAutoConvertWordpressUrlIntoProjectUrl(bool $autoConvertWordpressUrlIntoProjectUrl): Config
    {
        $this->autoConvertWordpressUrlIntoProjectUrl = $autoConvertWordpressUrlIntoProjectUrl;
        return $this;
    }

    public function getCssFilesAdditional(): array
    {
        return $this->cssFilesAdditional;
    }

    public function setCssFilesAdditional(array $cssFilesAdditional): Config
    {
        $this->cssFilesAdditional = $cssFilesAdditional;
        return $this;
    }

    public function getCssMergedFilename(): string
    {
        return $this->cssMergedFilename;
    }

    public function setCssMergedFilename(string $cssMergedFilename): Config
    {
        $this->cssMergedFilename = $cssMergedFilename;
        return $this;
    }

    public function getCssFolderLocal(): string
    {
        return $this->cssFolderLocal;
    }

    public function setCssFolderLocal(string $cssFolderLocal): Config
    {
        $this->cssFolderLocal = $cssFolderLocal;
        return $this;
    }

    public function getCssFolderPublicUrl(): string
    {
        return $this->cssFolderPublicUrl;
    }

    public function setCssFolderPublicUrl(string $cssFolderPublicUrl): Config
    {
        $this->cssFolderPublicUrl = $cssFolderPublicUrl;
        return $this;
    }

    public function getCssPregReplace(): array
    {
        return $this->cssPregReplace;
    }

    public function setCssPregReplace(array $cssPregReplace): Config
    {
        $this->cssPregReplace = $cssPregReplace;
        return $this;
    }

    public function getHtmlBodyPregReplace(): array
    {
        return $this->htmlBodyPregReplace;
    }

    public function setHtmlBodyPregReplace(array $htmlBodyPregReplace): Config
    {
        $this->htmlBodyPregReplace = $htmlBodyPregReplace;
        return $this;
    }

    public function getHtmlHeadPregReplace(): array
    {
        return $this->htmlHeadPregReplace;
    }

    public function setHtmlHeadPregReplace(array $htmlHeadPregReplace): Config
    {
        $this->htmlHeadPregReplace = $htmlHeadPregReplace;
        return $this;
    }

    public function getHtmlBodyTemplate(): string
    {
        return $this->htmlBodyTemplate;
    }

    public function setHtmlBodyTemplate(string $htmlBodyTemplate): Config
    {
        $this->htmlBodyTemplate = $htmlBodyTemplate;
        return $this;
    }

    public function getHtmlHeadSelector(): string
    {
        return $this->htmlHeadSelector;
    }

    public function setHtmlHeadSelector(string $htmlHeadSelector): Config
    {
        $this->htmlHeadSelector = $htmlHeadSelector;
        return $this;
    }

}