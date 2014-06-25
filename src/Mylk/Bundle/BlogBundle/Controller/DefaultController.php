<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response as HttpResponse;

    class DefaultController extends Controller
    {
        public function indexAction(){
            $pageGlobals = $this->container->getParameter("pageGlobals");
            $em = $this->getDoctrine()->getManager();
            $paginator = $this->get("knp_paginator");
            
            $posts = $em->getRepository("MylkBlogBundle:Post")->findBy(array(), array("createdAt" => "DESC"));
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            
            $pagination = $paginator->paginate(
                $posts,
                $this->get('request')->query->get("page", 1),
                5
            );

            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "pageGlobals" => $pageGlobals,
                "categories" => $categories,
                "pagination" => $pagination
            ));
        }
        
        public function postViewAction(){
            $pageGlobals = $this->container->getParameter("pageGlobals");
            
            return $this->render("MylkBlogBundle:Default:post.html.twig", array("pageGlobals" => $pageGlobals, "post" => array("id" => 1, "title" => "post a")));
        }
        
        public function searchAction(){
            return new HttpResponse("@TODO");
        }
    }