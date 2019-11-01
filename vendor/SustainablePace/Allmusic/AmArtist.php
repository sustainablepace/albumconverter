<?php
namespace SustainablePace\Allmusic;

use UnexpectedValueException;
use Zend\Stdlib\ArraySerializableInterface;

class AmArtist implements Artist, ArraySerializableInterface {

    const ID_REGEX = '/^mn[[0-9]{10}$/i';
    protected $id;
    protected $name;
    protected $urlName;

    /**
     * Gets the artist id.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the artist id.
     *
     * @param $id
     * @throws UnexpectedValueException
     */
    public function setId( $id ) {
        if( !static::isValidId( $id ) ) {
            throw new UnexpectedValueException( 'Invalid artist id.' );
        }
        $this->id = strtolower( $id );
    }

    /**
     * Returns the artist name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the artist name.
     *
     * @param $name
     */
    public function setName( $name ) {
        $this->name = $name;
    }

    /**
     * Returns the artist URL name.
     *
     * @return string
     */
    public function getUrlName() {
        return $this->urlName;
    }

    /**
     * Sets the artist URL name.
     *
     * @param $name
     */
    public function setUrlName( $name ) {
        $this->urlName = $name;
    }

    /**
     * Prüft ob eine ID gültig ist.
     *
     * @param $id
     * @return bool
     */
    public static function isValidId( $id ) {
        return (bool)preg_match( static::ID_REGEX, $id );
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array) {
        if( !is_array( $array ) ) {
            return;
        }
        if( array_key_exists( 'id', $array ) ) {
            $this->setId( $array[ 'id' ] );
        }
        if( array_key_exists( 'name', $array ) ) {
            $this->setName( $array[ 'name' ] );
        }
        if( array_key_exists( 'url_name', $array ) ) {
            $this->setUrlName( $array[ 'url_name' ] );
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy() {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'url_name' => $this->getUrlName()
        );
    }

}
