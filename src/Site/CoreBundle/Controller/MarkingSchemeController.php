<?php

namespace Site\CoreBundle\Controller;

use Peerassess\CoreBundle\Controller\BaseController;
use Site\CoreBundle\Form\MarkingSchemeType;
use Peerassess\CoreBundle\Entity\MarkingScheme;
use Peerassess\CoreBundle\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MarkingSchemeController extends BaseController
{
    /**
     * @ParamConverter("test", class="PeerassessCoreBundle:Test",
     *     options={"id" = "testId"})
     */
    public function markingSchemeCreateAction(Request $request, Test $test)
    {
        $markingScheme = $test->getMarkingScheme();
        $test->setMarkingScheme($markingScheme);
        $form = $this->createForm(new MarkingSchemeType(), $markingScheme);

        $res = $this->handleCreateOrEdit($request, $form, $markingScheme);
        if ($res instanceof Response) {
            return $res;
        }

        return $this->render('SiteCoreBundle:MarkingScheme:markingSchemeCreate.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("markingScheme", class="PeerassessCoreBundle:MarkingScheme",
     *     options={"id" = "markingSchemeId"})
     */
    public function markingSchemeEditAction(Request $request, MarkingScheme $markingScheme)
    {
        $form = $this->createForm(new MarkingSchemeType(), $markingScheme);

        $res = $this->handleCreateOrEdit($request, $form, $markingScheme);
        if ($res instanceof Response) {
            return $res;
        }

        return $this->render('SiteCoreBundle:MarkingScheme:markingSchemeCreate.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function handleCreateOrEdit(Request $request, $form, $markingScheme)
    {
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($markingScheme);
                $em->flush();

                $markingScheme = $em->getRepository('PeerassessCoreBundle:MarkingScheme')
                    ->findOneById($markingScheme->getId());

                return $this->redirect($this->generateUrl('marking_scheme_edit', array(
                    'markingSchemeId' => $markingScheme->getId()
                )));
            }
        }
    }

}
