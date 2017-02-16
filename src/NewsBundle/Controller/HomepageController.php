<?php

namespace NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
    /**
     * @Route("/",name="homepage")
     */
   public function homepageAction()
   {
       return $this->render("homepage/index.html.twig");
   }
}
