<?php

namespace Site\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener implements EventSubscriberInterface
{

    /**
     * @var \Bugsnag_Client
     */
    private $bugsnag;

    /**
     * @var string
     */
    private $env;

    public function __construct(\Bugsnag_Client $bugsnag, $env)
    {
        $this->bugsnag = $bugsnag;
        $this->env = $env;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('registerShutdownHandler', 0),
            ),
            KernelEvents::EXCEPTION => array(
                array('handleException', 0)
            )
        );
    }

    public function registerShutdownHandler(FilterControllerEvent $event)
    {
        register_shutdown_function(array($this, 'onShutdown'));
    }

    /**
     * Code taken from:
     * https://github.com/evolution7/Evolution7BugsnagBundle
     *
     * License: MIT
     */
    public function onShutdown()
    {
        // Get the last error if there was one, if not, let's get out of here.
        $error = error_get_last();
        if (!$error) {
            return;
        }

        $fatal = array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR,E_RECOVERABLE_ERROR);
        if (!in_array($error['type'], $fatal)) {
            return;
        }

        $message = sprintf("[Shutdown Error]: %s", $error['message']);
        $backtrace = sprintf("In file %s at line %d", $error['file'], $error['line']);

        $this->bugsnag->notifyError($message, $backtrace);
    }

    public function handleException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException) {
            return;
        }

        $this->bugsnag->notifyException($exception);
    }
}
