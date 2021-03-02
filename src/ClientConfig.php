<?php

namespace pammel\SimpleWordpressCms;

class ClientConfig
{
    /**
     * @var string
     */
    private $blogUrl;

    /**
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @param string $blogUrl
     * @return ClientConfig
     */
    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
        return $this;
    }

}