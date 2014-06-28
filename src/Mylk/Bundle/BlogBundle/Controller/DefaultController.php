<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response as HttpResponse;

    class DefaultController extends Controller{
        public function indexAction(){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $postRepo = $this->getDoctrine()->getRepository("MylkBlogBundle:Post");
            
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();

            // get posts by sticky and creation date order
            $posts = $postRepo->findAllByStickyAndDate();
            
            $pagination = $paginator->paginate(
                $posts,
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
        
        public function postViewAction(){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
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
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();
            
            $categoryId = $this->getRequest()->get("categoryid");
            $posts = $postRepo->findBy(array("category" => $categoryId), array("createdAt" => "DESC"));
            
            $pagination = $paginator->paginate(
                $posts,
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
        
        public function tagViewAction(){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();
            
            $tagId = $this->getRequest()->get("tagid");
            $posts = $postRepo->findBy(array("tag" => $tagId));

            $pagination = $paginator->paginate(
                $posts,
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
        
        public function archiveViewAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $repo = $this->getDoctrine()->getRepository("MylkBlogBundle:Post");
            
            $archive = $repo->getArchive();
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            
            $yearMonth = array("year" => $request->get("year"), "month" => $request->get("month"));
            $posts = $repo->findByYearMonth($yearMonth);

            $pagination = $paginator->paginate(
                $posts,
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

        public function searchAction(){
            $em = $this->getDoctrine()->getManager();
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $postRepo->getArchive();
            
            $posts = $postRepo->findBySearchTerm($this->getRequest()->get("term"));

            $pagination = $paginator->paginate(
                $posts,
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