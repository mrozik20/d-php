<?php

namespace UserBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Entity\Author;
use AppBundle\Entity\Reaction;
use AppBundle\Entity\Product;
use AppBundle\Entity\PostClosure;
use AppBundle\Entity\PostTag;
use AppBundle\Entity\PostNote;
use AppBundle\Entity\Channel;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\MergeAccounts;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\UserWorkTime;

use UserBundle\Form\ThreadForm;

class DashboardController extends Controller
{
    /**
     * @Config\Route("/dashboard", name="dashboard",host="{subdomain}.%host%")
     * @Config\ParamConverter("subdomain", class="AppBundle:Instance", options={"mapping": {"subdomain": "subdomain"}})
     * @Config\Method({"GET", "POST"})
     */
    public function indexAction(Request $request, Instance $subdomain)
    {
        if($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            return $this->redirectToRoute('superadmin_users');
        }

        /** @var EventManager **/
        $em = $this->getDoctrine()->getManager();

        $megreclients = $em->getRepository(MergeAccounts::class)->getMergeClients($subdomain);
        $list_count = $em->getRepository(\AppBundle\Entity\Channel::class)->getThreadsCountTypesByUser($this->getUser());
        $countActivity = $this->renderView('UserBundle:Dashboard:countActivity.html.twig',['list_count' => $list_count,]);

        return $this->render(
            'UserBundle:Dashboard:index.html.twig',
            array(
                'megreclients' => $megreclients,
                'countActivity' => $countActivity,
                'typeView' => 'dashboard'
            )
        );
    }
}