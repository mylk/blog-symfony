<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response as HttpResponse;

    class DefaultController extends Controller
    {
        public function indexAction(){
            $em = $this->getDoctrine()->getManager();
            $pageGlobals = $this->container->getParameter("pageGlobals");
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
            $em = $this->getDoctrine()->getManager();
            $pageGlobals = $this->container->getParameter("pageGlobals");
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            
            $postId = $this->getRequest()->get("postid");
            $post = $em->getRepository("MylkBlogBundle:Post")->find($postId);
            
            return $this->render("MylkBlogBundle:Default:post.html.twig", array(
                "pageGlobals" => $pageGlobals,
                "categories" => $categories,
                "post" => $post
            ));
        }
        
        public function searchAction(){
            return new HttpResponse("@TODO");
        }
    }