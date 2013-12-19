<?php

namespace Phipe\Connection\Event;

/**
 * Prober implementation for use with Event based connections. This simply asks the EventBase instance to wait for
 * changes with EventBufferEvent instances associated with it.
 *
 * @package Phipe\Connection\Event
 */
class EventProber implements \Phipe\Connection\Prober {
	/**
	 * @var \EventBase
	 */
	protected $eventBase;

	/**
	 * @param \EventBase $eventBase
	 */
	public function __construct($eventBase) {
		$this->eventBase = $eventBase;
	}

	/**
	 * Probes the supplied connections. This does not actually touch the supplied array; it is assumed that they share
	 * the same EventBase instance. If they don't then the factories have been misused. Unfortunately there is not
	 * way to stop this happening at the moment.
	 *
	 * @param EventConnection[] $connections
	 */
	public function probe(array $connections) {
		$this->eventBase->loop(\EventBase::LOOP_ONCE);
	}
}