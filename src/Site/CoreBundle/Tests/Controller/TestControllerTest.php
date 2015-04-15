<?php

namespace Site\CoreBundle\Tests\Controller;

use Peerassess\CoreBundle\Tests\BaseTestCase;

class TestControllerTest extends BaseTestCase
{
    public function testCreate()
    {
        $this->logIn('conradk@conradk.com', 'password');

        $crawler = $this->client->request('GET', '/admin/test/create');

        $this->assertTrue($this->client->getResponse()->isOk());
    }
}
