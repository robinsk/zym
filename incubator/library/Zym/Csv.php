<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Csv
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Csv
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Csv implements Iterator
{
    /**
     * Maximum row length
     *
     */
    const ROW_LENGTH = 4096;

    /**
     * The pointer to the CSV file.
     *
     * @var resource
     */
    protected $_filePointer = null;

    /**
     * The current element, which will be returned on each iteration.
     *
     * @var array
     */
    protected $_currentElement = null;

    /**
     * The row counter.
     *
     * @var int
     */
    protected $_rowCounter = 0;

    /**
     * The delimiter for the CSV file.
     *
     * @var string
     */
    protected $_delimiter = null;

    /**
     * Column headers
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * Open the CSV file.
     *
     * @param string $file
     * @param string $delimiter
     * @throws Zym_Csv_Exception_FileNotReadable
     */
    public function __construct($file, $delimiter = ',', $readHeaders = true)
    {
        if (!file_exists($file)) {
            /**
             * @see Zym_Csv_Exception
             */
            require_once 'Zym/Csv/Exception.php';

            throw new Zym_Csv_Exception(sprintf('The file "%s" cannot be found.', $file));
        }

        if (!is_readable($file)) {
            /**
             * @see Zym_Csv_Exception
             */
            require_once 'Zym/Csv/Exception.php';

            throw new Zym_Csv_Exception(sprintf('File "%s" was found, but cannot be read.', $file));
        }

        $this->filePointer = fopen($file, 'r');
        $this->delimiter = $delimiter;

        if ($readHeaders) {
            $headerRow = $this->current();

            $headers = array();

            foreach ($headerRow as $column) {
                $headers[] = $column;
            }

            $this->_headers = $headers;
        }
    }

    /**
     * Reset the file pointer.
     *
     */
    public function rewind()
    {
        $this->_rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * Return the current CSV row as array
     *
     * @return array
     */
    public function current()
    {
        $this->currentElement = fgetcsv($this->filePointer, self::ROW_LENGTH, $this->delimiter);

        if ($this->next()) {
            $this->_rowCounter += 1;
        }

        if (!empty($this->_headers)) {
            $return = array();

            foreach ($this->_headers as $index => $header) {
                $return[$header] = $this->_currentElement[$index];
            }

            return $return;
        } else {
            return $this->currentElement;
        }
    }

    /**
     * Return the current row number.
     *
     * @return int
     */
    public function key()
    {
        return $this->_rowCounter;
    }

    /**
     * Check if the end of file is reached.
     *
     * @return boolean
     */
    public function next()
    {
        if (is_resource($this->filePointer)) {
            return !feof($this->filePointer);
        }

        return false;
    }

    /**
     * Check if the next row is a valid row.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->next();
    }

    /**
     * Returns the entire CSV as array
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach ($this as $row) {
            $array[] = $row;
        }

        $this->rewind();

        return $array;
    }
}