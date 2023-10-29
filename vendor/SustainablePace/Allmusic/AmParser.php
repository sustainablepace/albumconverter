<?php
namespace SustainablePace\Allmusic;

use SustainablePace\Allmusic\HttpGetter as HttpGetter;
use SustainablePace\Allmusic\AmAlbum as AmAlbum;
use SustainablePace\Allmusic\AmArtist as AmArtist;
use Zend\Dom\Query;
use UnexpectedValueException;

class AmParser {

    const URL = 'http://www.allmusic.com';
    const URL_SEARCH_ALBUM = '/search/typeahead/album';
    const URL_ALBUM = '/album';

    /**
     * Getter for content.
     *
     * @var Getter
     */
    protected $getter;

    /**
     * Constructor, sets default HttpGetter.
     */
    public function __construct() {
        $this->setGetter( new HttpGetter() );
    }

    /**
     * Sets the Getter.
     *
     * @param Getter $getter
     */
    public function setGetter( Getter $getter ) {
        $this->getter = $getter;
    }

    /**
     * Returns an album by album name and artist name.
     *
     * @param $albumName
     * @param $artistName
     * @return RymAlbum
     */
    public function searchAlbum( $albumName, $artistName ) {
        $content = $this->getter->getContent( $this->getSearchAlbumUrl( $albumName ) );
        $dom = new Query( $content );
        $rows = $dom->execute( 'li.result' );
        $album = new AmAlbum();
        foreach( $rows as $row ) {
            $artistDom = new Query( utf8_decode( $row->ownerDocument->saveHTML( $row ) ) );
            $artistNodes = $artistDom->execute( 'p.artist' );
            if( count( $artistNodes ) !== 1 ) {
                throw new UnexpectedValueException( 'No artist node in response.' );
            }
            $node = $artistNodes[ 0 ];
            $artist = trim( $node->textContent );
            if( strtolower( $artist ) !== strtolower( $artistName ) ) {
                continue;
            }
            $id = $row->getAttribute( 'data-id' );
            $url = $row->getAttribute( 'data-url' );
            $name = $row->getAttribute( 'data-text' );
            $album->setId( $id );
            $album->setUrlName( $url );
            $album->setName( $name );
        }

        //query album page

        return $album;
    }

    /**
     * @param $albumUrlName
     * @return AmAlbum
     */
    public function getAlbum( $albumUrlName ) {
        $albumId = static::getIdFromUrlName( $albumUrlName );

        $album = new AmAlbum();
        $album->setUrlName( $albumUrlName );
        $album->setId( $albumId );

        $content = $this->getter->getContent( $this->getAlbumUrl( $albumId ) );
        $dom = new Query( $content );
        $artistNodes = $dom->execute( 'h2#albumArtists a' );
        $artist = new AmArtist();
        if( empty( $artistNodes ) ) {
                $artistNodes = $dom->execute( 'h2.album-artist span' );
        }
        if( count( $artistNodes ) >= 1 ) {
            $node = $artistNodes[ 0 ];
            $urlname = trim( $node->getAttribute( 'href' ) );
            $name = trim( $node->textContent );
            $id = static::getIdFromUrlName( $urlname );
            $artist->setId( $id );
            $artist->setName( $name );
            $components = explode( '/', $urlname );
            $artist->setUrlName( array_pop( $components ) );
        }
	    $album->setArtist( $artist );

        $yearNodes = $dom->execute( 'div.release-date > span' );
        if( count( $yearNodes ) === 1 ) {
		$node = $yearNodes[ 0 ];
		$year = substr( trim( $node->textContent ), -4 );
		if( preg_match( '/[0-9]{4}/', $year ) ) {
			$album->setYear( (int)$year );
		}
        }

        $nameNodes = $dom->execute( 'h1#albumTitle' );
        if( count( $nameNodes ) === 1 ) {
            $nameNode = $nameNodes[ 0 ];
            $name = trim( $nameNode->textContent );
            $album->setName( $name );
        }
 
        $descriptionNodes = $dom->execute( '#review > p' ); //doesn't work any longer - maybe deferred loading...
        if( count( $descriptionNodes ) >= 1 ) {
            $descriptionNode = $descriptionNodes[ 0 ];
            $description = trim( $descriptionNode->textContent );
            $album->setDescription( $description );
        }
 
        $coverNodes = $dom->execute( 'img#posterImage' );
        if( count( $coverNodes ) >= 1 ) {
            $coverNode = $coverNodes[ 0 ];
            $coverUrl = trim( $coverNode->getAttribute( 'src' ) );
            $album->setCoverUrl( $coverUrl );
        }

	    return $album;
    }

    /**
     * @param $year
     * @param int $page
     * @return string
     * @throws UnexpectedValueException
     */
    public function getSearchAlbumUrl( $albumName ) {
        if( mb_strlen( $albumName ) < 2 ) {
            throw new UnexpectedValueException( 'Album name too short.' );
        }
        return static::URL . static::URL_SEARCH_ALBUM . '/' . urlencode( $albumName );
    }

    /**
     * @param $urlName
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getAlbumUrl( $urlName ) {
        $albumId = static::getIdFromUrlName( $urlName );
        if( !AmAlbum::isValidId( $albumId ) ) {
            throw new UnexpectedValueException( 'Invalid album id.' );
        }
        return static::URL . static::URL_ALBUM . '/' . $urlName;
    }

    /**
     * @param $urlname
     * @return string
     */
    public static function getIdFromUrlName( $urlname ) {
        return substr( $urlname, -12 );
    }
}
