<?php

namespace pammel\SimpleWordpressCms;

class Page
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $wpContent;

    /**
     * @var array
     */
    private $wpCssFiles = [];

    /**
     * @var string
     */
    private $cssMergeHash;

    /**
     * @var string
     */
    private $cssMergedFilenameWithHash;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return '    
            <div id="wpShadow"></div>
            <div id="wpShadowContent">
                '.$this->getWpContent().'
            </div>
        
            <script>
                let shadowContent = document.querySelector("#wpShadowContent");
        
                let shadow = document.querySelector("#wpShadow");
                shadow.attachShadow({mode: "open", delegatesFocus: false});
                shadow.shadowRoot.append(shadowContent);
        
                var el = document.createElement("link");
                el.setAttribute("rel", "stylesheet");
                el.setAttribute("type", "text/css");
                el.setAttribute("href", "' . $this->getCssMergedFileUrl() . '");
                shadow.shadowRoot.append(el);
            </script>
        ';
    }

    private function mergeCssFiles()
    {
        if(!file_exists($this->getCssMergedFileLocal())) {

            # Clean up old files
            $files = glob($this->config->getCssFolderLocal() . '/' . explode('.', $this->getCssMergedFilenameWithHash())[0] . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            # merge css files
            $cssMerged = '';
            foreach ($this->getWpCssFiles(true) as $file) {
                $css = file_get_contents($file);
                if ($strReplace = $this->config->getCssReplace()) {
                    $css = str_replace(array_keys($strReplace), array_values($strReplace), $css);
                }

                $cssMerged .=
                   "/* " . str_repeat('-', 80) . " */ \n" .
                   "/* " . basename($file) . "*/ \n" .
                   "/* " . str_repeat('-', 80) . " */ \n" .
                   $css . "\n\n";
            }
            file_put_contents($this->getCssMergedFileLocal(), trim($cssMerged));
        }
    }

    private function getCssMergeHash(): string
    {
        if($this->cssMergeHash === null){
            $this->cssMergeHash = md5(serialize($this->getWpCssFiles(true)));
            $this->mergeCssFiles();
        }

        return $this->cssMergeHash;
    }

    public function getCssMergedFileLocal(): string
    {
        return $this->config->getCssFolderLocal() . '/' . $this->getCssMergedFilenameWithHash();
    }

    public function getCssMergedFileUrl(): string
    {
        return $this->config->getCssFolderPublicUrl() . '/' . $this->getCssMergedFilenameWithHash();
    }

    private function getCssMergedFilenameWithHash(): string
    {
        if($this->cssMergedFilenameWithHash === null){
            if(strpos($this->config->getCssMergedFilename(), '.') !== false){
                $parts = explode('.', $this->config->getCssMergedFilename());
                array_splice( $parts, count($parts)-1, 0, $this->getCssMergeHash() );
                $this->cssMergedFilenameWithHash = implode('.', $parts);
            }
            else{
                $this->cssMergedFilenameWithHash = $this->config->getCssMergedFilename() . '.' . $this->getCssMergeHash();
            }
        }

        return $this->cssMergedFilenameWithHash;
    }

    /**
     * @param bool $includeAdditional
     * @return array
     */
    public function getWpCssFiles($includeAdditional=true): array
    {
        if($includeAdditional){
            return array_merge($this->wpCssFiles, $this->config->getCssFilesAdditional());
        }

        return $this->wpCssFiles;
    }

    /**
     * @param array $wpCssFiles
     * @return Page
     */
    public function setWpCssFiles(array $wpCssFiles): Page
    {
        $this->wpCssFiles = $wpCssFiles;
        return $this;
    }

    /**
     * @return string
     */
    public function getWpContent(): string
    {
        return $this->wpContent;
    }

    /**
     * @param string $wpContent
     * @return Page
     */
    public function setWpContent(string $wpContent): Page
    {
        $this->wpContent = $wpContent;
        return $this;
    }
}