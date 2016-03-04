<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * TimeEntry controller.
 *
 * @Route("/time-entry")
 */
class CommentController extends Controller
{
    /**
     * @Route("/{identifier}", requirements={"identifier" = "[a-zA-Z1-9\-_]+"}, name="add_comment")
     * @Template()
     */
    public function addCommentAction(Request $request, $identifier)
    {
        $data = $this->container->get('comment_manager')
                ->process($request, $identifier);

        return $data;
    }

}
