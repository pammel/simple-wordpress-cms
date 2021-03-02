<?php

namespace pammel\SimpleWordpressCms;

class Page
{
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

    /**
     * @return string
     */
    public function getCssFileMerged(): string
    {
        $this->cssFileMerged = md5(serialize($this->getCssFiles())) . '.css';

        if(!file_exists($this->cssFileMerged)) {
            $cssMerged = '';
            foreach($this->getCssFiles() AS $file){
                $css = file_get_contents($file);
                $css = str_replace(':root', '', $css);

                $cssMerged .=
                   "/* " . str_repeat('-', 80)  . " */ \n" .
                   "/* " . $file . "*/ \n" .
                   "/* " . str_repeat('-', 80)  . " */ \n" .
                   $css . "\n\n";
            }

            file_put_contents($this->cssFileMerged, trim($cssMerged));
        }

        return 'css/wp/' . $this->cssFileMerged;
    }

    /**
     * @return array
     */
    public function getCssFiles(): array
    {
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

    public function addCssFile(string $cssFile): Page
    {
        $this->cssFiles[] = $cssFile;
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