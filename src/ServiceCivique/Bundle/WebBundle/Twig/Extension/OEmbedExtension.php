<?php

namespace ServiceCivique\Bundle\WebBundle\Twig\Extension;

use Symfony\Component\Filesystem\Filesystem;

class OEmbedExtension extends \Twig_Extension
{
    protected $essence;
    protected $webDir;
    protected $relativeImageDirPath;
    protected $fs;
    protected $loaded = false;

    /**
     * __construct
     *
     * @param $webDir
     * @param $relativeImageDirPath
     */
    public function __construct($webDir, $relativeImageDirPath)
    {
        $this->webDir               = $webDir;
        $this->relativeImageDirPath = $relativeImageDirPath;
    }

    public function load()
    {
        if ($this->loaded) {
            return;
        }

        $this->essence = \Essence\Essence::instance();
        $this->fs = new Filesystem();

        $imageDir = sprintf('%s/%s', $this->webDir, $this->relativeImageDirPath);

        if (!$this->fs->exists($imageDir)) {
            $this->fs->mkdir($imageDir);
        }

        $this->loaded = true;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'oembed' => new \Twig_Function_Method($this, 'getEmbedObject'),
        );
    }

    /**
     * getEmbedObject
     *
     */
    public function getEmbedObject($url)
    {
        $this->load();

        $result = $this->essence->embed($url, [
            'maxwidth' => 577,
            'maxheight' => 400
        ]);

        if (!$result) {
            return null;
        }

        $media = clone $result;

        // generate preview image filename
        $localThumbnailFilename = $this->generateLocalThumbnailFilename($media->thumbnailUrl);
        $localThumbnailFilepath = sprintf('%s/%s/%s', $this->webDir, $this->relativeImageDirPath, $localThumbnailFilename);

        if (!$this->fs->exists($localThumbnailFilepath)) {
            $this->downloadFile($media->thumbnailUrl, $localThumbnailFilepath);
        }

        $media->thumbnailUrl = sprintf('/%s/%s', $this->relativeImageDirPath, $localThumbnailFilename);

        return $media;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'service_civique_oembed';
    }

    protected function generateLocalThumbnailFilename($previewImageUrl)
    {
        return sprintf('%s.jpg', md5($previewImageUrl));
    }

    protected function downloadFile($url, $destination)
    {
        return file_put_contents($destination, fopen($url, 'r'));
    }

}
