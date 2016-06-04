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
        $slideshare = $this->get('rix_slideshare');
        $slideshares = $slideshare->get_slideTag($language,0,8);
        
        return $this->render(
            "CoreBundle:FreeUser:category_selected.html.twig",
            [
                'language' => $language,
                'videos' => $videos,
                'slideshares' => $slideshares,
            ]);
    }
    /**
     * @Route("/free/category/{language}/{api}/{id}", name="rix_core_free_user_category_detail")
     */
    public function showDetailAction($language,$api,$id){

        if ($api == 'slide') {
            $slideshare = $this->get('rix_slideshare');
            $slideinfo = $slideshare->get_slideInfo($id);


            return $this->render(
                "CoreBundle:FreeUser:detailed_slideshare.html.twig",
                [
                    'api' => $api,
                    'slideinfo' => $slideinfo,
                ]);
        } elseif ($api == 'vimeo'){
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
    
}