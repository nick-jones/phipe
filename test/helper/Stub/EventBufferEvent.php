<?php

namespace Phipe\Stub;

/**
 * EventBufferEvent is a final class, so cannot be mocked. This provides a copy of the interface, for use within
 * unit tests.
 *
 * @package Phipe\Stub
 */
interface EventBufferEvent {
	/**
	 * @param \EventBase $base
	 * @param resource|null $socket
	 * @param int $options
	 * @param callable|NULL $readcb
	 * @param callable|NULL $writecb
	 * @param callable|NULL $eventcb
	 */
	public function __construct(\EventBase $base, $socket = NULL, $options = 0, callable $readcb = NULL, callable $writecb = NULL, callable $eventcb = NULL);

	/**
	 * @param string $addr
	 * @return bool
	 */
	public function connect($addr);

	/**
	 * @param \EventDnsBase $dns_base
	 * @param string $hostname
	 * @param int $port
	 * @param int $family
	 * @return bool
	 */
	public function connectHost(\EventDnsBase $dns_base, $hostname, $port, $family = \EventUtil::AF_UNSPEC);

	/**
	 * @param \EventBase $base
	 * @param int $options
	 * @return array
	 */
	public function createPair(\EventBase $base, $options = 0);

	/**
	 * @param int $events
	 * @return bool
	 */
	public function disable($events);

	/**
	 * @param int $events
	 * @return bool
	 */
	public function enable($events);

	public function free();

	/**
	 * @return string
	 */
	public function getDnsErrorString();

	/**
	 * @return int
	 */
	public function getEnabled();

	/**
	 * @return \EventBuffer
	 */
	public function getInput();

	/**
	 * @return \EventBuffer
	 */
	public function getOutput();

	/**
	 * @param string $data
	 * @param int $size
	 * @return int
	 */
	public function read(&$data, $size);

	/**
	 * @param \EventBuffer $buf
	 * @return bool
	 */
	public function readBuffer(\EventBuffer $buf);

	/**
	 * @param callable $readcb
	 * @param callable $writecb
	 * @param callable $eventcb
	 * @param mixed $arg
	 */
	public function setCallbacks(callable $readcb = NULL, callable $writecb = NULL, callable $eventcb = NULL, $arg = NULL);

	/**
	 * @param int $priority
	 * @return bool
	 */
	public function setPriority($priority);

	/**
	 * @param int $timeout_read
	 * @param int $timeout_write
	 * @return bool
	 */
	public function setTimeouts($timeout_read, $timeout_write);

	/**
	 * @param int $events
	 * @param int $lowmark
	 * @param int $highmark
	 */
	public function setWatermark($events, $lowmark, $highmark);

	/**
	 * @return string
	 */
	public function sslError();

	/**
	 * @param \EventBase $base
	 * @param \EventBufferEvent $underlying
	 * @param \EventSslContext $ctx
	 * @param int $state
	 * @param int $options
	 * @return \EventBufferEvent
	 */
	public static function sslFilter(\EventBase $base, \EventBufferEvent $underlying, \EventSslContext $ctx, $state, $options = 0);

	public function sslRenegotiate();

	/**
	 * @param \EventBase $base
	 * @param resource|NULL $socket
	 * @param \EventSslContext $ctx
	 * @param int $state
	 * @param int $options
	 * @return mixed
	 */
	public static function sslSocket(\EventBase $base, $socket, \EventSslContext $ctx, $state, $options = 0);

	/**
	 * @param string $data
	 * @return bool
	 */
	public function write($data);

	/**
	 * @param \EventBuffer $buf
	 * @return bool
	 */
	public function writeBuffer(\EventBuffer $buf);
}