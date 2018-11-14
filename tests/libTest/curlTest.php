<?php

// use src\lib;

use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
    public function testCurlGet() {
        $curl = New Curl();
        $url = "https://www.google.com.tw";
        $curl->setPort(443);
        $curl->get($url);

        $this->assertEquals(200, $curl->getHttpCode());
    }
}