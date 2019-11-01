<?php
namespace SustainablePace\Allmusic;

interface Artist {
    /**
     * Gets the artist id.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the artist id.
     *
     * @param $id
     * @throws UnexpectedValueException
     */
    public function setId( $id );

    /**
     * Returns the artist name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the artist name.
     *
     * @param $name
     */
    public function setName( $name );

    /**
     * Returns the artist URL name.
     *
     * @return string
     */
    public function getUrlName();

    /**
     * Sets the artist URL name.
     *
     * @param $name
     */
    public function setUrlName( $name );

	/**
	 * Prüft ob eine ID gültig ist.
	 *
	 * @param $id
	 * @return bool
	 */
	public static function isValidId( $id );

}
