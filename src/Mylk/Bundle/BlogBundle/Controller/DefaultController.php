<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class DefaultController extends Controller
    {
        public function indexAction(){
            return $this->render("MylkBlogBundle:Default:index.html.twig");
        }
        
        public function postViewAction(){
            return $this->render("MylkBlogBundle:Default:post.html.twig", array("post" => array("id" => 1, "title" => "post a")));
        }
    }