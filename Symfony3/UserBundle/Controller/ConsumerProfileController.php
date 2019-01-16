<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Instance;
use AppBundle\Entity\Author;
use AppBundle\Entity\Pixel;
use AppBundle\Entity\Channel;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Note;
use AppBundle\Entity\Order;
use AppBundle\Entity\Tag;
use AppBundle\Entity\PostTag;
use AppBundle\Entity\Company;
use AppBundle\Entity\Reaction;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use UserBundle\Form\ConsumerProfileForm;
use UserBundle\Form\ConsumerShoppingForm;
use UserBundle\Form\MailConsumerForm;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\OrderProduct;

/**
 * @Config\Route("/consumer-profile/{consumer}", name="consumer-profile_")
 * @Config\ParamConverter("subdomain", class="AppBundle:Instance", options={"mapping": {"subdomain": "subdomain"}})
 */
class ConsumerProfileController extends Controller
{
    /**
     * @Config\Route("/{page}", name="index", host="{subdomain}.%host%", requirements={"page": "\d+"}, defaults={"page"=1})
     * @Config\Method({"GET", "POST"})
     */
    public function indexAction(Request $request, Instance $subdomain, Author $consumer, $page) {
        if($consumer->getInstance()->getId() != $subdomain->getId()){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
        /** @var EventManager **/
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConsumerProfileForm::class, null, array(
            'user' => $this->getUser(),
            'method' => 'GET',
            'action' => $this->generateUrl("consumer-profile_list", [
                'subdomain' => $subdomain->getSubdomain(),
                'consumer' => $consumer->getId(),
            ])
        ));

        //pobieranie ostatnio ogladanych produktow

        $connService = $this->get('pixel_connection.service');
        $lastViewed = $connService->getLastViewedProducts($consumer, $subdomain, 6);

        $orders = $em->getRepository(Order::class)->getAllOrders($consumer); /* @var $orders \AppBundle\Entity\OrderRepository */

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $orders,
            $page,
            3
        );

        $pagination->setUsedRoute('consumer-profile_load_next_orders');
        $pagination->setParam('consumer', $consumer->getId());

        return $this->render(
            'UserBundle:ConsumerProfile:index.html.twig',
            array(
                'lastViewedProducts' => $lastViewed,
                'lastBuy' => $pagination,
                'consumer' => $consumer,
                'channelImg' => Channel::$availableTypesIcons,
                'inactive' => Post::getAvailableTypesIcins('inactive'),
                'status' => Author::$availableUserStatus,
                'currency' => Instance::$availableCurrency[$subdomain->getCurrency()],
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Config\Route("/notes-list", name="notes_list", host="{subdomain}.%host%")
     * @Config\Method({"GET"})
     */
    public function notesListAction(Request $request, Instance $subdomain, Author $consumer) {
        if($consumer->getInstance()->getId() != $subdomain->getId()){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
        /** @var EventManager **/
        $em = $this->getDoctrine()->getManager();

        $notes  = $em->getRepository(Note::class)->getListConsumerNotes($consumer);

        return $this->render(
            'UserBundle:ConsumerProfile:notes-box.html.twig',
            array(
                'consumer' => $consumer,
                'notes' => $notes,
            )
        );
    }

    /**
     * @Config\Route("/notes-add", name="notes_add", host="{subdomain}.%host%")
     * @Config\Method({"POST"})
     */
    public function notesAddAction(Request $request, Instance $subdomain, Author $consumer) {
        if($consumer->getInstance()->getId() != $subdomain->getId()){
            throw $this->createAccessDeniedException('You cannot access this page!');
        }

        $text = trim($request->get("text", ''));
        if($text != '') {
            $em = $this->getDoctrine()->getManager();
            $item = new Note();
            $item->setAddDate(new \DateTime());
            $item->setAddUser($this->getUser());
            $item->setAuthor($consumer);
            $item->setContent($text);
            $em->persist($item);
            $em->flush($item);

        }

        return $this->json(['content' => $item->getContent()]);
    }
}