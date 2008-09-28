<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zym/Couch/Response.php';

class Zym_Couch_ResponseTest extends PHPUnit_Framework_TestCase 
{
    const CRLF = "\r\n";
    private $_couchRawResponse;
    
    /**
     * Enter description here...
     *
     * @var Zym_Couch_Response
     */
    private $_couchResponse;
    
    private $_couchResponseHeaders = array(
        'Date'         => 'Thu, 17 Aug 2006 05:39:28 +0000GMT',
        'Content-Type' => 'application/json',
        'Connection'   => 'close'
    );
    
    private $_couchResponseBody = array(
        '_id'        => '123BAC',
        '_rev'       => '946B7D1C',
        'Subject'    => 'I like Planktion',
        'Author'     => 'Rusty',
        'PostedDate' => '2006-08-15T17:30:12Z-04:00',
        'Tags'       => array('plankton', 'baseball', 'decisions'),
        'Body'       => 'I decided today that I don\'t like baseball. I like plankton.'
    );
    
    private $_couchRawResponseBody = '{
 "_id":"123BAC",
 "_rev":"946B7D1C",
 "Subject":"I like Planktion",
 "Author":"Rusty",
 "PostedDate":"2006-08-15T17:30:12Z-04:00",
 "Tags":["plankton", "baseball", "decisions"],
 "Body":"I decided today that I don\'t like baseball. I like plankton."
}';
    
    public function setUp()
    {
        $response = 'HTTP/1.1 200 OK' . self::CRLF;
        $response .= 'Date: Thu, 17 Aug 2006 05:39:28 +0000GMT' . self::CRLF;
        $response .= 'Content-Type: application/json' . self::CRLF;
        $response .= 'Connection: close' . self::CRLF . self::CRLF;
        $response .= $this->_couchRawResponseBody;
        
        $this->_couchRawResponse = $response;
        $this->_couchResponse = new Zym_Couch_Response($response);
    }
    
    public function testGetRawResponse()
    {
        $this->assertEquals($this->_couchRawResponse, $this->_couchResponse->getRawResponse());
    }
    
    public function testGetStatus()
    {
        $this->assertEquals(200, $this->_couchResponse->getStatus());
    }
    
    public function testGetHeaders()
    {
        $this->assertEquals($this->_couchResponseHeaders, $this->_couchResponse->getHeaders());
    }
    
    public function testGetIndividualHeaders()
    {
        $this->assertEquals('Thu, 17 Aug 2006 05:39:28 +0000GMT', $this->_couchResponse->getHeader('Date'));
        $this->assertEquals('application/json', $this->_couchResponse->getHeader('Content-Type'));
        $this->assertEquals('close', $this->_couchResponse->getHeader('Connection'));
        $this->assertEquals(null, $this->_couchResponse->getHeader('NON_EXISTING'));
    }
    
    public function testGetRawBody()
    {
        $this->assertEquals($this->_couchRawResponseBody, $this->_couchResponse->getBody(false));
    }
    
    public function testGetDecodedBody()
    {
        $this->assertEquals($this->_couchResponseBody, $this->_couchResponse->getBody());
    }
    
    public function testToArray()
    {
        $expected = array_merge(array('status' => 200),
                                $this->_couchResponseHeaders,
                                array('body' => $this->_couchResponseBody));
                                
        $this->assertEquals($expected, $this->_couchResponse->toArray());
    }
}