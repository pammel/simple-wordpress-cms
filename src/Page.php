<?php

namespace pammel\SimpleWordpressCms;

class Page
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $cssFiles = [];

    /**
     * @var string
     */
    private $cssFileMerged;

    /**
     * @var string
     */
    private $body;

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
            <div class="wordpress-container" style="margin-top: -1.5rem">
                <div id="wpShadow"></div>
                <div id="wpShadowContent">
                    '.$this->getBody().'
                </div>
            </div>
        
            <script>
                let shadowContent = document.querySelector("#wpShadowContent");
        
                let shadow = document.querySelector("#wpShadow");
                shadow.attachShadow({mode: "open", delegatesFocus: false});
                shadow.shadowRoot.append(shadowContent);
        
                var el = document.createElement("link");
                el.setAttribute("rel", "stylesheet");
                el.setAttribute("type", "text/css");
                el.setAttribute("href", "' . $this->getCssFileMerged() . '");
                shadow.shadowRoot.append(el);
            </script>
        ';
    }

    /**
     * return URL of merged CSS file
     *
     * @return string
     */
    public function getCssFileMerged(): string
    {
        if($this->cssFileMerged === null){
            $mergeFolder = $this->config->getCssFilePath() . '/' . $this->config->getCssMergeFolder();
            $fileName = md5(serialize($this->getCssFiles())) . '.css';
            $filePath = $mergeFolder . '/' . $fileName;
            $this->cssFileMerged = $this->config->getCssUrlPath() . '/' . $this->config->getCssMergeFolder() . '/' . $fileName;

            if(!file_exists($filePath)) {

                # make dir
                if (!is_dir($mergeFolder)) {
                    mkdir($mergeFolder, $this->config->getCssMergeFolderPermissions(), true);
                }

                # Clean up old files
                $files = glob($mergeFolder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                # merge css files
                $cssMerged = '';
                foreach ($this->getCssFiles() as $file) {
                    $css = file_get_contents($file);
                    if ($strReplace = $this->config->getCssStringReplace()) {
                        $css = str_replace(array_keys($strReplace), array_values($strReplace), $css);
                    }

                    $cssMerged .=
                       "/* " . str_repeat('-', 80) . " */ \n" .
                       "/* " . $file . "*/ \n" .
                       "/* " . str_repeat('-', 80) . " */ \n" .
                       $css . "\n\n";
                }
                file_put_contents($filePath, trim($cssMerged));
            }
        }

        return $this->cssFileMerged;
    }

    /**
     * @param bool $includeAdditional
     * @return array
     */
    public function getCssFiles($includeAdditional=true): array
    {
        if($includeAdditional){
            return array_merge($this->cssFiles, $this->config->getCssFilesAdditional());
        }

        return $this->cssFiles;
    }

    /**
     * @param array $cssFiles
     * @return Page
     */
    public function setCssFiles(array $cssFiles): Page
    {
        $this->cssFiles = $cssFiles;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Page
     */
    public function setBody(string $body): Page
    {
        $this->body = $body;
        return $this;
    }
}