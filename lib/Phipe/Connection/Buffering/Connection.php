<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Decorating\Connection as DecoratingConnection;

/**
 * This connection implementation buffers reads and writes until a newline characters is reached.
 * The read data may contain numerous newlines, but will always end on one. Data to be written
 * will be buffered until a newline is provided.
 *
 * @package Phipe\Connection\Buffering
 */
class Connection extends DecoratingConnection
{
    /**
     * @var string
     */
    protected $readBuffer = '';

    /**
     * @var string
     */
    protected $partialReadBuffer = '';

    /**
     * @var string
     */
    protected $partialWriteBuffer = '';

    /**
     * @var array
     */
    protected $eventIgnores = [
        self::EVENT_READ
    ];

    /**
     * Write any data which precedes a newline character.
     *
     * @param string $data
     */
    public function write($data)
    {
        $data = $this->partialWriteBuffer . $data;
        $this->partialWriteBuffer = $this->stripPartial($data);

        parent::write($data);
    }

    /**
     * Read all data (newline inclusive) preceding a newline character.
     *
     * @return string
     */
    public function read()
    {
        return $this->readBuffer;
    }

    /**
     * Populates the read buffer for reading.
     */
    public function populateReadBuffer()
    {
        $data = $this->partialReadBuffer . parent::read();

        $this->partialReadBuffer = $this->stripPartial($data);
        $this->setReadBuffer($data);

        if (strlen($this->readBuffer) > 0) {
            $this->notify(self::EVENT_READ);
        }
    }

    /**
     * @param string $readBuffer
     */
    public function setReadBuffer($readBuffer)
    {
        $this->readBuffer = $readBuffer;
    }

    /**
     * Clear the internal read buffer.
     */
    public function clearReadBuffer()
    {
        $this->readBuffer = '';
    }

    /**
     * Strips any data that does *not* precede a newline character. This "partial" data is returned
     * for buffering.
     *
     * @param string $data The string to be stripped
     * @return string The partial part of the string, if applicable
     */
    protected function stripPartial(&$data)
    {
        preg_match("#^((?:.*\r?\n)*)(.*)$#D", $data, $matches);

        $data = $matches[1];

        return $matches[2];
    }
}