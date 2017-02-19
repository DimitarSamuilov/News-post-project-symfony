<?php

namespace NewsBundle\Controller;

use NewsBundle\Entity\News;
use NewsBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
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
        $originalImage=$news->getImage();
        $file=new File(
            $this->getParameter('kernel.root_dir')
            .DIRECTORY_SEPARATOR.'..'
            .DIRECTORY_SEPARATOR.'web'
            .DIRECTORY_SEPARATOR.
            $news->getImage()
        );
        $news->setImage($file);
        $news->setImage(null);
        $form=$this->createForm(NewsType::class,$news);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            try{
                if($news->getImage()!==null) {
                    $file = $news->getImage();
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $directory = $this->getParameter('kernel.root_dir') . '\..\web\images';
                    $file->move($directory, $fileName);
                    $news->setImage('images' . DIRECTORY_SEPARATOR . $fileName);
                }
                else{
                    $news->setImage($originalImage);
                }
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

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/change/{id}",name="change_active_state")
     */
    public function changeActiveState($id)
    {
        $news=$this->getDoctrine()->getRepository(News::class)->find($id);
        if($news==null) {
            return $this->redirectToRoute("admin_news_list");
        }
        if($news->isActive()){
            $news->setActive(false);
        }else{
            $news->setActive(true);
        }
        $em=$this->getDoctrine()->getManager();
        $em->persist($news);
        $em->flush();
        return $this->redirectToRoute("admin_news_list");

    }
}
