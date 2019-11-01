<?php
namespace SustainablePace\Allmusic;

use UnexpectedValueException;
use Zend\Stdlib\ArraySerializableInterface;

class AmAlbum implements Album, ArraySerializableInterface {
    const ID_REGEX = '/^mw[0-9]{10}$/i';
    /**
     * Album artist.
     *
     * @var Artist $artist
     */
    protected $artist;
    protected $name;
    protected $year;
    protected $id;
    protected $urlName;
    protected $description;
    protected $coverUrl;
    protected $genres = array();

    /**
     * Returns the album description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Sets the album description.
     *
     * @param string $description
     */
    public function setDescription( $description ) {
        $this->description = $description;
    }

    /**
     * Returns the album cover url.
     *
     * @return string
     */
    public function getCoverUrl() {
        return $this->coverUrl;
    }

    /**
     * Sets the album cover url.
     *
     * @param string $url
     */
    public function setCoverUrl( $url ) {
        $this->coverUrl = $url;
    }

    /**
     * Returns the album artist.
     *
     * @return Artist
     */
    public function getArtist() {
        return $this->artist;
    }

    /**
     * Sets the album artist.
     *
     * @param Artist $artist
     */
    public function setArtist( Artist $artist ) {
        $this->artist = $artist;
    }

	/**
	 * Returns the album genres.
	 *
	 * @return array(Genre)
	 */
	public function getGenres() {
		return $this->genres;
	}

	/**
	 * Sets the album genres.
	 *
	 * @param array(Genre) $genres
	 * @throws UnexpectedValueException If the param is not an array of genres.
	 */
	public function setGenres( $genres ) {
		if( !is_array( $genres ) ) {
			throw new UnexpectedValueException( 'Not an array. setGenre expects an array of Genre objects.' );
		}
		foreach( $genres as $genre ) {
			if( !($genre instanceof Genre) ) {
				throw new UnexpectedValueException( 'Not a genre. setGenre expects an array of Genre objects.' );
			}
		}
		$this->genres = $genres;
	}

	/**
     * Returns the album name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the album name.
     *
     * @param $name
     */
    public function setName( $name ) {
        $this->name = $name;
    }

    /**
     * Returns the album year.
     *
     * @return integer
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Sets the album year.
     *
     * @param $year
     */
    public function setYear( $year ) {
        $this->year = $year;
    }

    /**
     * Gets the album id.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the album id.
     *
     * @param $id
     * @throws UnexpectedValueException
     */
    public function setId( $id ) {
        if( !static::isValidId( $id ) ) {
            throw new UnexpectedValueException();
        }
        $this->id = strtolower( $id );
    }

    /**
     * Returns the album URL name.
     *
     * @return string
     */
    public function getUrlName() {
        return $this->urlName;
    }

    /**
     * Sets the album URL name.
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
        if( array_key_exists( 'year', $array ) ) {
            $this->setYear( $array[ 'year' ] );
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy() {
        $artistId = null;
        if( $this->getArtist() instanceof Artist ) {
            $artistId = $this->getArtist()->getId();
        }
        return array(
            'id' => $this->getId(),
            'artist_id' => $artistId,
            'name' => $this->getName(),
            'url_name' => $this->getUrlName(),
            'year' => $this->getYear()
        );
    }

}
