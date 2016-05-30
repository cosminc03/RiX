<?php

namespace RIX\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FreeUserController extends Controller
{
    /**
     * @Route("/free", name="rix_core_free_user_home")
     */
    public function homeAction()
    {
        return $this->render('CoreBundle:Default:home.html.twig');
    }

    /**
     * @Route("/free/category/{language}", name="rix_core_free_user_category")
     */
    public function categoryAction($language)
    {
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/tags/". $language ."/videos", array('per_page' => 8), 'GET');
        $user = $this->getUser();

        return $this->render(
            "CoreBundle:FreeUser:category_selected.html.twig",
            [
                'language' => $language,
                'user' => $user,
                'videos' => $videos,
            ]);
    }
}