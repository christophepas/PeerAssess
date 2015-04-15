<?php

namespace Peerassess\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BaseTestCase extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected function logIn($email, $password)
    {
        $session = $this->client->getContainer()->get('session');
        $em = $this->client->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('PeerassessCoreBundle:User')->findOneByEmail($email);

        $token = new UsernamePasswordToken(
            $user,
            $password,
            $firewall = 'main',
            $user->getRoles()
        );

        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
