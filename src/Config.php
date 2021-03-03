<?php

namespace pammel\SimpleWordpressCms;

class Config
{
    /**
     * @var string
     */
    private $blogUrl;

    /**
     * @var array
     */
    private $cssFilesAdditional = [];

    /**
     * @var string
     */
    private $cssMergedFilename = 'simple-wordpress-cms.css';

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
    private $cssReplace = [':root' => ''];

    /**
     * @var array
     */
    private $contentReplace = [];

    /**
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @param string $blogUrl
     * @return Config
     */
    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
        return $this;
    }

    /**
     * @return array
     */
    public function getCssFilesAdditional(): array
    {
        return $this->cssFilesAdditional;
    }

    /**
     * @param array $cssFilesAdditional
     * @return Config
     */
    public function setCssFilesAdditional(array $cssFilesAdditional): Config
    {
        $this->cssFilesAdditional = $cssFilesAdditional;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssMergedFilename(): string
    {
        return $this->cssMergedFilename;
    }

    /**
     * @param string $cssMergedFilename
     * @return Config
     */
    public function setCssMergedFilename(string $cssMergedFilename): Config
    {
        $this->cssMergedFilename = $cssMergedFilename;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssFolderLocal(): string
    {
        return $this->cssFolderLocal;
    }

    /**
     * @param string $cssFolderLocal
     * @return Config
     */
    public function setCssFolderLocal(string $cssFolderLocal): Config
    {
        $this->cssFolderLocal = $cssFolderLocal;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssFolderPublicUrl(): string
    {
        return $this->cssFolderPublicUrl;
    }

    /**
     * @param string $cssFolderPublicUrl
     * @return Config
     */
    public function setCssFolderPublicUrl(string $cssFolderPublicUrl): Config
    {
        $this->cssFolderPublicUrl = $cssFolderPublicUrl;
        return $this;
    }

    /**
     * default = [':root' => ''] because :root don't work in shadowRoot
     *
     * @return array
     */
    public function getCssReplace(): array
    {
        return $this->cssReplace;
    }

    /**
     * @param array $cssReplace
     * @return Config
     */
    public function setCssReplace(array $cssReplace): Config
    {
        $this->cssReplace = $cssReplace;
        return $this;
    }

    /**
     * @return array
     */
    public function getContentReplace(): array
    {
        return $this->contentReplace;
    }

    /**
     * @param array $contentReplace
     * @return Config
     */
    public function setContentReplace(array $contentReplace): Config
    {
        $this->contentReplace = $contentReplace;
        return $this;
    }
}