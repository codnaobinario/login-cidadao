<?php

namespace PROCERGS\LoginCidadao\NotificationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\LoginCidadao\NotificationBundle\Handler\NotificationHandlerInterface;
use PROCERGS\LoginCidadao\NotificationBundle\Form\SettingsType;
use PROCERGS\LoginCidadao\NotificationBundle\Model\NotificationIterable;
use PROCERGS\LoginCidadao\NotificationBundle\Model\ClientNotificationIterable;
use PROCERGS\LoginCidadao\CoreBundle\Helper\GridHelper;
use PROCERGS\OAuthBundle\Model\ClientInterface;

class NotificationController extends Controller
{

    /**
     * @Route("/notifications/{id}", requirements={"id" = "\d+"}, defaults={"id" = null}, name="lc_notifications")
     * @Template()
     */
    public function indexAction($id = null)
    {
        if (null !== $id) {
            $openId = $id;
            $offset = $id + 1;
        } else {
            $offset = null;
        }
        $grid = $this->getNotificationGrid($offset)->createView($this->getRequest());

        return compact('grid', 'openId');
    }

    /**
     * @Route("/client/{clientId}/notifications/{id}", requirements={"id" = "\d+", "clientId" = "\d+"}, defaults={"id" = null}, name="lc_notifications_from_client")
     * @Template("PROCERGSLoginCidadaoNotificationBundle:Notification:index.html.twig")
     */
    public function getFromClientAction($clientId, $id = null)
    {
        if (null !== $id) {
            $openId = $id;
            $offset = $id + 1;
        } else {
            $offset = null;
        }

        $client = $this->getDoctrine()->getRepository('PROCERGSOAuthBundle:Client')
            ->find($clientId);

        $grid = $this->getNotificationGrid($offset, $client)->createView($this->getRequest());

        return compact('grid', 'openId');
    }

    /**
     * @Route("/notifications/clients/settings/{clientId}", requirements={"clientId" = "\d+"}, defaults={"clientId" = null}, name="lc_notifications_settings")
     * @Template()
     */
    public function editSettingsAction(Request $request, $clientId = null)
    {
        $person = $this->getUser();
        if (null !== $clientId) {
            $client = $this->getDoctrine()
                ->getRepository('PROCERGSOAuthBundle:Client')
                ->find($clientId);
            if (!$client instanceof ClientInterface || !$person->hasAuthorization($client)) {
                throw new AccessDeniedHttpException();
            }
        } else {
            $client = null;
        }

        $handler = $this->getNotificationHandler()
            ->getAuthenticatedHandler($person);
        $handler->initializeSettings();

        $form = $this->createForm(new SettingsType(),
                                  $handler->getGroupedSettings($client));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $form->getData()->persist($em);
            $em->flush();
            return $this->redirect($this->generateUrl('lc_notifications_settings'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/notifications/navbar/fragment/{offset}", requirements={"offset" = "\d+"}, defaults={"offset" = 0}, name="lc_notifications_navbar_fragment")
     * @Template()
     */
    public function getNavbarFragmentAction(Request $request, $offset = 0)
    {
        $notifications = $this->getDoctrine()->getManager()
            ->getRepository('PROCERGSLoginCidadaoNotificationBundle:Notification')
            ->findNextNotifications($this->getUser(), 8, $offset);

        return compact('notifications');
    }

    /**
     * @Route("/notifications/grid/fragment/{offset}", requirements={"offset" = "\d+"}, defaults={"offset" = 0}, name="lc_notifications_grid_fragment")
     * @Template("PROCERGSLoginCidadaoNotificationBundle:Notification:grid.html.twig")
     */
    public function getGridFragmentAction(Request $request, $offset = 0)
    {
        $grid = $this->getNotificationGrid($offset)->createView($request);

        return compact('grid');
    }

    /**
     * @Route("/client/{clientId}/notifications/grid/fragment/{offset}", requirements={"offset" = "\d+", "clientId" = "\d+"}, defaults={"offset" = 0}, name="lc_client_notifications_grid_fragment")
     * @Template("PROCERGSLoginCidadaoNotificationBundle:Notification:grid.html.twig")
     */
    public function getGridClientFragmentAction(Request $request, $clientId,
                                                $offset = 0)
    {
        $client = $this->getDoctrine()->getRepository('PROCERGSOAuthBundle:Client')
            ->find($clientId);
        $grid = $this->getNotificationGrid($offset, $client)->createView($request);

        return compact('grid');
    }

    /**
     * @return NotificationHandlerInterface
     */
    private function getNotificationHandler()
    {
        return $this->get('procergs.notification.handler');
    }

    private function getNotificationGrid($offset = 0,
                                         ClientInterface $client = null)
    {
        $handler = $this->getNotificationHandler()
            ->getAuthenticatedHandler($this->getUser());
        if ($client instanceof ClientInterface) {
            $iterator = new ClientNotificationIterable($handler, $client, 10,
                                                       $offset);
        } else {
            $iterator = new NotificationIterable($handler, 10, $offset);
        }

        $grid = new GridHelper($iterator);
        $grid->setId('notificationInfiniteGrid');
        $grid->setPerPage(10);
        $grid->setMaxResult(10);
        $grid->setInfiniteGrid(true);

        $routeParams = array('offset');
        if ($client instanceof ClientInterface) {
            $grid->setRoute('lc_client_notifications_grid_fragment');
            $routeParams[] = 'clientId';
        } else {
            $grid->setRoute('lc_notifications_grid_fragment');
        }
        $grid->setRouteParams($routeParams);

        return $grid;
    }

    /**
     * @Route("/notifications/sidebar", name="lc_notifications_sidebar")
     * @Template()
     */
    public function sidebarAction()
    {
        $handler = $this->getNotificationHandler()
            ->getAuthenticatedHandler($this->getUser());

        $clients = $handler->countUnreadByClient();
        return compact('clients');
    }

    /**
     * @Route("/notifications/navbar/{offset}", requirements={"offset" = "\d+"}, defaults={"offset" = 0}, name="lc_notifications_navbar")
     * @Template()
     */
    public function navbarAction(Request $request, $offset = 0)
    {
        $grid = $this->getNotificationGrid($offset)
            ->setPerPage(5)
            ->setMaxResult(5)
            ->setRoute('lc_notifications_navbar')
            ->createView($request);

        return compact('grid');
    }

}
