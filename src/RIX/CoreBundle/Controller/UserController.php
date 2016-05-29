<?php

namespace RIX\CoreBundle\Controller;

use RIX\CoreBundle\Form\User\ChangeEmailTypeForm;
use RIX\CoreBundle\Form\User\ChangePasswordTypeForm;
use RIX\CoreBundle\Form\User\RegisterTypeForm;
use RIX\CoreBundle\Form\User\UserAccountTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use RIX\CoreBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/free/home", name="rix_core_user_free_home")
     */
    public function freeHomeAction()
    {
        return $this->render('CoreBundle:Default:index.html.twig');
    }

    /**
     * @Route("/free/category/{language}", name="rix_core_user_free_category")
     */
    public function freeCategoryAction($language)
    {
        return $this->render(
            "CoreBundle:Default:category_selected.html.twig",
            [
                'language' => $language,
            ]);
    }

    /**
     * @Route("/home", name="rix_core_user_home")
     */
    public function homeAction()
    {
        $user = $this->getUser();
        
       return $this->render(
           'CoreBundle:Default:index.html.twig',
            [
               'user' => $user,
            ]
       );
    }

    /**
     * @Route("/register", name="rix_core_user_register")
     *
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $user = new User;
        $form = $this->createForm(RegisterTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('rix_core_user_account');
        }
        return $this->render(
            'CoreBundle:Default:register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/login", name="rix_core_user_login")
     * 
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $this->getUser();

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render(
            'CoreBundle:Default:login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @Route("/logout", name="rix_core_user_logout")
     */
    public function logoutAction()
    {
        $this->get('security.token_storage')->setToken(null);
        $this->get('request')->getSession()->invalidate();
    }

    /**
     * @Route("/account", name="rix_core_user_account")
     *
     * @var Request $request
     * @return Response
     */
    public function mainAccountAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserAccountTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render(
            'CoreBundle:Default:main_account.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/category-selected", name="core_user_category_selected")
     *
     * @return Response
     */
    public function categorySelectedAction()
    {
        return $this->render(
            'CoreBundle:Default:category_selected.html.twig',
            [
                'user' => $this->getUser(),
            ]);
    }

    /**
     * @Route("/change-email", name="rix_core_user_change_email")
     *
     * @param Request $request
     * @return Response
     */
    public function changeEmailAction(Request $request)
    {
        $valid = false;
        $user = $this->getUser();
        $form = $this->createForm(ChangeEmailTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->eraseOldPassword();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $valid = true;
        }

        return $this->render(
            '@Core/Default/change_email.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'valid' => $valid,
            ]
        );
    }

    /**
     * @Route("/change-password", name="rix_core_user_change_password")
     * 
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $valid = false;
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordTypeForm::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
            $user->eraseOldPassword();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $valid = true;
        }
        
        return $this->render(
            '@Core/Default/change_password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
                'valid' => $valid,
            ]
        );
    }
    
    /**
     * @Route("/success", name="rix_core_success")
     * 
     * @return Response
     */
    public function succesAction()
    {
        return $this->render('CoreBundle:Default:success.html.twig');
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }
}
