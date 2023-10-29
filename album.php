<?php
function __autoload($class) {
	$parts = explode('\\', $class);
	$filename = implode( '/', $parts ) . '.php';
	if( stream_resolve_include_path( $filename ) ) {
		require_once $filename;
		return;
	} 
	if( stripos($class, 'Zend') !== false && count( $parts ) >= 3 ) {
		$filename = strtolower( $parts[ 0 ] . '-' . $parts[ 1 ] ) . DIRECTORY_SEPARATOR . 'src';
		for( $i = 2; $i < count( $parts ); $i++ ) {
			$filename .= DIRECTORY_SEPARATOR . $parts[ $i ];
		}
		$filename .= '.php';
    		if( stream_resolve_include_path( $filename ) ) {
			require_once $filename;
			return;
		}
	}
	echo "Unable to find " . $class;
}

$paths = array(
	dirname(__FILE__) . '/vendor/zendframework',
	dirname(__FILE__) . '/vendor'
);

foreach( $paths as $path ) {
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}

require_once('vendor/james-heinrich/getid3/getid3/getid3.php');
require_once('vendor/james-heinrich/getid3/getid3/write.php');

use SustainablePace\Allmusic\AmParser as AmParser;
use SustainablePace\Allmusic\AmAlbum as AmAlbum;

class AlbumConverter {
	protected function exec( $cmd ) {
		ob_start();
		exec( $cmd . ' 1>/dev/null 2>&1');
		$log = ob_get_contents();
		ob_end_clean();
	}

	public function deleteWav() {
		$wav = glob("*.wav");
		if( !empty( $wav ) ) {
			echo "Deleting all WAV files...\n";
			$this->exec("rm *.wav");
		}
	}

	public function decodeFlac( $limit = null ) {
		$flac = glob("*.flac");
		sort( $flac );
		$flacLimit = empty( $limit ) ? count( $flac ) : min(count( $flac ), $limit);
		echo "Decoding FLAC files...\n";
		for( $i = 0; $i < $flacLimit; $i++ ) {
			$this->exec("flac -d " . escapeshellarg( $flac[ $i ] ) );
		}
		$wav = glob("*.wav");
		if($flacLimit != count($wav)) {
			exit("FLAC decode error! Aborting.\n");
		}
	}

	public function joinWav( $limit = null ) {
		$wav = glob("*.wav");
		sort( $wav );
		$limit = empty( $limit ) ? count( $wav ) : min(count( $wav ), $limit);
		for( $i = 0; $i < $limit; $i++ ) {
			$wav[ $i ] = escapeshellarg( $wav[ $i ] );
		}
		$file = 'out.wav';
		echo "Joining WAV files...\n";
		$this->exec("sox " . implode( " ", $wav ) . " " . $file);
		return $file;
	}

	public function encodeMp3( $filename, AmAlbum $album ) {
		echo "Encoding WAV file...\n";
		$out = '../' . $album->getYear() . ' ' . $album->getArtist()->getName() . ' - ' . $album->getName() . '.mp3';
		$this->exec("lame -V0 -h " . $filename . ' ' . escapeshellarg( $out ) );
		return $out;
	}

	public function writeTags( $mp3, AmAlbum $album ) {
		echo "Writing tags...\n";
		$getID3 = new getID3;
		$getID3->setOption(array('encoding'=>'UTF8'));
		$tagwriter = new getid3_writetags;
		$tagwriter->filename = $mp3;
		$tagwriter->tagformats = array('id3v2.3');
		$tagwriter->overwrite_tags    = true; 
		$tagwriter->tag_encoding      = 'UTF-8';
		$tagwriter->remove_other_tags = true;
		$TagData = array(
			'title'         => array($album->getName()),
			'artist'        => array($album->getArtist()->getName()),
			'album'         => array($album->getName()),
			'year'          => array($album->getYear()),
			'comment'       => array($album->getDescription()),
			'track'         => array('01/01')
		);
		$this->exec( 'wget ' . escapeshellarg( $album->getCoverUrl() ) . ' -O __cover' );
		$image = '__cover';
		if ($fd = fopen( $image, 'rb')) {
			$APICdata = fread($fd, filesize($image));
			fclose ($fd);
			list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($image);
			$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
			if (isset($imagetypes[$APIC_imageTypeID])) {
				$TagData['attached_picture'] = array(
					array(
						'data' => $APICdata,
						'description' => $album->getArtist()->getName() . ' - ' . $album->getName() . ' (' . $album->getYear() . ')',
						'mime' => 'image/' . $imagetypes[$APIC_imageTypeID],
						'picturetypeid' => 3
					)
				);
			} else {
				echo "Invalid image format " . $APIC_imageTypeID . " (only GIF, JPEG, PNG)\n";
			}
			$this->exec( 'rm -rf ' . $image );
		} else {
			echo "Cannot open ".$image. "\n";
		}

		// write tags
		$tagwriter->tag_data = $TagData;
		if ($tagwriter->WriteTags()) {
			echo "Successfully wrote tags\n";
			if (!empty($tagwriter->warnings)) {
				echo "There were some warnings:\n".implode("\n\n", $tagwriter->warnings);
			}
		} else {
			echo "Failed to write tags!\n".implode("\n\n", $tagwriter->errors);
		}
	}
}


$limit = ( is_array($argv) && count( $argv ) >= 2 ) ? intval($argv[1]) : null;

$parser = new AmParser();

#$foldername = end(explode( DIRECTORY_SEPARATOR, getcwd()));
#$album = $parser->searchAlbum( 'Spiral Staircase', 'ralph mctell');

$id = readline("Allmusic album id: ");
$album = $parser->getAlbum( $id );
if( !$album->getArtist()->getName() ) {
	$album->getArtist()->setName( readline("Artist name: ") );
}
if( !$album->getName() ) {
	$album->setName( readline("Album name: ") );
}
if( !$album->getYear() ) {
	$album->setYear( (int)readline("Album year: ") );
}

#if( !$album->getDescription() ) {
#	$album->setDescription( readline("Album description: ") );
#}
if( !$album->getCoverUrl() ) {
	$album->setCoverUrl( readline("Album cover url: ") );
}

$conv = new AlbumConverter();
$conv->deleteWav();
$conv->decodeFlac( $limit );
$filename = $conv->joinWav( $limit );
$mp3 = $conv->encodeMp3( $filename, $album );
$conv->deleteWav();
$conv->writeTags( $mp3, $album );


