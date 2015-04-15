<?php

namespace Peerassess\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends Controller
{
    protected function getCandidate()
    {
        return $this->getUser()->getCandidate();
    }

    protected function getSupervisor()
    {
        return $this->getUser()->getSupervisor();
    }

    protected function redirectBack()
    {
        if (!$this->getRequest()->headers->has('referer')) {
            throw new \LogicException('No referer provided. Cannot redirect back.');
        }

        $referer = $this->getRequest()->headers->get('referer');

        return $this->redirect($referer);
    }

    protected function redirectRoute($route, array $params = array())
    {
        $url = $this->generateUrl($route, $params);

        return $this->redirect($url);
    }

    /**
     * @param string $key
     * @param string $contentType
     * @param string $filename
     */
    protected function sendFile($key, $contentType, $filename = null)
    {
        if (null === $filename) {
            $filename = $key;
        }

        $headers = array(
            'Content-Type' => $contentType . ', application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        );

        $contents = $this->get('peerassess_core.file_manager')
            ->retrieve($key);

        return new Response($contents, 200, $headers);
    }

    protected function denyAccessUnlessEqual($a, $b, $message = '')
    {
        if ($a !== $b) {
            throw $this->createAccessDeniedException($message);
        }
    }

    protected function flashTrans($type, $msg, array $params = array(), $domain = null, $locale = null)
    {
        return $this->addFlash(
            $type,
            $this->get('translator')->trans($msg, $params, $domain, $locale)
        );
    }
}
