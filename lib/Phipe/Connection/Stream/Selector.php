<?php

namespace Phipe\Connection\Stream;

/**
 * This class encapsulates the select behaviour, largely to aid the testability of the Prober class. A "select"
 * strategy is used, stream_select by default. The strategy can be swapped out, if required.
 *
 * @package Phipe\Connection\Stream
 */
class Selector {
	/**
	 * The strategy to use for selecting updated resource handles.
	 *
	 * @var callable
	 */
	protected $selectStrategy = array(__CLASS__, 'selectOrSleep');

	/**
	 * Indicates which resources are available for reading (or rather, won't block for further processing)
	 *
	 * @param array $streams
	 * @param int $timeout
	 * @return array
	 */
	public function select(array $streams, $timeout = 500000) {
		// Call out select function
		$result = call_user_func($this->getSelectStrategy(), $streams, $timeout);

		list($changed, $streams) = $result;

		if ($changed === false) {
			throw new SelectFailureException('Select strategy indicated failure');
		}

		return $streams;
	}

	/**
	 * This strategy uses stream_select, but also emulates the timeout wait when no streams are supplied. When no
	 * streams are supplied to the stream_select function is return FALSE immediately.
	 *
	 * @param array $streams
	 * @param int $timeout
	 * @return array
	 */
	public function selectOrSleep(array $streams, $timeout) {
		if (count($streams) > 0) {
			$changed = stream_select($streams, $write = NULL, $except = NULL, 0, $timeout);
		}
		else {
			usleep($timeout);
			$changed = 0;
		}

		return array($changed, $streams);
	}

	/**
	 * @return callable
	 */
	public function getSelectStrategy() {
		return $this->selectStrategy;
	}

	/**
	 * @param callable $callback
	 */
	public function setSelectStrategy($callback) {
		$this->selectStrategy = $callback;
	}
}