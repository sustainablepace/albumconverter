<?php
namespace SustainablePace\Allmusic;

interface Getter {
    /**
     * Returns the content of the resource.
     *
     * @param string $url
     * @return string
     * @throws UnexpectedValueException $e If the resource returns an unexpected result.
     */
    public function getContent( $url );

    /**
     * Sets the content of the resource.
     *
     * @param $content
     */
    public function setContent( $content );

}
