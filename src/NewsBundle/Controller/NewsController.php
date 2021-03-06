<?php

namespace NewsBundle\Controller;

use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use NewsBundle\Entity\News;
use NewsBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NewsController
 * @package NewsBundle\Controller
 * @Route("/news")
 */
class NewsController extends Controller
{

    /**
     * @Route("/create",name="news_create")
     */
    public function createAction(Request $request)
    {
        $news=new News();
        $form=$this->createForm(NewsType::class,$news);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            if($this->getUser()!=null){
                $news->setUser($this->getUser());
            }
            try{
                $file=$news->getImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $directory = $this->getParameter('kernel.root_dir') . '\..\web\images';
                $file->move($directory,$fileName);
                $news->setImage('images'.DIRECTORY_SEPARATOR.$fileName);
                $em=$this->getDoctrine()->getManager();
                $em->persist($news);
                $em->flush();
            }catch (Exception $e){
                $this->get('session')->getFlashBag()->add('error', 'Error occurred!');
                return $this->render('news/create.html.twig', ['form' => $form->createView()]);
            }
            return $this->redirectToRoute('homepage');

        }
        return $this->render("news/create.html.twig",['form'=>$form->createView()]);
    }

    /**
     * @Route("/list",name="news_list")
     */
    public function listNewsAction()
    {

        $allNews=$this->getDoctrine()->getRepository(News::class)->findBy([],['posted'=>'DESC'],10);
        return $this->render("news/list.html.twig",['news'=>$allNews]);
    }


    /**
     * @param $id
     * @return RedirectResponse
     * @Route("/single/{id}",name="view_single_news")
     */
    public function viewSingle($id)
    {
        $news=$this->getDoctrine()->getRepository(News::class)->find($id);
        if($news==null){
            return $this->redirectToRoute("news_list");
        }

        return $this->render("news/viewSingle.html.twig",['single'=>$news]);

    }
}
