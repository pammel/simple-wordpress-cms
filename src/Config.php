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
    private $cssUrlPath;

    /**
     * @var string
     */
    private $cssFilePath;

    /**
     * @var string
     */
    private $cssMergeFolder = 'wp-merged';

    /**
     * @var int
     */
    private $cssMergeFolderPermissions = 0775;

    /**
     * @var array
     */
    private $cssStringReplace = [':root' => ''];

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
    public function getCssUrlPath(): string
    {
        return $this->cssUrlPath;
    }

    /**
     * @param string $cssUrlPath
     * @return Config
     */
    public function setCssUrlPath(string $cssUrlPath): Config
    {
        $this->cssUrlPath = $cssUrlPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssFilePath(): string
    {
        return $this->cssFilePath;
    }

    /**
     * @param string $cssFilePath
     * @return Config
     */
    public function setCssFilePath(string $cssFilePath): Config
    {
        $this->cssFilePath = $cssFilePath;
        return $this;
    }

    /**
     * default = 'wp-merged'
     *
     * @return string
     */
    public function getCssMergeFolder(): string
    {
        return $this->cssMergeFolder;
    }

    /**
     * @param string $cssMergeFolder
     * @return Config
     */
    public function setCssMergeFolder(string $cssMergeFolder): Config
    {
        $this->cssMergeFolder = $cssMergeFolder;
        return $this;
    }

    /**
     * default = 0775
     *
     * @return int
     */
    public function getCssMergeFolderPermissions(): int
    {
        return $this->cssMergeFolderPermissions;
    }

    /**
     * @param int $cssMergeFolderPermissions
     * @return Config
     */
    public function setCssMergeFolderPermissions(int $cssMergeFolderPermissions): Config
    {
        $this->cssMergeFolderPermissions = $cssMergeFolderPermissions;
        return $this;
    }

    /**
     * default = [':root' => ''] because :root don't work in shadowRoot
     *
     * @return array
     */
    public function getCssStringReplace(): array
    {
        return $this->cssStringReplace;
    }

    /**
     * @param array $cssStringReplace
     * @return Config
     */
    public function setCssStringReplace(array $cssStringReplace): Config
    {
        $this->cssStringReplace = $cssStringReplace;
        return $this;
    }


}