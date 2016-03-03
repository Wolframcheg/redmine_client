<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TimeEntry;
use AppBundle\Form\TimeEntryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;


/**
 * TimeEntry controller.
 *
 * @Route("/time-entry")
 */
class TimeEntryController extends Controller
{
    /**
     * @Route("/{identifier}", requirements={"identifier" = "[a-zA-Z1-9\-_]+"}, name="time_entry_project")
     * @Template()
     */
    public function timeEntryProjectAction(Request $request, $identifier)
    {
        $data = $this->container->get('time_entry_manager')
                ->process($request, $identifier);

        return $data;
    }

    /**
     * @Route("/{identifier}/{issueId}", requirements={"identifier" = "[a-zA-Z1-9\-_]+", "issueId" = "\d+"},name="time_entry_issue")
     * @Template("@App/TimeEntry/timeEntryProject.html.twig")
     */
    public function timeEntryIssueAction(Request $request, $identifier, $issueId)
    {
        $data = $this->container->get('time_entry_manager')
            ->process($request, $identifier, $issueId);

        return $data;
    }

}
