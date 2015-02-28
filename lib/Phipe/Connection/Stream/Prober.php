<?php

namespace Phipe\Connection\Stream;

use Phipe\Connection\Prober as BaseProber;

/**
 * Provides Probing behaviour for Stream Connection instances. This makes use of stream_select to wait for changed
 * connections. Various conversions internally have be performed to make this possible.
 *
 * @package Phipe\Connection\Stream
 */
class Prober implements BaseProber
{
    /**
     * An instance that provides select() like behaviour on file descriptors.
     *
     * @var Selector
     */
    protected $selector;

    /**
     * @param Selector $selector
     */
    public function __construct(Selector $selector)
    {
        $this->selector = $selector;
    }

    /**
     * Probes the provided connections for activity.
     *
     * @param Connection[] $connections
     */
    public function probe(array $connections)
    {
        // Read buffers are used for populating *new* data - as such, we need to remove any stale data.
        $this->clearConnectionReadBuffers($connections);

        // Resolve any changed resource handles
        $changedResources = $this->resolveChangedResources(
            // Translate our connections to an array of resource handles
            $this->connectionsToResources($connections)
        );

        // Populate the read buffers of those changed Connection instances
        $this->populateConnectionReadBuffers(
            // Translate the changed resource handles to an array of Connection instances
            $this->resourcesToConnections($changedResources, $connections)
        );
    }

    /**
     * Interrogates the provided resource handles, and returns those handles have changed.
     *
     * @param resource[] $resources The resource handles to be interrogated
     * @return resource[] The changed resource handles
     */
    protected function resolveChangedResources(array $resources)
    {
        $changed = $this->selector->select($resources);

        return $changed;
    }

    /**
     * Clears the read buffers of the provided connections.
     *
     * @param Connection[] $connections
     */
    protected function clearConnectionReadBuffers(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->clearReadBuffer();
        }
    }

    /**
     * Requests that the provided Connection instances populate their own read buffer.
     *
     * @param Connection[] $connections
     */
    protected function populateConnectionReadBuffers(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->populateReadBuffer();
        }
    }

    /**
     * Converts an array of Connection instances into an array of resource handles.
     *
     * @param Connection[] $connections The instances to be translated
     * @return resource[] The resource handles associated with the provided instances
     */
    protected function connectionsToResources(array $connections)
    {
        $resources = [];

        foreach ($connections as $position => $connection) {
            $resource = $connection->getStream();
            $resources[$position] = $resource;
        }

        return $resources;
    }

    /**
     * Converts an array of resources to Connections. This is achieved by virtue of the positioning within the original
     * array of Connections. This can only be used with resources extracted via the connectionsToResources().
     *
     * @param resource[] $resources Resource handles to be mapped to Connections
     * @param Connection[] $existingConnections Connections that should contain the associated resource handles
     * @return array
     */
    protected function resourcesToConnections($resources, $existingConnections)
    {
        $drivers = [];

        foreach ($resources as $position => $resource) {
            $drivers[] = $existingConnections[$position];
        }

        return $drivers;
    }
}