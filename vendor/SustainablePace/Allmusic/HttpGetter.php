<?php
namespace SustainablePace\Allmusic;

use Zend\Http\Client as Client;
use Zend\Http\Client\Adapter\Exception\TimeoutException as TimeoutException;
use Zend\Http\Response as Response;

class HttpGetter implements Getter {

    const RETRIES = 5;

    /**
     * Returns the content of the resource.
     *
     * @param string $url
     * @return string
     * @throws UnexpectedValueException $e If the resource returns an unexpected result.
     */
    public function getContent( $url, $count = 0 ) {
        $response = '';
        try {
            $client = new Client();
            $client->setUri( $url );
            $response = $client->send()->getBody();
        } catch( TimeoutException $e ) {
            if( $count < self::RETRIES ) {
                $response = $this->getContent( $url, $count + 1 );
            }
        }
        return $response;
    }

    /**
     * Sets the content of the resource.
     *
     * @param $content
     */
    public function setContent( $content ) {
    }

}
