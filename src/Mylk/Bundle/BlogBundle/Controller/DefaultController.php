<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Mylk\Bundle\BlogBundle\Utils\RssFeedGenerator;
    use Symfony\Component\HttpFoundation\Response as HttpResponse;

    class DefaultController extends Controller{
        public function indexAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $posts = $postRepo->findAllByStickyAndDate();

            return $this->renderBlog($posts);
        }
        
        public function postViewAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $page_globals = $this->container->getParameter("page_globals");
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();
   
            $postId = $this->getRequest()->get("postid");
            $post = $postRepo->find($postId);
            
            return $this->render("MylkBlogBundle:Default:post.html.twig", array(
                "page_globals" => $page_globals,
                "categories" => $categories,
                "archive" => $archive,
                "post" => $post
            ));
        }
        
        public function categoryViewAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $categoryId = $this->getRequest()->get("categoryid");
            $posts = $postRepo->findBy(array("category" => $categoryId), array("createdAt" => "DESC"));
   
            return $this->renderBlog($posts);
        }
        
        public function tagViewAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $tagId = $this->getRequest()->get("tagid");
            $posts = $postRepo->findBy(array("tag" => $tagId));
            
            return $this->renderBlog($posts);
        }
        
        public function archiveViewAction(){
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository("MylkBlogBundle:Post");
            
            $request = $this->getRequest();
            $yearMonth = array("year" => $request->get("year"), "month" => $request->get("month"));
            $posts = $repo->findByYearMonth($yearMonth);
            
            return $this->renderBlog($posts);
        }

        public function searchAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $term = $this->getRequest()->get("term");
            $posts = $postRepo->findBySearchTerm($term);

            return $this->renderBlog($posts);
        }
        
        public function rssAction(){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $repo = $em->getRepository("MylkBlogBundle:Post");
            $posts = $repo->findLatests();
            
            $rss = new RssFeedGenerator();
            $feed = $rss->generate(
                $posts,
                $config = array(
                    // true requests a full URL
                    "rssURL" => $this->generateUrl("rss", array(), true),
                    "homepageURL" => $this->generateUrl("homepage", array(), true),
                    "blogTitle" => $page_globals["blog_title"],
                    "blogDescription" => $page_globals["blog_description"]
                )
            );

            $response = new HttpResponse($feed);
            $response->headers->set("Content-Type", "text/xml; charset=UTF-8");
            return $response;
        }
        
        private function renderBlog($content){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $postRepo = $this->getDoctrine()->getRepository("MylkBlogBundle:Post");

            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();

            $pagination = $paginator->paginate(
                $content,
                $this->getRequest()->get("page", 1),
                $page_globals["posts_per_page"]
            );

            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "page_globals" => $page_globals,
                "categories" => $categories,
                "archive" => $archive,
                "pagination" => $pagination
            ));
        }
    }