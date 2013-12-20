<?php

namespace Phipe\Connection\Buffering;

/**
 * This connection implementation buffers reads and writes until a newline characters is reached.
 * The read data may contain numerous newlines, but will always end on one. Data to be written
 * will be buffered until a newline is provided.
 *
 * @package Phipe\Connection\Buffering
 */
class BufferingConnection extends \Phipe\Connection\Decorating\DecoratingConnection {
	/**
	 * @var string
	 */
	protected $readBuffer = '';

	/**
	 * @var string
	 */
	protected $writeBuffer = '';

	/**
	 * Write any data which precedes a newline character.
	 *
	 * @param string $data
	 */
	public function write($data) {
		$data = $this->writeBuffer . $data;
		$this->writeBuffer = $this->stripPartial($data);

		parent::write($data);
	}

	/**
	 * Read all data (newline inclusive) preceding a newline character.
	 *
	 * @return string
	 */
	public function read() {
		$data = $this->readBuffer . parent::read();
		$this->readBuffer = $this->stripPartial($data);

		return $data;
	}

	/**
	 * Strips any data that does *not* precede a newline character. This "partial" data is returned
	 * for buffering.
	 *
	 * @param string $data The string to be stripped
	 * @return string The partial part of the string, if applicable
	 */
	protected function stripPartial(&$data) {
		preg_match("#^((?:.*\r?\n)*)(.*)$#D", $data, $matches);

		$data = $matches[1];

		return $matches[2];
	}
}