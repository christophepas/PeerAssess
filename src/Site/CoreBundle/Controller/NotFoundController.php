<?php

namespace Site\CoreBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;

class NotFoundController extends BaseController
{
    public function notFoundAction()
    {
        $pathinfo = $this->getRequest()->getPathInfo();

        throw $this->createNotFoundException($pathinfo);
    }
}
