<?php
namespace AppBundle\Services;

use AppBundle\Entity\TimeEntry;
use AppBundle\Entity\User;
use AppBundle\Form\TimeEntryType;
use AppBundle\Interfaces\RedmineManagerInterface;
use Redmine\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class RedmineManager
 * @package AppBundle\Services
 */
class TimeEntryManager
{

    private $redmineManager;

    private $formFactory;
    
    private $router;


    /**
     * @param RedmineManagerInterface $redmineManager
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(RedmineManagerInterface $redmineManager,
                                FormFactoryInterface $formFactory,
                                RouterInterface $router)
    {
        $this->redmineManager = $redmineManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function process(Request $request, $identifier, $issueId = null)
    {
        $project = $this->redmineManager->getProject($identifier);
        $activities = $this->redmineManager->getActivitiesPairs();

        $timeEntry = new TimeEntry();

        $form = $this->formFactory->create(TimeEntryType::class, $timeEntry, [
            'projectId' =>  $project['id'],
            'activities' => $activities,
            'issueId' => $issueId
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'Save', 'attr' => [ 'class' => 'btn btn-primary' ]]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $timeEntry->setIssueId($issueId);
            $response = $this->redmineManager->trackTime($timeEntry->toArray());

            $session = $request->getSession();

            if($response->error){
                $session->getFlashBag()->add('warning', 'Time did not traсked. Something went wrong');
            } else
                $session->getFlashBag()->add('success', "{$timeEntry->getHours()}h was tracked");

            $url = $this->router->generate('project_view', ['identifier' => $identifier]);
            return new RedirectResponse($url, 301);
        }


        return [
            'project' => $project,
            'form' => $form->createView()
        ];
    }



}