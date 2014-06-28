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
            
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));

            // get posts by sticky and creation date order
            $repo = $this->getDoctrine()->getRepository("MylkBlogBundle:Post");
            $posts = $repo->findAllByStickyAndDate();
            
            $pagination = $paginator->paginate(
                $posts,
                $this->getRequest()->get("page", 1),
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
        
        public function categoryViewAction(){
            $em = $this->getDoctrine()->getManager();
            $pageGlobals = $this->container->getParameter("pageGlobals");
            $paginator = $this->get("knp_paginator");
            
            $categoryId = $this->getRequest()->get("categoryid");
            $posts = $em->getRepository("MylkBlogBundle:Post")->findBy(array("category" => $categoryId));
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            
            $pagination = $paginator->paginate(
                $posts,
                $this->getRequest()->get("page", 1),
                5
            );
            
            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "pageGlobals" => $pageGlobals,
                "categories" => $categories,
                "pagination" => $pagination
            ));
        }
        
        public function tagViewAction(){
            $em = $this->getDoctrine()->getManager();
            $pageGlobals = $this->container->getParameter("pageGlobals");
            $paginator = $this->get("knp_paginator");
            
            $tagId = $this->getRequest()->get("tagid");
            $posts = $em->getRepository("MylkBlogBundle:Post")->findBy(array("tag" => $tagId));
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $pagination = $paginator->paginate(
                $posts,
                $this->getRequest()->get("page", 1),
                5
            );
            
            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "pageGlobals" => $pageGlobals,
                "categories" => $categories,
                "pagination" => $pagination
            ));
        }
        
        public function searchAction(){
            return new HttpResponse("@TODO");
        }
    }