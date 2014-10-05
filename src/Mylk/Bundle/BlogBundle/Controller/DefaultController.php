<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Mylk\Bundle\BlogBundle\Utils\RssFeedGenerator;
    use Mylk\Bundle\BlogBundle\Form\CommentType;
    use Mylk\Bundle\BlogBundle\Entity\Comment;
    use Symfony\Component\HttpFoundation\Response as HttpResponse;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Mylk\Bundle\BlogBundle\Event\Comment as CommentEvent;

    class DefaultController extends Controller{
        public function indexAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $posts = $postRepo->findAllByStickyAndDate();

            return $this->renderContent($posts);
        }
        
        public function postViewAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");

            $postId = $this->getRequest()->get("postid");
            $post = $postRepo->find($postId);

            $post->addView();
            $em->persist($post);
            $em->flush();
            
            return $this->renderContent(array($post));
        }
        
        public function categoryViewAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $categoryId = $this->getRequest()->get("categoryid");
            $posts = $postRepo->findBy(array("category" => $categoryId), array("createdAt" => "DESC"));
   
            return $this->renderContent($posts);
        }
        
        public function tagViewAction(){
            $em = $this->getDoctrine()->getManager();
            $tagRepo = $em->getRepository("MylkBlogBundle:Tag");

            $tagId = $this->getRequest()->get("tagid");
            $tag = $tagRepo->find($tagId);
            $posts = $tag->getPosts();
            
            return $this->renderContent($posts);
        }
        
        public function archiveViewAction(){
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository("MylkBlogBundle:Post");
            
            $request = $this->getRequest();
            $yearMonth = array("year" => $request->get("year"), "month" => $request->get("month"));
            $posts = $repo->findByYearMonth($yearMonth);
            
            return $this->renderContent($posts);
        }

        public function searchAction(){
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            
            $term = $this->getRequest()->get("term");
            $posts = $postRepo->findBySearchTerm($term);

            return $this->renderContent($posts);
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
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
                    
            $form = $this->createForm(new CommentType(), new Comment());
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                $session = new Session();
                if(!$session->isStarted()) $session->start();
                
                $comment = $form->getData();
                $postId = $comment->getPost();
                
                if($form->isValid()){
                    $post = $postRepo->find($postId);
                    
                    if($post){
                        $comment->setPost($post);

                        $em->persist($comment);
                        $em->flush();
                        
                        // fire a comment_added event
                        if($this->mailerIsSetUp()){
                            $dispatcher = $this->container->get("event_dispatcher");
                            $dispatcher->dispatch("mylk_blogbundle.comment_added", new CommentEvent($post, $comment));
                        };

                        $session->getFlashBag()->add("success", "Comment successfully submitted.");
                    }else{
                        // user faked the hidden field that contains the post id?
                        $session->getFlashBag()->add("error", "Comment could not be added to the post.");
                        
                        $lastPostUrl = $this->getRequest()->headers->get("referer");
                        return new RedirectResponse($lastPostUrl);
                    };
                }else{
                    $errors = $this->getErrorMessages($form);

                    // itterate through the errors of each form field
                    foreach($errors as $errorKey => $errorMsgs){
                        // itterate through the errors of each form field's children
                        foreach($errorMsgs as $errorMsg){
                            // $errorKey is the field name
                            $session->getFlashBag()->add("error", "$errorKey: $errorMsg");
                        };
                    };
                };
                
                return new RedirectResponse($this->generateUrl("post", array("postid" => $postId)));
            };
        }
        
        private function renderContent($posts){
            $page_globals = $this->container->getParameter("page_globals");
            $paginator = $this->get("knp_paginator");
            
            // generate the comment form if showing a single article
            if($this->getRequest()->get("_route") === "post"){
                $comment_form = $this->createForm(new CommentType(), new Comment(), array(
                    "action" => $this->generateUrl("comment_submit") . "#submit-comment",
                    "method" => "POST"))
                    ->createView();
            }else{
                $comment_form = null;
            };

            $pagination = $paginator->paginate(
                $posts,
                $this->getRequest()->get("page", 1),
                $page_globals["posts_per_page"]
            );

            return $this->render("MylkBlogBundle:Default:index.html.twig", array(
                "pagination" => $pagination,
                "comment_form" => $comment_form
            ));
        }
        
        public function renderWidgetsAction(){
            $em = $this->getDoctrine()->getManager();
            
            $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
            $archive = $this->getDoctrine()->getRepository("MylkBlogBundle:Post")->getArchive();
            $comments = $this->getDoctrine()->getRepository("MylkBlogBundle:Comment")->findLatests();
            $popular = $this->getDoctrine()->getRepository("MylkBlogBundle:Post")->findPopular();
            $most_commented = $this->getDoctrine()->getRepository("MylkBlogBundle:Post")->findMostCommented();
            $tags = $this->getDoctrine()->getRepository("MylkBlogBundle:Tag")->findAll();

            return $this->render("MylkBlogBundle:Default:widgets.html.twig", array(
                "categories" => $categories,
                "archive" => $archive,
                "comments" => $comments,
                "popular" => $popular,
                "most_commented" => $most_commented,
                "tags" => $tags,
            ));
        }
        
        public function renderMenuAction(){
            $em = $this->getDoctrine()->getManager();
            $_menuItems = $em->getRepository("MylkBlogBundle:MenuItem")->findAll();
            
            $menuItems = $this->prepareMenu($_menuItems);

            return $this->render("MylkBlogBundle:Default:menu.html.twig", array("menu_items" => $menuItems));
        }
        
        private function prepareMenu($menuItems, $parentId = null){
            $menu = array();

            foreach($menuItems as $item){
                $item = $item->toArray();

                if($item["parent"] == $parentId){
                    $menu[$item["id"]] = $item;
                    $children =  $this->prepareMenu($menuItems, $item["id"]);

                    if($children){
                        $menu[$item["id"]]["children"] = $children;
                    };
                };
            };

            return $menu;
        }
        
        private function getErrorMessages($form){
            $errors = array();

            foreach($form->getErrors() as $key => $error){
                    $errors[] = $error->getMessage();
            };

            // forms have no common errors, each form field has its own,
            // just like a form did. fields can have children elements
            foreach($form->all() as $child){
                if(!$child->isValid()){
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                };
            };

            return $errors;
        }

        private function mailerIsSetUp(){
            return ($this->container->getParameter("mailer_user") !== "YOUR_USERNAME" && $this->container->getParameter("mailer_password") !== "YOUR_PASSWORD");
        }
    }
