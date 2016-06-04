<?php

namespace RIX\CoreBundle\Controller;

use RIX\CoreBundle\Service\SlideShare\Helper;
use RIX\CoreBundle\Form\User\ChangeEmailTypeForm;
use RIX\CoreBundle\Form\User\ChangePasswordTypeForm;
use RIX\CoreBundle\Form\User\RegisterTypeForm;
use RIX\CoreBundle\Form\User\UserAccountTypeForm;
use RIX\CoreBundle\Service\Vimeo\Vimeo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use RIX\CoreBundle\Entity\User;

class UserController extends Controller
{

    /**
     * @Route("/", name="rix_core_user_home")
     * 
     * @return Response
     */
    public function homeAction()
    {
        return $this->render('CoreBundle:Default:home.html.twig');
    }

    /**
     * @Route("/category/{language}/{api}/page/{page}", name="rix_core_user_category_by_page")
     *
     * @return Response
     */
    public function categoryByPageAction($language, $api, $page)
    {
        //var_dump("/tags/". $language ."/videos?page=".$page);
        //die();
        $vimeo = $this->get('rix_vimeo');
        //$videos = $vimeo->request("/tags/". $language ."/videos?page=".$page, array('per_page' => 16), 'GET');
        $videos = $vimeo->request("/tags/java/videos?per_page=16&page=".$page);
        $lastPage = $videos["body"]["paging"]["last"];
        //var_dump($lastPage);
        $startPos = strrpos($lastPage, "=");
        $lastPage = substr($lastPage, $startPos + 1);

       // var_dump($lastPage);
       // die();
        return $this->render(
            "CoreBundle:Default:category_selected.html.twig",
            [
                'language' => $language,
                'videos' => $videos,
                'page' => $page,
                'lastPage' => $lastPage,
            ]);
    }

    /**
     * @Route("/category/{language}", name="rix_core_user_category")
     * 
     * @return Response
     */
    public function categoryAction($language)
    {
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/tags/". $language ."/videos", array('per_page' => 16), 'GET');
        $lastPage = $videos["body"]["paging"]["last"];
        $startPos = strrpos($lastPage, "=");
        $lastPage = substr($lastPage, $startPos + 1);

        $slideshare = $this->get('rix_slideshare');
        $slideshares = $slideshare->get_slideTag($language,0,16);

        return $this->render(
            "CoreBundle:Default:category_selected.html.twig",
            [
                'language' => $language,
                'videos' => $videos,
                'slideshares' => $slideshares,
                'lastPage' => $lastPage,
            ]);
    }

    /**
     * @Route("/get/{language}/{type}", name="rix_core_user_get_language_type")
     * 
     * @return Response
     */
    public function filterByTypeAction($language, $type)
    {
        if ($type == 'article') {
            return $this->render(
                "CoreBundle:Default:get_articles.html.twig",
                [
                    'language' => $language,
                ]);

        } elseif ($type == 'slides') {
            $slideshare = $this->get('rix_slideshare');
            $slideshares = $slideshare->get_slideTag($language,0,16);

            return $this->render(
                "CoreBundle:Default:get_slideshares.html.twig",
                [
                    'language' => $language,
                    'slideshares' => $slideshares,
                ]);
        } else {
            $vimeo = $this->get('rix_vimeo');
            $videos = $vimeo->request("/tags/". $language ."/videos", array('per_page' => 16), 'GET');

            return $this->render(
                "CoreBundle:Default:get_vimeo.html.twig",
                [
                    'language' => $language,
                    'videos' => $videos,
                ]);
        }
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
        $vimeo = new Vimeo;
        $videos = $vimeo->request("/tags/hello/videos");
        $user = $this->getUser();
       
        $api = new Helper;
        $res = $api->get_slideTag('java',0,10);
        return $this->render(
            'CoreBundle:Default:category_selected.html.twig',
            [
                'res' => $res,
                'video' => $videos,
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
     * @Route("/category/{language}/{api}/{id}", name="rix_core_user_category_detail")
     */
    public function showDetailAction($language,$api,$id){

        if($api == 'slide') {
            $slideshare = $this->get('rix_slideshare');
            $slideinfo = $slideshare->get_slideInfo($id);


            return $this->render(
                "CoreBundle:FreeUser:detailed_slideshare.html.twig",
                [
                    'api' => $api,
                    'slideinfo' => $slideinfo,
                ]);
        }
        elseif($api == 'vimeo'){
            $vimeo = $this->get('rix_vimeo');
            $video = $vimeo->requestId("/videos/". $id);
            return $this->render(
                "CoreBundle:FreeUser:detailed_vimeo.html.twig",
                [
                    'api' => $api,
                    'vimeo' => $video,
                ]);
        }
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
