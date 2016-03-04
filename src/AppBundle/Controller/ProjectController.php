<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/", name="project_list")
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/{identifier}", requirements={"identifier" = "[a-zA-Z1-9\-_\/]+"}, name="project_view")
     * @Template()
     */
    public function viewAction(Request $request, $identifier)
    {
        $page = $request->query->getInt('page', 1);
        $data = $this->container->get('redmine_manager')
            ->getProjectWithIssues($identifier, $page);

        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('AppBundle:Comment')->findBy(['project' => $identifier],['createdAt' => 'ASC']);

        return [
            'data' => $data,
            'comments' => $comments
        ];
    }

}
