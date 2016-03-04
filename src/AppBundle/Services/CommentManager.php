<?php
namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Interfaces\RedmineManagerInterface;
use AppBundle\Entity\Comment;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class CommentManager
 * @package AppBundle\Services
 */
class CommentManager
{

    private $redmineManager;

    private $formFactory;
    
    private $router;

    private $doctrine;


    /**
     * @param RedmineManagerInterface $redmineManager
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(RedmineManagerInterface $redmineManager,
                                FormFactoryInterface $formFactory,
                                RouterInterface $router,
                                RegistryInterface $doctrine)
    {
        $this->redmineManager = $redmineManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->doctrine = $doctrine;
    }

    public function process(Request $request, $identifier)
    {
        $project = $this->redmineManager->getProject($identifier);

        $em = $this->doctrine->getManager();
        $comment= new Comment();
        $comment->setProject($identifier);

        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->add('submit', SubmitType::class, ['label' => 'Save', 'attr' => [ 'class' => 'btn btn-primary' ]]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();
            $session = $request->getSession();
            $session->getFlashBag()->add('success', "Comment was created");

            $url = $this->router->generate('project_view', ['identifier' => $identifier]);
            return new RedirectResponse($url, 301);
        }

        return [
            'project' => $project,
            'form' => $form->createView()
        ];
    }



}