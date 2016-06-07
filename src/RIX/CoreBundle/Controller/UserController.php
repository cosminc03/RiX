<?php

namespace RIX\CoreBundle\Controller;

use RIX\CoreBundle\Entity\Favorite;
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
     * @Route("/", name="rix_core_user_home")
     * 
     * @return Response
     */
    public function homeAction()
    {
        return $this->render('CoreBundle:Default:home.html.twig');
    }

    /**
     * @Route("/category/{language}/video/page/{page}", name="rix_core_user_category_type_video")
     * 
     * @return Response
     */
    public function categoryTypeVideoAction($language, $page)
    {
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/tags/" . $language . "/videos?per_page=16&page=" . $page);
        $lastPage = $videos["body"]["paging"]["last"];
        $startPos = strrpos($lastPage, "=");
        $lastPage = substr($lastPage, $startPos + 1);

        return $this->render(
            "CoreBundle:Default:category_type_video.html.twig",
            [
                'language' => $language,
                'videos' => $videos,
                'page' => $page,
                'lastPage' => $lastPage,
            ]);
    }

    /**
     * @Route("/category/{language}/slides/page/{page}", name="rix_core_user_category_type_slide")
     *
     * @return Response
     */
    public function categoryTypeSlideAction($language, $page)
    {
        $offset = ($page - 1) * 16 + 1;
        $slideshare = $this->get('rix_slideshare');
        $slideshares = $slideshare->get_slideTag($language,$offset,16);


        $slidesCount = $slideshares[0]['COUNT'];
        $lastPage = intval($slidesCount/16);

        return $this->render(
            "CoreBundle:Default:category_type_slides.html.twig",
            [
                'language' => $language,
                'slideshares' => $slideshares,
                'page' => $page,
                'lastPage' => $lastPage,
            ]);
    }

    /**
     * @Route("/category/{language}/articles", name="rix_core_user_category_type_article", requirements={"language"=".+"})
     *
     * @return Response
     */
    public function categoryTypeArticleAction($language)
    {
        $feedly = $this->get('rix_feedly');
        $count = 16;
        $res = $feedly->getStreamContent($language, $count, $ranked = NULL, $unreadOnly = NULL, $newerThan = NULL, $cont = NULL, $feedly->_accesToken);
        $nextId = $res['continuation'];
        
        $data = array();
        for($i=0;$i<$count;$i++)
            $data[$i]=date("m-d-Y H:i", $res['items'][$i]['published']/1000);

        return $this->render(
            "CoreBundle:Default:category_type_feed_selected.html.twig",
            [
                'feedId' => $language,
                'res' => $res['items'],
                'data' => $data,
                'continuation' => $res['continuation'],
                'nextId' => $nextId,
            ]);
    }

    /**
     * @Route("/load-more/{api}/{language}/{nextId}", name="rix_core_user_load_more", requirements={"language"=".+"})
     *
     * @return Response
     */
    public function loadMoreAction($api, $language, $nextId)
    {
        if ($api == 'feedly') {
            $feedly = $this->get('rix_feedly');
            $count = 16;
            $res = $feedly->getStreamContent($language, $count, $ranked = NULL, $unreadOnly = NULL, $newerThan = NULL, $nextId, $feedly->_accesToken);
            $nextId = $res['continuation'];
            $data = array();
            for($i=0;$i<$count;$i++)
                $data[$i]=date("m-d-Y H:i", $res['items'][$i]['published']/1000);

            return $this->render(
                "CoreBundle:Default:more_feedly_items.html.twig",
                [
                    'feedId' => $language,
                    'res' => $res['items'],
                    'data' => $data,
                    'continuation' => $res['continuation'],
                    'nextId' => $nextId,
                ]);
        }

    }

    /**
     * @Route("/category/{language}/{type}", defaults={"type" = "video"}, name="rix_core_user_category")
     * 
     * @return Response
     */
    public function categoryAction($language, $type)
    {
        if ($type == "video") {
            $page = 1;
            $vimeo = $this->get('rix_vimeo');
            $videos = $vimeo->request("/tags/" . $language . "/videos?per_page=16&page=".$page);
            $lastPage = $videos["body"]["paging"]["last"];
            $startPos = strrpos($lastPage, "=");
            $lastPage = substr($lastPage, $startPos + 1);

            return $this->render(
                "CoreBundle:Default:category_type_video.html.twig",
                [
                    'language' => $language,
                    'videos' => $videos,
                    'page' => $page,
                    'lastPage' => $lastPage,
                ]);
        } elseif ($type == "slides") {
            $page = 1;
            $slideshare = $this->get('rix_slideshare');
            $slideshares = $slideshare->get_slideTag($language,0,16);
            
            $slidesCount = $slideshares[0]['COUNT'];
            $lastPage = intval($slidesCount/16);

            return $this->render(
                "CoreBundle:Default:category_type_slides.html.twig",
                [
                    'language' => $language,
                    'slideshares' => $slideshares,
                    'page' => $page,
                    'lastPage' => $lastPage,
                ]);
        } else {
            $feedly = $this->get('rix_feedly');
            $feeds = $feedly->searchFeeds($language,32,"en_EN",$feedly->_accesToken);

            return $this->render(
                "CoreBundle:Default:category_type_article.html.twig",
                [
                    'articles' => $feeds['results'],
                    'language' => $language,
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
    public function loginAction()
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
     * @Route("/category/{language}/{api}/{id}", name="rix_core_user_category_detail", requirements={"id"=".+"})
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
        elseif ($api == 'vimeo') {
            $vimeo = $this->get('rix_vimeo');
            $video = $vimeo->requestId("/videos/". $id);
            return $this->render(
                "CoreBundle:FreeUser:detailed_vimeo.html.twig",
                [
                    'api' => $api,
                    'vimeo' => $video,
                ]);
        } else {
            $feedly = $this->get('rix_feedly');
            $article = $feedly->getEntries($id,$feedly->_accesToken);

            if(array_key_exists('updated', $article['0']))
                $data=date("m-d-Y H:i", $article['0']['updated']/1000);
            else
                $data=date("m-d-Y H:i", $article['0']['published']/1000);

            return $this->render(
                "CoreBundle:FreeUser:detailed_feedly.html.twig",
                [
                    'api' => $api,
                    'article' => $article[0],
                    'data'=> $data,
                ]);
        }
    }

    /**
     * @Route("/my-courses", name="rix_core_user_my_courses")
     * @return Response
     */
    public function myCoursesAction()
    {
        $vimeo = $this->get('rix_vimeo');
        $em = $this->getDoctrine()->getManager();

        /** @var Favorite[] $videos */
        $videos = $em
            ->getRepository(Favorite::class)
            ->findBy([
                'user' => $this->getUser(),
                'apiType' => 'video',
            ]);
        
        $vimeoVideos = array();
        $iterator = 0;
        foreach ($videos as $video) {
            $vimeoVideos[$iterator] = $vimeo->requestId("/videos/". $video->getApiKey());
            $vimeoVideos[$iterator]['topic'] = $video->getTopic();
            $iterator++;
        }
        
        return $this->render(
            "CoreBundle:Default:my_courses.html.twig",
            [
                'videos' => $vimeoVideos,
            ]
        );
    }

    /**
     * @Route("/get/{type}", name="rix_core_user_get_language_type")
     *
     * @return Response
     */
    public function filterByTypeAction($type)
    {
        if ($type == 'article') {
            $feedly = $this->get('rix_feedly');

            $em = $this->getDoctrine()->getManager();

            /** @var Favorite[] $articles */
            $articles = $em
                ->getRepository(Favorite::class)
                ->findBy([
                    'user' => $this->getUser(),
                    'apiType' => 'article',
                ]);

            $articlesFeedly = array();
            $data = array();
            $iterator = 0;
            foreach ($articles as $article) {
                $articlesFeedly[$iterator] = $feedly->getEntries($article->getApiKey(), $feedly->_accesToken);
                $data[$iterator]=date("m-d-Y H:i", $articlesFeedly[$iterator][0]['published']/1000);
                $iterator++;
            }

            return $this->render(
                "CoreBundle:Default:get_articles.html.twig",
                [
                    'res' => $articlesFeedly,
                    'data' => $data,
                ]);

        } elseif ($type == 'slide') {
            $slideshare = $this->get('rix_slideshare');
            $em = $this->getDoctrine()->getManager();

            /** @var Favorite[] $slides */
            $slides = $em
                ->getRepository(Favorite::class)
                ->findBy([
                    'user' => $this->getUser(),
                    'apiType' => 'slide',
                ]);

            $slidesArr = array();
            $iterator = 0;
            foreach ($slides as $slide) {
                $slidesArr[$iterator] = $slideshare->get_slideInfo($slide->getApiKey());
                $slidesArr[$iterator]['ID'] = $slide->getApiKey();
                $slidesArr[$iterator]['topic'] = $slide->getTopic();
                $iterator++;
            }

            return $this->render(
                "CoreBundle:Default:get_slideshares.html.twig",
                [
                    'slideshares' => $slidesArr,
                ]);
        } else {
            $vimeo = $this->get('rix_vimeo');
            $em = $this->getDoctrine()->getManager();

            /** @var Favorite[] $videos */
            $videos = $em
                ->getRepository(Favorite::class)
                ->findBy([
                    'user' => $this->getUser(),
                    'apiType' => 'video',
                ]);

            $vimeoVideos = array();
            $iterator = 0;
            foreach ($videos as $video) {
                $vimeoVideos[$iterator] = $vimeo->requestId("/videos/". $video->getApiKey());
                $vimeoVideos[$iterator]['topic'] = $video->getTopic();
                $iterator++;
            }

            return $this->render(
                "CoreBundle:Default:get_vimeo.html.twig",
                [
                    'videos' => $vimeoVideos,
                ]);
        }
    }

    /**
     * @Route("/favorite/add/{language}/{type}/{key}", name="rix_core_user_favorite_add", requirements={"key"=".+"})
     *
     * @return Response
     */
    public function addToFavoriteAction($language, $type, $key)
    {
        $em = $this->getDoctrine()->getManager();
        $favorite = new Favorite();
        $favorite->setUser($this->getUser());
        $favorite->setTopic($language);
        $favorite->setApiKey($key);
        $favorite->setApiType($type);

        $em->persist($favorite);
        $em->flush();

        return new Response('New course added');
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
