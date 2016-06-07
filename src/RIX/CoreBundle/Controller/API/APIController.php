<?php

namespace RIX\CoreBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


class APIController extends Controller{

    private function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::utf8_encode_deep($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                self::utf8_encode_deep($input->$var);
            }
        }
    }
    /**
     * @Route("/api/get_article_by_tag/{tag}")
     * @Method("GET")
     */
    public function get_articles_by_tag($tag)
    {

        $feedly = $this->get('rix_feedly');
        $articles = $feedly->searchFeeds($tag, 3,"en_EN", $feedly->_accesToken);
        $this->utf8_encode_deep($articles);
        $response = new Response(json_encode(array('name' => $articles)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/api/get_videos_by_tag/{tag}/{per_page}/{page}")
     * @Method("GET")
     */
    public function get_videos_by_tag($tag,$per_page=16,$page=1){
        $tag = str_replace(' ','%20',$tag);
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/tags/" . $tag. "/videos?".$per_page."&page=" . $page);

        $this->utf8_encode_deep($videos);
        $response = new Response(json_encode(array('name' => $videos)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/get_videos_by_user/{user}")
     * @Method("GET")
     */
    public function get_videos_by_user($user){
        $user = str_replace(' ','%20',$user);
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/users/".$user);
        $this->utf8_encode_deep($videos);
        $response = new Response(json_encode(array('name' => $videos)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/get_videos_by_id/{id}")
     * @Method("GET")
     */
    public function get_videos_by_id($id){
        $vimeo = $this->get('rix_vimeo');
        $videos = $vimeo->request("/videos/".$id);
        $this->utf8_encode_deep($videos);
        $response = new Response(json_encode(array('name' => $videos)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/get_slideshows_by_tag/{tag}/{page}/{limit}")
     * @Method("GET")
     */
    public function get_slideshows_by_tag($tag, $page = 1, $limit = 1)
    {
        $tag = str_replace(' ', '-', $tag);
        $offset = ($page - 1) * $limit + 1;
        $slideshare = $this->get('rix_slideshare');
        $res = $slideshare->XMLtoArray($slideshare->get_data("get_slideshows_by_tag", "&tag=$tag&offset=$offset&limit=$limit&detailed=1"));
        if($res != null){
            $response = new Response(json_encode(array('name' => $res)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else return new Response('Slideshows by tag not found');
    }
    /**
     * @Route("/api/get_slideshows_by_user/{user}/{page}/{limit}")
     * @Method("GET")
     */
    public function get_slideshows_by_user($user,$page=1,$limit=1){
        $offset = ($page - 1) * $limit + 1;
        $slideshare = $this->get('rix_slideshare');
        $res = $slideshare->XMLtoArray($slideshare->get_data("get_slideshows_by_user","&username_for=$user&offset=$offset&limit=$limit"));
        if($res != null){
            $response = new Response(json_encode(array('name' => $res)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else return new Response('Slideshows by user not found');
    }
    /**
     * @Route("/api/get_slideshows_by_id/{id}")
     * @Method("GET")
     */
    public function get_slideshows_by_id($id){
        $slideshare = $this->get('rix_slideshare');
        $res = $slideshare->XMLtoArray($slideshare->get_data("get_slideshow","&slideshow_id=$id&detailed=1"));
        if($res != null){
            $response = new Response(json_encode(array('name' => $res)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else return new Response('Slideshow by tag not found');
    }
}