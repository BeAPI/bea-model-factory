<?php namespace BEA\Model_Factory\Models;

class File extends Model {
	/**
	 * @var int the element id
	 */
	public $ID;
	public $meta_data;

	protected $post_type = 'attachment';

	public function __get( $val ) {
		if ( isset( $this->wp_object->$val ) ) {
			return $this->wp_object->$val;
		}

		return null;
	}

	/**
	 * Get the excerpt
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_the_copyright() {
		return $this->wp_object->post_excerpt;
	}

	/**
	 * Check if has excerpt
	 * @return bool
	 * @author Maxime CULEA
	 */
	public function has_copyright() {
		$excerpt = $this->get_the_copyright();

		return ! empty( $excerpt );
	}

	/**
	 * Display the copyright stocked in excerpt
	 *
	 * @param string $before
	 * @param string $after
	 *
	 * @author Maxime CULEA
	 */
	public function the_copyright( $before = '', $after = '' ) {
		echo $this->has_copyright() ? $before . \BEA\Model_Factory\Functions::esc_textarea( $this->get_the_copyright() ) . $after : '';
	}

	/**
	 * Get the current file's title
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_the_title() {
		return $this->wp_object->post_title;
	}

	/**
	 * Check if has title
	 * @return bool
	 * @author Maxime CULEA
	 */
	public function has_title() {
		$excerpt = $this->get_the_title();

		return ! empty( $excerpt );
	}

	/**
	 * Display the title
	 *
	 * @param string $before
	 * @param string $after
	 *
	 * @author Maxime CULEA
	 */
	public function the_title( $before = '', $after = '' ) {
		echo $this->has_title() ? $before . esc_html( $this->get_the_title() ) . $after : '';
	}

	/**
	 * Get the thumbnail
	 *
	 * @param string $size
	 * @param array $args
	 *
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_thumbnail( $size = 'thumbnail', $args = [ ] ) {
		return bea_get_attachment_image( $this->get_ID(), $size, $args );
	}

	/**
	 * Display the thumbnail
	 *
	 * @param string $size
	 * @param array $args
	 */
	public function the_thumbnail( $size = 'thumbnail', $args = [ ] ) {
		echo $this->get_thumbnail( $size, $args );
	}

	/**
	 * Get file's type
	 *
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_type() {
		// Get te file_path
		$file_path = get_attached_file( $this->get_ID() );
		if ( $file_path === false ) {
			return '';
		}

		// check the mime_type
		$mime = wp_check_filetype( $file_path );

		if ( strpos( $mime['type'], 'image' ) !== false ) {
			$mime_type = __( 'Image', 'bea-model-factory' );
		} elseif ( strpos( $mime['type'], 'video' ) !== false ) {
			$mime_type = $mime['ext'] === 'avi' ? 'avi' : 'video';
		} elseif ( strpos( $mime['type'], 'audio' ) !== false ) {
			$mime_type = __( 'Audio', 'bea-model-factory' );
		} elseif ( strpos( $mime['type'], 'pdf' ) !== false ) {
			$mime_type = __( 'PDF', 'bea-model-factory' );
		} else {
			$mime_type = $mime['ext'];
		}

		return strtolower( $mime_type );
	}

	/**
	 * Get file's size
	 *
	 * @return false|string
	 * @author Maxime CULEA
	 */
	public function get_size() {
		$file_size = filesize( get_attached_file( $this->get_ID() ) );
		if ( false === $file_size ) {
			return '';
		}

		foreach ( [
			__( 'TB', 'bea-model-factory' ) => TB_IN_BYTES,
			__( 'GB', 'bea-model-factory' ) => GB_IN_BYTES,
			__( 'MB', 'bea-model-factory' ) => MB_IN_BYTES,
			__( 'kB', 'bea-model-factory' ) => KB_IN_BYTES,
			__( 'B', 'bea-model-factory' )  => 1,
		] as $unit => $mag ) {
			if ( doubleval( $file_size ) >= $mag ) {
				return number_format_i18n( $file_size / $mag, 0 ) . ' ' . $unit;
			}
		}

		return '';
	}

	/**
	 * Get the file info : type / size
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_info() {
		$type = $this->get_type();
		$size = $this->get_size();
		if ( empty( $type ) || empty( $size ) ) {
			return '';
		}

		return sprintf( '(%s. %s)', $type, $size );
	}

	/**
	 * Display the file info : type / size
	 * @author Maxime CULEA
	 */
	public function the_info() {
		echo esc_html( $this->get_info() );
	}

	/**
	 * Get the file's download url
	 * @return false|string
	 */
	public function get_download_url() {
		$url = wp_get_attachment_url( $this->get_ID() );
		return ! empty( $url ) ? $url : '';
	}

	/**
	 * Display the file's download url
	 * @author Maxime CULEA
	 */
	public function the_download_url() {
		echo esc_url( $this->get_download_url() );
	}

	/**
	 * Get file's metadata
	 * Only on demand to not ask too much data for nothing
	 * @author Maxime CULEA
	 */
	public function get_metadata() {
		if ( ! empty( $this->meta_data ) ) {
			return;
		}
		$this->meta_data = wp_get_attachment_metadata( $this->get_ID() );
	}

	/**
	 * Get file's height
	 * @return int
	 * @author Maxime CULEA
	 */
	public function get_the_height() {
		$this->get_metadata();
		return ! empty( $this->meta_data['height'] ) ? $this->meta_data['height'] : 0;
	}

	/**
	 * Display file's height
	 * @author Maxime CULEA
	 */
	public function the_height() {
		echo esc_attr( $this->get_the_height() );
	}

	/**
	 * Get file's width
	 * @return int
	 * @author Maxime CULEA
	 */
	public function get_the_width() {
		$this->get_metadata();
		return ! empty( $this->meta_data['width'] ) ? $this->meta_data['width'] : 0;
	}

	/**
	 * Display file's width
	 * @author Maxime CULEA
	 */
	public function the_width() {
		echo esc_attr( $this->get_the_width() );
	}

	/**
	 * Get the file's ga type depending on the mime type
	 * @return string
	 * @author Maxime CULEA
	 */
	public function get_the_ga_type() {
		switch ( $this->get_type() ) {
			case 'pdf' :
				$ga_type = 'PDF';
			break;
			case '' :
				$ga_type = 'Excel';
			break;
			case 'docx' :
				$ga_type = 'Word';
			break;
			case 'pptx' :
				$ga_type = 'PPT';
			break;
			default :
				$ga_type = '';
			break;
		}
		return $ga_type;
	}

	/**
	 * Display the file's type for GA purpose
	 * @author Maxime CULEA
	 */
	public function the_ga_type() {
		echo esc_html( $this->get_the_ga_type() );
	}
}