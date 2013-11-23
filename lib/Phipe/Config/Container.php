<?php

namespace Phipe\Config;

/**
 * This is a container for configuration options. Raw values can be provided, as well as factories for lazy
 * instantiation of values. Factories will not be run if the container already holds a value for one of the keys.
 * Additionally, once factories are run, the values are persisted as a value - as such, the factories are run once
 * only.
 *
 * @package Phipe\Config
 */
class Container implements \ArrayAccess {
	/**
	 * Mapping of config key -> value.
	 *
	 * @var array
	 */
	protected $values = array();

	/**
	 * Mapping of config key -> callable
	 *
	 * @var array
	 */
	protected $factories = array();

	/**
	 * Raw config values and factories can optionally be provided here. If you wish to provide them post-construction
	 * then the offsetSet & factory methods allow further to be added.
	 *
	 * @param array $values Optional mapping of config key -> value
	 * @param array $factories Optional mapping of config key -> callable
	 */
	public function __construct(array $values = array(), array $factories = array()) {
		$this->values = $values;
		$this->factories = $factories;
	}

	/**
	 * Indicates whether the field exists as part of the config. This can be used in conjunction with isset();
	 *
	 * @param mixed $field
	 * @return bool
	 */
	public function offsetExists($field) {
		return isset($this->values[$field]) || isset($this->factories[$field]);
	}

	/**
	 * Retrieves a config value. If there is none, it will attempt to run a factory, if available.
	 *
	 * @param mixed $field
	 * @return mixed|void
	 */
	public function offsetGet($field) {
		if (isset($this->values[$field]) || $this->load($field)) {
			return $this->values[$field];
		}

		return NULL;
	}

	/**
	 * Sets a config value against an associated field.
	 *
	 * @param mixed $field
	 * @param mixed $value
	 */
	public function offsetSet($field, $value) {
		$this->values[$field] = $value;
	}

	/**
	 * Removes a config field. Currently this does not touch registered factories; as such, you can unset a field,
	 * and fetch a fresh value on next request.
	 *
	 * @param mixed $field
	 */
	public function offsetUnset($field) {
		if (isset($this->values[$field])) {
			unset($this->values[$field]);
		}
	}

	/**
	 * Registers a factory callback, associated with a field name. If a factory of the same name is already registered,
	 * it will be overwritten.
	 *
	 * @param string $field
	 * @param callable $factory
	 */
	public function factory($field, callable $factory) {
		$this->factories[$field] = $factory;
	}

	/**
	 * Indicates whether we have a factory registered with the provided field name.
	 *
	 * @param string $field
	 * @return bool
	 */
	public function factoryExists($field) {
		return isset($this->factories[$field]);
	}

	/**
	 * Populates a config field with the result of a factory being run, if one exists.
	 *
	 * @param string $field The name of the field to be loaded
	 * @return bool TRUE if a factory has been successfully run, FALSE otherwise
	 */
	protected function load($field) {
		if ($this->factoryExists($field)) {
			$this->values[$field] = $this->runFactory($field);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Runs the callable associated with the field name, and returns the result of it's execution.
	 *
	 * @param string $field
	 * @return mixed
	 */
	protected function runFactory($field) {
		$factory = $this->factories[$field];
		return call_user_func($factory);
	}
}