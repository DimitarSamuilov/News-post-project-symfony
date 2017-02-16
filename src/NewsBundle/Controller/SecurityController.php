<?php

namespace NewsBundle\Controller;

use NewsBundle\Entity\Role;
use NewsBundle\Entity\User;
use NewsBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        return $this->render("security/login.html.twig");
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/register",name="security_register")
     */
    public function registerUserAction(Request $request)
    {
        $user=new User();
        $form=$this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $user=$this->prepareUser($user);
            try{
                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

            }catch(\Exception $e){
                $this->get('session')->getFlashBag()->add('error', 'Username or email already taken!');
                return $this->render('security/register.html.twig', ['form' => $form->createView()]);
            }

            return $this->redirectToRoute("homepage");
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @param $user User
     * @return User
     */
    private function prepareUser($user)
    {
        $doctrine = $this->getDoctrine();
        $roleRepo = $doctrine->getRepository(Role::class);
        $userRole = $roleRepo->findOneBy(['name' => 'ROLE_USER']);

        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $user->getPassword());;

        $user->setPassword($password);
        $user->addRoles($userRole);
        return $user;
    }


    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }
}
