<?php

namespace SustainablePace\Allmusic;

interface Album {
    /**
     * Returns the album artist.
     *
     * @return Artist
     */
    public function getArtist();

    /**
     * Sets the album artist.
     *
     * @param Artist $artist
     */
    public function setArtist( Artist $artist );

	/**
	 * Returns the album genres.
	 *
	 * @return array(Genre)
	 */
	public function getGenres();

	/**
	 * Sets the album genres.
	 *
	 * @param array(Genre) $genres
	 * @throws UnexpectedValueException If the param is not an array of genres.
	 */
	public function setGenres( $genres );

    /**
     * Returns the album name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the album name.
     *
     * @param $name
     */
    public function setName( $name );

    /**
     * Returns the album year.
     *
     * @return integer
     */
    public function getYear();

    /**
     * Sets the album year.
     *
     * @param $year
     */
    public function setYear( $year );

    /**
     * Gets the album id.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the album id.
     *
     * @param $id
     * @throws UnexpectedValueException
     */
    public function setId( $id );

    /**
     * Returns the album URL name.
     *
     * @return string
     */
    public function getUrlName();

    /**
     * Sets the album URL name.
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
