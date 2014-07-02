<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Mylk\Bundle\BlogBundle\Utils\RssFeedGenerator;
    use Mylk\Bundle\BlogBundle\Form\CommentType;
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
   
            $postId = $this->getRequest()->get("postid");
            $post = $postRepo->find($postId);
            
            return $this->renderBlog(array($post));
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
        
        public function commentSubmitAction(){
            return new HttpResponse("@TODO: comment submission");
        }
        
        private function renderBlog($posts){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");

            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $this->getDoctrine()->getRepository("MylkBlogBundle:Post")->getArchive();
            $comments = $this->getDoctrine()->getRepository("MylkBlogBundle:Comment")->findBy(array(), array("createdAt" => "DESC"));
            $tags = $this->getDoctrine()->getRepository("MylkBlogBundle:Tag")->findAll();
            $comment_form = $this->createForm(new CommentType());

            $pagination = $paginator->paginate(
                $posts,
                $this->getRequest()->get("page", 1),
                $page_globals["posts_per_page"]
            );

            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "page_globals" => $page_globals,
                "categories" => $categories,
                "archive" => $archive,
                "comments" => $comments,
                "tags" => $tags,
                "pagination" => $pagination,
                "comment_form" => $comment_form->createView()
            ));
        }
    }