<?php

namespace Phipe\Loop;

/**
 * Workers are used by Runners for performing long running tasks. This are effectively glorified callbacks; the main
 * benefit is that they can perform initialisation work, and can also indicate if they have nothing more to do.
 *
 * @package Phipe\Loop
 */
interface Worker {
	/**
	 * This method is called before work beings. Concrete implementations should perform any setup work when this
	 * method is called.
	 */
	public function initialise();

	/**
	 * This method is called when the Runner instance is running. Concrete implementations should perform their task
	 * within this method.
	 */
	public function work();

	/**
	 * This method is called to check whether the worker still wants to continue working. If the Worker does not
	 * maintain any state that can indicate this, then simply return TRUE always.
	 *
	 * @return bool
	 */
	public function hasWork();
}