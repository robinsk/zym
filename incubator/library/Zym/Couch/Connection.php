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
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_Couch_Request
 */
require_once 'Zym/Couch/Request.php';

/**
 * @see Zym_Couch_Response
 */
require_once 'Zym/Couch/Response.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Couch
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Couch_Connection
{
    /**
     * Host name
     *
     * @var string
     */
    protected $_host = 'localhost';

    /**
     * Port number
     *
     * @var int port
     */
    protected $_port = 5984;

    /**
     * Constructor
     *
     * @param string $host
     * @param int port
     */
    public function __construct($host = null, $port = null)
    {
        if (null !== $host) {
            $this->_host = $host;
        }
        
        if (null !== $port) {
            $this->_port = $port;
        }
    }

    /**
     * Send the request and return the response
     *
     * @param Zym_Couch_Request
     * @return Zym_Couch_Response
     */
    public function send(Zym_Couch_Request $request)
    {
        $errorString = '';
        $errorNumber = '';
        $response    = '';

        $socket = fsockopen($this->_host, $this->_port, $errorNumber, $errorString);

        if (!$socket) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('Failed to open connection to ' . $this->_host . ':' .
                                            $this->_port . ' (Error number ' . $errorNumber . ': ' .
                                            $errorString . ')');
        }

        fwrite($socket, $request->getRawRequest());

        while (!feof($socket)) {
            $response .= fgets($socket);
        }

        fclose($socket);

        $socket = null;

        return new Zym_Couch_Response($response);
    }
    
    /**
     * Send the request and returns the response body
     *
     * @param Zym_Couch_Request $request
     * @return array|string
     */
    public function execute(Zym_Couch_Request $request)
    {
        $response = $this->send($request);
        
        return $response->getBody(true);
    }

    /**
     * List all databases
     *
     * @return Zym_Couch_Response
     */
    public function listAll()
    {
        return $this->execute(new Zym_Couch_Request('/_all_dbs'));
    }

    /**
     * Get CouchDb info
     *
     * @return Zym_Couch_Response
     */
    public function info($database = '')
    {
        $message = '/';

        if ($database != null) {
            $message .= $this->_stripSlashes($database) . '/';
        }

        return $this->execute(new Zym_Couch_Request($message));
    }

    /**
     * Create a database
     *
     * @param string $name
     * @throws Zym_Couch_Exception
     * @return Zym_Couch_Response
     */
    public function createDb($name)
    {
        $name = '/' . $this->_stripSlashes($name) . '/';
        $request = new Zym_Couch_Request($name, Zym_Couch_Request::PUT);

        $response = $this->send($request);

        if ($response->getStatus() == 409) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('Database already exists.');
        }

        return $response;
    }

    /**
     * Delete a database
     *
     * @param string $name
     * @throws Zym_Couch_Exception
     * @return Zym_Couch_Response
     */
    public function deleteDb($name)
    {
        $name = '/' . $this->_stripSlashes($name) . '/';
        $request = new Zym_Couch_Request($name, Zym_Couch_Request::DELETE);

        $response = $this->send($request);
        
        if ($response->getStatus() == 500) {
            /**
             * @see Zym_Couch_Exception
             */
            require_once 'Zym/Couch/Exception.php';

            throw new Zym_Couch_Exception('Database doesn\'t exists.');
        }

        return $response;
    }
    
    /**
     * Get a DB instance
     *
     * @param string $name
     * @return Zym_Couch_Db
     */
    public function getDb($name)
    {
        $db = new Zym_Couch_Db($name);
        $db->setConnection($this);
        
        return $db;
    }

    /**
     * Strip forward slashes from the provided string
     *
     * @param string $string
     * @return string
     */
    protected function _stripSlashes($string)
    {
        return str_replace('/', '', $string);
    }
}