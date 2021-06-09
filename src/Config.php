<?php

namespace pammel\SimpleWordpressCms;

class Config implements ConfigInterface
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
        <div id="wpShadowContent" style="visibility: hidden;">
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
            
            // avoid flickering
            window.onload = function(){
               shadowContent.style.visibility = "visible";
            }

        </script>
    ';

    /**
     * @var string
     */
    private $htmlHeadSelector = 'title, link[rel=canonical], meta[name=description], meta[property^=og], meta[name^=twitter], script[class=yoast-schema-graph]';

    public function getWordpressUrl(): string
    {
        return $this->wordpressUrl;
    }

    public function setWordpressUrl(string $wordpressUrl): ConfigInterface
    {
        $this->wordpressUrl = $wordpressUrl;
        return $this;
    }

    public function getProjectUrl(): string
    {
        return $this->projectUrl;
    }

    public function setProjectUrl(string $projectUrl): ConfigInterface
    {
        $this->projectUrl = $projectUrl;
        return $this;
    }

    public function isAutoConvertWordpressUrlIntoProjectUrl(): bool
    {
        return $this->autoConvertWordpressUrlIntoProjectUrl;
    }

    public function setAutoConvertWordpressUrlIntoProjectUrl(bool $autoConvertWordpressUrlIntoProjectUrl): ConfigInterface
    {
        $this->autoConvertWordpressUrlIntoProjectUrl = $autoConvertWordpressUrlIntoProjectUrl;
        return $this;
    }

    public function getCssFilesAdditional(): array
    {
        return $this->cssFilesAdditional;
    }

    public function setCssFilesAdditional(array $cssFilesAdditional): ConfigInterface
    {
        $this->cssFilesAdditional = $cssFilesAdditional;
        return $this;
    }

    public function getCssMergedFilename(): string
    {
        return $this->cssMergedFilename;
    }

    public function setCssMergedFilename(string $cssMergedFilename): ConfigInterface
    {
        $this->cssMergedFilename = $cssMergedFilename;
        return $this;
    }

    public function getCssFolderLocal(): string
    {
        return $this->cssFolderLocal;
    }

    public function setCssFolderLocal(string $cssFolderLocal): ConfigInterface
    {
        $this->cssFolderLocal = $cssFolderLocal;
        return $this;
    }

    public function getCssFolderPublicUrl(): string
    {
        return $this->cssFolderPublicUrl;
    }

    public function setCssFolderPublicUrl(string $cssFolderPublicUrl): ConfigInterface
    {
        $this->cssFolderPublicUrl = $cssFolderPublicUrl;
        return $this;
    }

    public function getCssPregReplace(): array
    {
        return $this->cssPregReplace;
    }

    public function setCssPregReplace(array $cssPregReplace): ConfigInterface
    {
        $this->cssPregReplace = $cssPregReplace;
        return $this;
    }

    public function getHtmlBodyPregReplace(): array
    {
        return $this->htmlBodyPregReplace;
    }

    public function setHtmlBodyPregReplace(array $htmlBodyPregReplace): ConfigInterface
    {
        $this->htmlBodyPregReplace = $htmlBodyPregReplace;
        return $this;
    }

    public function getHtmlHeadPregReplace(): array
    {
        return $this->htmlHeadPregReplace;
    }

    public function setHtmlHeadPregReplace(array $htmlHeadPregReplace): ConfigInterface
    {
        $this->htmlHeadPregReplace = $htmlHeadPregReplace;
        return $this;
    }

    public function getHtmlBodyTemplate(): string
    {
        return $this->htmlBodyTemplate;
    }

    public function setHtmlBodyTemplate(string $htmlBodyTemplate): ConfigInterface
    {
        $this->htmlBodyTemplate = $htmlBodyTemplate;
        return $this;
    }

    public function getHtmlHeadSelector(): string
    {
        return $this->htmlHeadSelector;
    }

    public function setHtmlHeadSelector(string $htmlHeadSelector): ConfigInterface
    {
        $this->htmlHeadSelector = $htmlHeadSelector;
        return $this;
    }

}