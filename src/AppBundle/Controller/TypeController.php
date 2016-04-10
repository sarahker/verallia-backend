<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Type;
use AppBundle\Form\TypeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TypeController extends Controller
{
    public function indexAction()
    {
        $listTypes = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Type')
            ->findAll()
        ;
        return $this->render('AppBundle:Type:index.html.twig', array(
            'listTypes' => $listTypes
        ));
    }
    public function viewAction($id)
    {
        $type = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Type')
            ->find($id)
        ;
        return $this->render('AppBundle:Type:view.html.twig', array(
            'type' => $type
        ));
    }

    public function addAction(Request $request)
    {
        $type = new Type();
        $form = $this->get('form.factory')->create(TypeType::class, $type);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Type bien enregistré.');
            return $this->redirect($this->generateUrl('type_home'));
        }
        return $this->render('AppBundle:Type:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $type = $em->getRepository('AppBundle:Type')->find($id);

        // Si le type n'existe pas, on affiche une erreur 404
        if ($type == null) {
            throw $this->createNotFoundException("Le type d'id " . $id . " n'existe pas.");
        }
        $form = $this->get('form.factory')->create(new TypeType, $type);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Type bien enregistré.');
            return $this->redirect($this->generateUrl('type_view', array('id' => $type->getId())));
        }
        return $this->render('AppBundle:Type:edit.html.twig', array(
            'form' => $form->createView(), 'type' => $type
        ));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $type = $em->getRepository('AppBundle:Type')->find($id);
        if (null === $type) {
            throw new NotFoundHttpException("Le type d'id " . $id . " n'existe pas.");
        }
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $em->remove($type);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', "Le type a bien été supprimée.");
            return $this->redirect($this->generateUrl('type_home'));
        }
        return $this->render('AppBundle:Type:delete.html.twig', array(
            'type' => $type,
            'form'   => $form->createView()
        ));
    }
}