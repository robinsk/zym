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
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */

/**
 * @see Zym_CouchDb_Request
 */
require_once 'Zym/CouchDb/Request.php';

/**
 * @see Zym_CouchDb_Response
 */
require_once 'Zym/CouchDb/Response.php';

/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_CouchDb
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_CouchDb_Connection
{
    protected $_host;
    protected $_port;

    public function __construct($host, $port)
    {
        $this->_host = $host;
        $this->_port = $port;
    }

    /**
     * Send the request and return the response
     *
     * @param Zym_CouchDb_Request
     * @return Zym_CouchDb_Response
     */
    public function send(Zym_CouchDb_Request $request)
    {
        $errorString = '';
        $errorNumber = '';
        $response    = '';

        $socket = fsockopen($this->_host, $this->_port, $errorNumber, $errorString);

        if (!$socket) {
            /**
             * @see Zym_CouchDb_Exception
             */
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Failed to open connection to ' . $this->_host . ':' .
                                            $this->_port . ' (Error number ' . $errorNumber . ': ' .
                                            $errorString . ')');
        }

        fwrite($socket, $request->getRawRequest());

        while (!feof($socket)) {
            $response .= fgets($socket);
        }

        fclose($socket);

        $socket = null;

        return new Zym_CouchDb_Response($response);
    }

    /**
     * List all databases
     *
     * @return Zym_CouchDb_Response
     */
    public function listAll()
    {
        return $this->send(new Zym_CouchDb_Request('/_all_dbs'));
    }

    /**
     * Get CouchDb info
     *
     * @return Zym_CouchDb_Response
     */
    public function info($database = '')
    {
        $message = '/';

        if ($database != null) {
            $message .= $this->_stripSlashes($database) . '/';
        }

        return $this->send(new Zym_CouchDb_Request($message));
    }

    public function createDb($name)
    {
        $name = '/' . $this->_stripSlashes($name) . '/';
        $request = new Zym_CouchDb_Request($name, Zym_CouchDb_Request::PUT);

        $response = $this->send($request);

        if ($response->getStatus() == 409) {
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Database already exists.');
        }

        return $response;
    }

    public function deleteDb($name)
    {
        $name = '/' . $this->_stripSlashes($name) . '/';
        $request = new Zym_CouchDb_Request($name, Zym_CouchDb_Request::DELETE);

        $response = $this->send($request);

        if ($response->getStatus() == 404) {
            require_once 'Zym/CouchDb/Exception.php';

            throw new Zym_CouchDb_Exception('Database doesn\'t exists.');
        }

        return $response;
    }

    protected function _stripSlashes($string)
    {
        return str_replace('/', '', $string);
    }
}