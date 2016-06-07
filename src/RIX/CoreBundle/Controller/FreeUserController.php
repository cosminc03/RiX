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
     * @Route("/free/category/{language}/", name="rix_core_free_user_category")
     */
    public function categoryAction($language)
    {
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/tags/". $language ."/videos", array('per_page' => 8), 'GET');
        $slideshare = $this->get('rix_slideshare');
        $slideshares = $slideshare->get_slideTag($language,0,8);
        $feedly = $this->get('rix_feedly');
        $articles = $feedly->searchFeeds($language,8,"en_EN",$feedly->_accesToken);

          
        return $this->render(
            "CoreBundle:FreeUser:category_selected.html.twig",
            [
                'language' => $language,
                'videos' => $videos,
                'slideshares' => $slideshares,
                'articles' => $articles['results'],
            ]);
    }


    /**
     * @Route("free/category/{language}/articles/{id}", name="rix_core_free_user_category_type_article", requirements={"id"=".+"})
     *
     * @return Response
     */
    public function categoryFreeTypeArticleAction($language,$id)
    {
        $feedly = $this->get('rix_feedly');
        $count = 16;
        $res = $feedly->getStreamContent($id, $count, $ranked = NULL, $unreadOnly = NULL, $newerThan = NULL, $cont = NULL, $feedly->_accesToken);
        $nextId = $res['continuation'];

        $data = array();
        for($i=0;$i<$count;$i++)
            $data[$i]=date("Y-m-d ", $res['items'][$i]['published']/1000);

        return $this->render(
            "CoreBundle:FreeUser:free_category_get_feed.html.twig",
            [

                'language' => $language,
                'res' => $res['items'],
                'data' => $data,
                'continuation' => $res['continuation'],
                'nextId' => $nextId,
            ]);
    }
    /**
     * @Route("/free/category/{language}/{api}/{id}", name="rix_core_free_user_category_detail" , requirements={"id"=".+"} )
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

        else {
            $feedly = $this->get('rix_feedly');
            $article = $feedly->getEntries($id, $feedly->_accesToken);
         

                $data = date("Y-m-d ", $article['0']['published'] / 1000);

            return $this->render(
                "CoreBundle:FreeUser:detailed_feedly.html.twig",
                [
                    'api' => $api,
                    'article' => $article[0],
                    'data' => $data,
                ]);
        }
    }
    
}