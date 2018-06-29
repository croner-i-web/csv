<?php

namespace mnshankar\CSV;

/**
 * CSV package by mnshankar
 *
 * @package mnshankar\CSV
 * @author  mnshankar
 * @license https://opensource.org/licenses/MIT MIT
 */
class CSV
{
    /**
     * Source
     *
     * @var mixed $source
     */
    protected $source;

    /**
     * Handle.
     *
     * @var mixed $handle
     */
    protected $handle;


    /**
     * Header row exists
     *
     * @var boolean
     */
    protected $headerRowExists = true;


    /**
     * Delimiter
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * Enclosure
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * With separator
     *
     * @var boolean
     */
    protected $withSeparator = false;

    /**
     * Set delimiter
     *
     * @param string $delimiter Delimiter
     *
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Set header row exists
     *
     * @param boolean $headerFlag Header flag
     *
     * @return $this
     */
    public function setHeaderRowExists($headerFlag = true)
    {
        $this->headerRowExists = $headerFlag;

        return $this;
    }

    /**
     * Set enclosure
     *
     * @param string $enclosure Enclosure
     *
     * @return $this
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * CSV function with
     *
     * @param string|array $source          Source
     * @param boolean      $headerRowExists Header row exists
     * @param string       $mode            Mode
     *
     * @return $this
     * @throws \Exception Throw exception on failure.
     */
    public function with($source, $headerRowExists = true, $mode = 'r+')
    {
        // fromArray
        if (is_array($source)) {
            $this->source = $source;
        } else {
            // fromfile
            if (is_string($source)) {
                $this->fromFile($source, $headerRowExists, $mode);
            } else {
                throw new \Exception('Source must be either an array or a file name');
            }
        }

        return $this;
    }

    /**
     * Sets the $withSeparator property to true to specify the delimiter in the file
     *
     * @return $this
     */
    public function withSeparator()
    {
        $this->withSeparator = true;
        return $this;
    }

    /**
     * Array
     *
     * @param mixed $arr Array
     *
     * @return $this
     */
    public function fromArray($arr)
    {
        $this->source = $arr;

        return $this;
    }

    /**
     * To array
     *
     * @return mixed
     */
    public function toArray()
    {
        return $this->source;
    }

    /**
     * From file
     *
     * @param string  $filePath        File path
     * @param boolean $headerRowExists Header row exists
     * @param string  $mode            Mode
     *
     * @return $this
     */
    public function fromFile($filePath, $headerRowExists = true, $mode = 'r+')
    {
        $from = fopen($filePath, $mode);
        $arr = [];
        $this->headerRowExists = $headerRowExists;

        if ($headerRowExists) {
            // first header row
            $header = fgetcsv($from, 0, $this->delimiter, $this->enclosure);
        }
        while (($data = fgetcsv($from, 0, $this->delimiter, $this->enclosure)) !== false) {
            $arr[] = $headerRowExists ? array_combine($header, $data) : $data;
        }

        fclose($from);
        $this->source = $arr;

        return $this;
    }

    /**
     * Put csv
     *
     * @param string $filePath File path
     * @param string $mode     Mode
     *
     * @return void
     */
    public function put($filePath, $mode = 'w+')
    {
        $fileToCreate = fopen($filePath, $mode);
        fwrite($fileToCreate, $this->toString());
        fclose($fileToCreate);
    }

    /**
     * Render CSV
     *
     * @param string $filename File name
     * @param string $mode     Mode
     *
     * @return void
     */
    public function render($filename = 'export.csv', $mode = 'r+')
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private');
        header('pragma: cache');

        $response = $this->toString();

        if( $this->withSeparator ){
            $response = 'sep='.$this->delimiter.PHP_EOL.$response;
        }

        echo $response;
        exit;
    }

    /**
     * Use PHP's inbuilt fputcsv to generate csv
     *
     * @return void
     */
    private function getCSV()
    {
        $outputStream = fopen('php://output', 'r+');
        if ($this->headerRowExists) {
            $longest_row = max($this->source);
            $header = array_keys(static::dot($longest_row));
            fputcsv($outputStream, $header, $this->delimiter, $this->enclosure);
        }

        foreach ($this->source as $key => $row) {
            fputcsv($outputStream, static::dot($row), $this->delimiter, $this->enclosure);
        }
        fclose($outputStream);
    }

    /**
     * This method is used by unit tests. So it is public.
     *
     * @return string
     */
    public function toString()
    {
        // buffer the output ...
        ob_start();

        $this->getCSV();

        //return it as a string
        return ob_get_clean();
    }

    /**
     * Copied from illuminate array to avoid dependence on illuminate/support
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array  $array   Array
     * @param string $prepend Prepend
     *
     * @return array
     */
    public static function dot(array $array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}
