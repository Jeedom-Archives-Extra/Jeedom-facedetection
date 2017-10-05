<?php
/***************************************************************
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker:
 *
 *
 * PHP versions 5
 *
 * --LICENSE NOTICE--
 * This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * --LICENSE NOTICE--
 ***************************************************************
 * $File: snapshot.class.php $
 * $Author: mschulz $ - $Date: 2009-06-08 23:35:27 +0100 (Mo, 08 Juni 2009) $
 * $HeadURL: http://tbox.markoschulz.intern/php-001/class/snapshot.class.php $ - $Revision: 6 $
 * $Id: snapshot.class.php 6 2009-06-08 23:35:27Z mschulz $
 * $Description: Class to extract an image from a video file. $
 * $Copyright: (c) 2009 Marko Schulz $
 ***************************************************************/

/**
 * {{{ @example
 *
 * <code>
 *
 * // Define class settings
 * $cfg['snapshot'] = array(
 *  	'program' => '/usr/bin/mplayer',
 *  	'outdir' => '/tmp',
 *  	'progressive' => False,
 *  	'baseline' => True,
 *  	'optimize' => 100,
 *  	'smooth' => 0,
 *  	'quality' => 90,
 *  	'frames' => 19,
 *  	'delete' => False
 * );
 * 
 * // Path to the input file
 * (string) $file = "argonnerwald.flv";
 * 
 * // Load the Snapshot class
 * include_once( 'snapshot.class.php' );
 * 
 * try {
 * 
 *  	// Create the $img object
 *  	(object) $img = new Snapshot( $cfg['snapshot'] );
 * 
 *  	// Extract a jpeg image from defined video file
 *  	if ( $img->extract( $file ) ) {
 *  		if ( is_file($img->getOutfile()) ) {
 *  			// If you have set the parameter delete to Fale, you have
 *  			// to delete the extracted file manually with the remove() method.
 *  			echo $file." was successfuly extracted to <a href=\"file://".$img->getOutfile()."\">".$img->getOutfile()."</a><br/>\n";
 *  		} else {
 *  			echo "Sorry, but I can't find any output file!";
 *  		}
 *  	} else {
 *  		echo "Can't extract an image from the movie [".$file."]!";
 *  	}
 * 
 *  	// You also can stream the image (send image header)
 *  	// if you set the second parameter to True
 *  	//if ( !$img->extract( $file, True ) ) {
 *  	//	echo "Can't extract ".$file;
 *  	//}
 * 
 *  	// You can remove the outfile manullay if $delete is False
 *  	//if ( $cfg['snapshot']['delete'] === False ) {
 *  	//	if ( !$img->remove($img->getOutfile()) )
 *  	//		die( "Can't delete the output file [".$img->getOutfile()."]!" );
 *  	//}
 * 
 * } catch ( Exception $error ) {
 *  	echo "Error: ".$error->getMessage()."; on line ".$error->getLine()." in ".$error->getFile()."\n";
 * }
 *
 * </code>
 *
 * }}}
 */



/**
 * The class provides methods to extract an image from a video file.
 *
 * @category   Video
 * @author     Marko Schulz
 * @copyright  2009 tuxnet24.de
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    File: $Revision: 6 $
 */

class Snapshot {


	/**
	 * Specify the directory to save the JPEG files.
	 * 
	 * @var string
	 * @access protected
	 * @see $this->outdir
	 */
	protected $outdir = "/tmp";


	/**
	 * The image quality factor <0-100>.
	 * 
	 * @var integer
	 * @access protected
	 * @see $this->quality
	 */
	protected $quality = 75;


	/**
	 * The image smooth factor <0-100>.
	 * 
	 * @var integer
	 * @access protected
	 * @see $this->smooth
	 */
	protected $smooth = 0;


	/**
	 * The image optimization factor <0-100>.
	 * 
	 * @var integer
	 * @access protected
	 * @see $this->optimize
	 */
	protected $optimize = 100;


	/**
	 * Specify use of baseline or not.
	 * 
	 * @var bool
	 * @access protected
	 * @see $this->baseline
	 */
	protected $baseline = True;


	/**
	 * Specify standard or progressive JPEG.
	 * 
	 * @var bool
	 * @access protected
	 * @see $this->progressive
	 */
	protected $progressive = False;


	/**
	 * Path to the mplayer binary
	 *
	 * @var string
	 * @access protected
	 * @see $this->program
	 */
	protected $program = "/usr/bin/mplayer";


	/**
	 * Should the converted file deleted
	 *
	 * @var bool
	 * @access protected
	 * @see $this->delete
	 */
	protected $delete = True;


	/**
	 * The video output driver
	 *
	 * @var string
	 * @access protected
	 * @see $this->driver
	 */
	protected $driver = "jpeg";


	/**
	 * The desired frame where an image should be created >=1
	 *
	 * @var integer
	 * @access protected
	 * @see $this->frames
	 */
	protected $frames = 1;


	/**
	 * All exeption messages
	 *
	 * @var array
	 * @static
	 * @access protected
	 * @see self::$ERROR[]
	 */
	protected static $ERROR = array(
		'NOFILE' => "No input file defined!",
		'NODELETE' => "Can't remove temporary file [%s]",
		'NOREMOVE' => "Can't remove the output file [%s]!"
	);


	/**
	 * This is the class constructor
	 * 
	 * @access public
	 * @param array $args
	 * @return void
	 * @use $this->__construct()
	 */
	public function __construct ( array $args = array() ) {


		if ( is_array($args) ) {
			foreach ( $args as $keys => $value )
				$this->setVar( $keys, $value );
		}

	}


	/**
	 * This is the class destructor
	 * 
	 * @access public
	 * @return void
	 * @use $this->__destruct()
	 */
	public function __destruct () {

		if ( $this->delete && isset($this->outfile) ) {
			if ( !$this->remove( $this->outfile ) ) // Remove extracted file
				throw new Exception( $this->message( 'NOREMOVE', $this->outfile ) );
		}

	}


	/**
	 * This method return the value of the defined variable
	 *
	 * @access public
	 * @param string $keys
	 * @return mixed
	 * @use $this->getVar()
	 */
	public function getVar( $keys ) {

		if ( isset($this->$keys) )
			return $this->$keys;

	}


	/**
	 * This method set the value of the defined variable
	 *
	 * @access public
	 * @param string $keys
	 * @param string $value
	 * @return void
	 * @use $this->setVar()
	 */
	public function setVar ( $keys, $value ) {

		if ( isset($keys) ) {
			switch ($keys) {
				case "outdir":
					if ( self::isDir( $value, True ) === True )
						$this->$keys = $value;
				break;
				case "program":
					if ( self::isBin( $value ) === True )
						$this->$keys = $value;
				break;
				case ( $keys == "quality" || $keys == "smooth" || $keys == "optimize" ):
					if ( self::in_range( $value ) === True )
						$this->$keys = $value;
				break;
				case ( $keys == "baseline" || $keys == "progressive" || $keys == "delete" ):
					if ( is_bool($value) )
						$this->$keys = $value;
				break;
				case "frames":
					if ( self::isInt($value) && $value > 0 )
						$this->$keys = $value;
				break;
			}
		}

	}


	/**
	 * This method call the conversion
	 *
	 * @access public
	 * @param string $input path to input file
	 * @param bool $stream true|false
	 * @return void
	 * @use $this->extract()
	 */
	public function extract ( $input, $stream = False ) {

		if ( !is_bool($stream) ) return False;

		if ( !isset( $input ) || !$this->isFile( $input ) )
			throw new Exception( $this->message( 'NOFILE', $input ) );
		else {
			if ( $this->execute( $input, $stream ) ) return True;
			else return False;
		}

	}


	/**
	 * This method download/stream the extracted file
	 *
	 * @access public
	 * @param string &$file path to extracted file
	 * @return void
	 * @use $this->stream()
	 */
	public function stream ( &$file ) {

		header( "Content-Description: File Transfer\r\n" );
		header( "Content-Type: image/jpeg\r\n" );
		header( "Content-Disposition: ".(!strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 5.5")?"inline; ":"")."filename=".basename( $file )."\r\n" );
		header( "Content-Length: ".filesize( $file )."\r\n" );
		header( "Content-Transfer-Encoding: Binary\n" );
		header( "Pragma: no-cache\r\n" );
		header( "Connection: Close\r\n\r\n" );
		readfile( $file );

	}


	/**
	 * This method return the name and path of the output file
	 *
	 * @access public
	 * @return string
	 * @use $this->getOutfile()
	 */
	public function getOutfile() {

		return self::getVar( 'outfile' );

	}


	/**
	 * This method exec the conversion
	 *
	 * @access protected
	 * @param string $infile path to input file
	 * @param bool $stream should be the output file send to HTTP header
	 * @return bool
	 * @use $this->execute()
	 */
	protected function execute ( &$infile, $stream ) {

		// Execute the mplayer to extract an image from video file
		@/**/exec( $this->program." -nosound "
								."-frames ".$this->frames
								." -vo ".$this->driver
								.":".($this->progressive ? 'progressive' : 'noprogressive')
								.":".($this->baseline ? 'baseline' : 'nobaseline')
								.":optimize=".$this->optimize
								.":smooth=".$this->smooth
								.":quality=".$this->quality
								.":outdir=".$this->outdir." ".$infile." >/dev/null 2>&1", $out = array(), $return );


		// Get a list of all created temporary output files.
		(array) $tmpfiles = self::getFiles( $this->outdir );

		// Get the output file, defined by the frame number.
		$this->outfile = self::setOutfile( $tmpfiles );

		if ( $return !== 0 || !self::isFile($this->outfile) ) return False;
		else {
			if ( $stream ) $this->stream( $this->outfile );
			return True;
		}

	}


	/**
	 * This method get the right output file and delete all others
	 *
	 * @access protected
	 * @param array $filelist list of all extracted files
	 * @return string
	 * @use $this->setOutfile()
	 */
	protected function setOutfile ( $filelist ) {

		// Loop the list of all temporary extracted files
		foreach ( $filelist as $file ) {
			// Get the extracted file with the right frame number
			if ( preg_match( '/^(.*0+'.$this->frames.'\.jpg)$/i', $file, $result ) ) {
				$img = $result[1];
			} else {
				// Delete all other temporary extracted files
				if ( !@/**/unlink($file) )
					throw new Exception( $this->message( 'NODELETE', $file ) );
			}
		}

		return $img;

	}


	/**
	 * This method get all extracted file as array
	 *
	 * @access protected
	 * @param string $dir path to output directory
	 * @return array
	 * @use $this->getFiles()
	 */
	protected function getFiles ( $dir ) {

		(array) $images = array();

		if ( is_dir($dir) ) {
			if ( $dh = opendir($dir) ) {
				while ( ($file = readdir($dh) ) !== false ) {
					if ( preg_match( '/^(0+[0-9]{1,}\.jpg)$/i', $file, $result ) ) {
						array_push( $images, $dir."/".$file );
					}
				}
				closedir($dh);
			}
		}

		return $images;

	}


	/**
	 * This method exec a directory check
	 *
	 * @access protected
	 * @param string &$file path to file
	 * @param bool $writable writable check (default: False)
	 * @return bool
	 * @use $this->isDir()
	 */
	protected function isDir( &$file,  $writable = False ) {

		// clear the status chache
		clearstatcache();

		if ( is_dir($file) && ( $writable ? is_writable($file) : is_readable($file) ) )
			return True;
		else
			return False;

	}


	/**
	 * This method exec a file check
	 *
	 * @access protected
	 * @param string &$file path to file
	 * @param bool $writable writable check (default: False)
	 * @return bool
	 * @use $this->isFile()
	 */
	protected function isFile( &$file,  $writable = False ) {

		// clear the status chache
		clearstatcache();

		if ( is_file($file) && ( $writable ? is_writable($file) : is_readable($file) ) )
			return True;
		else
			return False;

	}


	/**
	 * This method check if the defined file is executable
	 *
	 * @access protected
	 * @param string &$file path to file
	 * @return bool
	 * @use $this->isBin()
	 */
	protected function isBin( &$file ) {

		// clear the status chache
		clearstatcache();

		if ( is_file($file) && is_executable($file) )
			return True;
		else
			return False;

	}


	/**
	 * This method check if variable is an integer
	 *
	 * @access protected
	 * @param string &$number number to check
	 * @return bool
	 * @use $this->isInt()
	 */
	protected function isInt( &$number ) {

		return ((string) $number) === ((string)(int) $number);

	}


	/**
	 * This method check if variable is a definedd range
	 *
	 * @access protected
	 * @param integer &$var number to check
	 * @param integer $start begin of range
	 * @param integer $end end of range
	 * @return bool
	 * @use $this->in_range()
	 */
	protected function in_range( &$var, $start = 0, $end = 100 ) {

		if ( $end <= $start ) return False;
		if ( self::isInt( $var ) && in_array( $var, range( $start, $end ) ) )
			return True;
		else
			return False;

	}


	/**
	 * This method remove the defined file
	 *
	 * @access private
	 * @param string &$file path to file
	 * @return bool
	 * @use $this->remove()
	 */
	public function remove ( &$file ) {

		return @/**/unlink( $file );

	}


	/**
	 * Return the right exception message
	 *
	 * @access private
	 * @param string $err error keyword
	 * @param string $which to replaced string
	 * @return string
	 * @use $this->message()
	 */
	private function message( $err, $which = Null ) {

		// Replace the %s placeholder with $which in ERROR array.
		(string) $except = str_replace( '%s', $which, self::$ERROR[$err] );
		return $except;

	}


}

//***************************************************************
// EOF
?>