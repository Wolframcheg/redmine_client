<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="project_view")
     * @Template()
     */
    public function viewAction(Request $request, $id)
    {
        $page = $request->query->getInt('page', 1);
        $data = $this->container->get('redmine_manager')
            ->getProject($id, $page);
        //var_dump($data);exit();
        return ['data' => $data];
    }

}
