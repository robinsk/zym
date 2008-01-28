<?php
/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Zym
 * @package    CSV
 * @subpackage Exception
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */

/**
 * @see Zym_CSV_Exception_FileNotReadable
 */
require_once 'Zym/CSV/Exception/FileNotReadable.php';

/**
 * @category   Zym
 * @package    CSV
 * @subpackage Exception
 * @copyright Copyright (c) 2008 Zym. (http://www.assembla.com/wiki/show/zym)
 * @license http://www.assembla.com/wiki/show/dpEKouT5Gr3jP5abIlDkbG/License    New BSD License
 */
class Zym_CSV implements Iterator
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
    protected $_rowCounter = null;

    /**
     * The delimiter for the CSV file.
     *
     * @var string
     */
    protected $_delimiter = null;

    /**
     * Open the CSV file.
     *
     * @param string $file
     * @param string $delimiter
     * @throws Zym_CSV_Exception_FileNotReadable
     */
    public function __construct($file, $delimiter = ',')
    {
        $this->filePointer = @fopen($file, 'r');
        $this->delimiter = $delimiter;

        if (empty($this->filePointer)) {
            throw new Zym_CSV_Exception_FileNotReadable(sprintf('The file "%s" cannot be read.', $file));
        }
    }

    /**
     * Reset the file pointer.
     *
     */
    public function rewind()
    {
        $this->rowCounter = 0;
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
        $this->rowCounter += 1;

        return $this->currentElement;
    }

    /**
     * Return the current row number.
     *
     * @return int
     */
    public function key()
    {
        return $this->rowCounter;
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
        if (!$this->next()) {
            if (is_resource($this->filePointer)) {
                fclose($this->filePointer);
            }

            return false;
        }

        return true;
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