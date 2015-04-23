<?php

namespace Site\CoreBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Peerassess\CoreBundle\Entity\Test;
use Site\CoreBundle\Form\TestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TestController extends BaseController
{
    /**
     * Show all the tests subjectes for a supervisor
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction ()
    {
        $em = $this->getDoctrine()->getManager();
        $tests = $em->getRepository('PeerassessCoreBundle:Test')->findAll();
        return $this->render(
            'SiteCoreBundle:Test:list.html.twig',
            array(
                'tests' => $tests
            )
        );
    }

    public function createAction(Request $request)
    {
        $test = new Test();
        $form = $this->createForm(new TestType(), $test);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // Save this new test.
                $this->get('peerassess_core.test_manager')->create($test);

                // Redirect to the page to create a marking scheme for this test.
                return $this->redirectRoute('marking_scheme_create', array(
                    'testId' => $test->getId()
                ));
            }
        }
        return $this->render('SiteCoreBundle:Test:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function showAction($testId)
    {
        $em = $this->getDoctrine()->getManager();
        $test = $em->getRepository('PeerassessCoreBundle:Test')->findOneById($testId);
        return $this->render('SiteCoreBundle:Test:show.html.twig', array(
            'test' => $test
        ));
    }

    /**
     * @ParamConverter("test", class="PeerassessCoreBundle:Test",
     *     options={"id" = "testId"})
     */
    public function editAction(Test $test)
    {
        $form = $this->createForm(new TestType(), $test);
        if ($form->isValid()) {
            // Save this new test.
            $this->get('peerassess_core.test_manager')->create($test);

            // Redirect to the page to create a marking scheme for this test.
            return $this->redirectRoute('marking_scheme_edit', array(
                'testId' => $test->getId()
            ));
        }
        return $this->render('SiteCoreBundle:Test:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("test", class="PeerassessCoreBundle:Test",
     *     options={"id" = "testId"})
     */
    public function fileDownloadAction(Test $test)
    {
        $headers = array(
            'Content-Type' => 'application/zip, application/octet-stream',
            'Content-Disposition' => 'attachment; filename="test-' . $test->getId() . '.zip"'
        );

        $contents = $this->get('peerassess_core.file_manager')
            ->retrieve($test->getBaseFileKey());

        return new Response($contents, 200, $headers);
    }
}
