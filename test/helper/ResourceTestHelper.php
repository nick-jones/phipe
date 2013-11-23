<?php

namespace Phipe;

/**
 * Small helper class to help with mocking stream handle behaviour.
 *
 * @package Phipe
 */
class ResourceTestHelper {
	/**
	 * @var resource
	 */
	protected $handle;

	/**
	 * @param resource $handle The resource handle to be used within the helper methods
	 */
	public function __construct($handle) {
		$this->handle = $handle;
	}

	/**
	 * Closes the resource handle, if it's open.
	 */
	public function close() {
		if (is_resource($this->handle)) {
			fclose($this->handle);
		}
	}

	/**
	 * Prepares our handle for reading. The payload is written our handle, and then the file pointer is reset to the
	 * previous position.
	 *
	 * @param string $payload
	 */
	public function addPayload($payload) {
		$position = ftell($this->handle);
		fwrite($this->handle, $payload);
		fseek($this->handle, $position);
	}

	/**
	 * Retrieves the payload written to our handle.
	 *
	 * @return string
	 */
	public function fetchPayload() {
		rewind($this->handle);
		$payload = '';

		while (!feof($this->handle)) {
			$payload .= fread($this->handle, 8192);
		}

		return $payload;
	}
}