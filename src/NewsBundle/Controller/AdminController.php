<?php

namespace NewsBundle\Controller;

use NewsBundle\Entity\News;
use NewsBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AdminController
 * @package NewsBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @Route("/news/list",name="admin_news_list")
     */
    public function allNewsAction()
    {
        $allNews=$this->getDoctrine()->getRepository(News::class)->findAll();

        return $this->render("admin/news/list.html.twig",['news'=>$allNews]);
    }

    /**
     * @Route("/news/edit/{id}",name="admin_news_edit")
     */
    public function editNews($id,Request $request)
    {
        $news=$this->getDoctrine()->getRepository(News::class)->find($id);
        if($news==null){
            return $this->redirectToRoute("admin_news_list");
        }
        $form=$this->createForm(NewsType::class,$news);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            try{
                $em=$this->getDoctrine()->getManager();
                $em->persist($news);
                $em->flush();
            }catch (\Exception $e){
                $this->get('session')->getFlashBag()->add('error', 'Error occurred!');
                return $this->render('/admin/news/edit.html.twig', ['form' => $form->createView()]);
            }
            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render("/admin/news/edit.html.twig",['form'=>$form->createView(),'news'=>$news]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/news/delete/{id}",name="admin_news_delete")
     */
    public function deleteNews($id)
    {
        $newsToDelete=$this->getDoctrine()->getRepository(News::class)->find($id);
        if($newsToDelete==null){
            return $this->redirectToRoute('admin_news_list');
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($newsToDelete);
        $em->flush();

        return $this->redirectToRoute('admin_news_list');
    }
}
