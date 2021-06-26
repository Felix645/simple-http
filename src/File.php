<?php


namespace Neon\Http;


class File
{
    /**
     * The filename
     *
     * @var string
     */
    private $filename;

    /**
     * Path to the file.
     *
     * @var string
     */
    private $filepath;

    /**
     * File constructor.
     *
     * @param string $filename
     * @param string $filepath
     */
    public function __construct(string $filename, string $filepath)
    {
        $this->filename = $filename;
        $this->filepath = $filepath;
    }

    /**
     * Gets the file name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->filename;
    }

    /**
     * Gets the file path.
     *
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->filepath;
    }
}