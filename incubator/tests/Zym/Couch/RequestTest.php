<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zym/Couch/Request.php';

class Zym_Couch_RequestTest extends PHPUnit_Framework_TestCase 
{
    const CRLF = "\r\n";
    
    public function testInvalidRequestThrowsException()
    {
        try {
            $request = new Zym_Couch_Request('/foo/bar', 'INVALID');
            $this->fail('No exception thrown');
        } catch (Exception $e) {
            $this->assertEquals('Zym_Couch_Exception', get_class($e));
        }
    }
    
    public function testGetRawRequest()
    {
        $request = new Zym_Couch_Request('/foo/bar', Zym_Couch_Request::GET, 'myDataGoesHere');
        $rawRequest = $request->getRawRequest();
        
        $this->assertContains('GET /foo/bar HTTP/1.0' . self::CRLF, $rawRequest);
        $this->assertContains('Date: ', $rawRequest);
        $this->assertContains('Content-Length: 14' . self::CRLF, $rawRequest);
        $this->assertContains('Content-Type: application/json' . self::CRLF . self::CRLF, $rawRequest);
        $this->assertContains('myDataGoesHere' . self::CRLF, $rawRequest);
    }
    
    public function testSendArray()
    {
        $originalData = array('my' => 'data', 'goes' => 'here');
        
        $request = new Zym_Couch_Request('/foo/bar', Zym_Couch_Request::GET, $originalData);
        $rawRequest = $request->getRawRequest();
        
        $this->assertContains('Content-Length: 27' . self::CRLF, $rawRequest);
        $this->assertContains('{"my":"data","goes":"here"}' . self::CRLF, $rawRequest);
    }
    
    public function testGetters()
    {
        $request = new Zym_Couch_Request('/foo/bar', Zym_Couch_Request::GET, 'myDataGoesHere');
        $this->assertEquals('/foo/bar', $request->getUrl());
        $this->assertEquals(Zym_Couch_Request::GET, $request->getMethod());
        $this->assertEquals('myDataGoesHere', $request->getData());
    }
}