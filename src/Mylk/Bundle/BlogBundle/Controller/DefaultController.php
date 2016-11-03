<?php

namespace Mylk\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mylk\Bundle\BlogBundle\Form\CommentType;
use Mylk\Bundle\BlogBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Mylk\Bundle\BlogBundle\Event\CommentEvent;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $posts = $postRepo->findAllByStickyAndDate();

        return $this->renderContent($posts);
    }

    public function postViewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $postId = $this->getRequest()->get("postid");
        $post = $postRepo->find($postId);

        if ($post) {
            $post->addView();
            $em->persist($post);
            $em->flush();
        } else {
            $post = null;
        }

        return $this->renderContent(array($post));
    }

    public function categoryViewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $categoryId = $this->getRequest()->get("categoryid");
        $posts = $postRepo->findBy(array("category" => $categoryId), array("createdAt" => "DESC"));

        return $this->renderContent($posts);
    }

    public function tagViewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tagRepo = $em->getRepository("MylkBlogBundle:Tag");

        $tagId = $this->getRequest()->get("tagid");
        $tag = $tagRepo->find($tagId);
        $posts = $tag->getPosts();

        return $this->renderContent($posts);
    }

    public function archiveViewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("MylkBlogBundle:Post");

        $request = $this->getRequest();
        $yearMonth = array("year" => $request->get("year"), "month" => $request->get("month"));
        $posts = $repo->findByYearMonth($yearMonth);

        return $this->renderContent($posts);
    }

    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $term = $this->getRequest()->get("term");
        $posts = $postRepo->findBySearchTerm($term);

        return $this->renderContent($posts);
    }

    public function rssAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("MylkBlogBundle:Post");
        $posts = $repo->findLatests();

        $rss = $this->get("mylk_blog.rss_generator");
        $feed = $rss->generate($posts);

        $response = new Response($feed);
        $response->headers->set("Content-Type", "text/xml; charset=UTF-8");
        return $response;
    }

    public function commentSubmitAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $form = $this->createForm(new CommentType(), new Comment());

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            $session = $request->getSession();

            $comment = $form->getData();
            $postId = $comment->getPost();

            if ($form->isValid()) {
                $post = $postRepo->find($postId);

                // check if comments are closed too, in case someone found his way through this action
                if ($post && $post->getCommentsClosed() === false) {
                    $comment->setPost($post);

                    // fire a comment_added event
                    if ($this->mailerIsSetUp()) {
                        $dispatcher = $this->container->get("event_dispatcher");
                        $dispatcher->dispatch("mylk_blogbundle.comment_added", new CommentEvent($post, $comment));
                    }

                    $session->getFlashBag()->add("success", "Comment successfully submitted.");

                    if ($post->getCommentsProtected()) {
                        $session->getFlashBag()->add("warn", "This post's comments are protected. Your comment is pending for approval before being published.");
                    } else {
                        $comment->approve();
                    }

                    $em->persist($comment);
                    $em->flush();
                } else if ($post && $post->getCommentsClosed() === true) {
                    $session->getFlashBag()->add("error", "Comment submission failed. Comments are closed for this post.");
                } else {
                    // user faked the hidden field that contains the post id?
                    $session->getFlashBag()->add("error", "Comment could not be added to the post.");

                    $lastPostUrl = $this->getRequest()->headers->get("referer");
                    return new RedirectResponse($lastPostUrl);
                }
            } else {
                $errors = $this->getErrorMessages($form);

                // itterate through the errors of each form field
                foreach ($errors as $errorKey => $errorMsgs) {
                    // itterate through the errors of each form field's children
                    foreach ($errorMsgs as $errorMsg) {
                        // $errorKey is the field name
                        $session->getFlashBag()->add("error", "$errorKey: $errorMsg");
                    }
                }
            }

            return new RedirectResponse($this->generateUrl("post", array("postid" => $postId)));
        }
    }

    private function renderContent($posts)
    {
        $page_globals = $this->container->getParameter("page_globals");
        $paginator = $this->get("knp_paginator");
        $em = $this->getDoctrine()->getManager();

        $comments = array();
        $comment_form = null;

        // showing a single article
        if ($this->getRequest()->get("_route") === "post") {
            $post = $posts[0];

            if ($post !== null) {
                // generate comment form
                $comment_form = $this->createForm(new CommentType(), new Comment(), array(
                    "action" => $this->generateUrl("comment_submit") . "#submit-comment",
                    "method" => "POST"
                ))
                    ->createView();

                // dont try to get comments
                if (!$post->getCommentsClosed()) {
                    $comments = $em->getRepository("MylkBlogBundle:Comment")->findAllowedAndApproved($post->getId());
                }
            }
        }

        $pagination = $paginator->paginate(
                $posts, $this->getRequest()->get("page", 1), $page_globals["posts_per_page"]
        );

        return $this->render("MylkBlogBundle:Default:index.html.twig", array(
            "pagination" => $pagination,
            "comments" => $comments,
            "comment_form" => $comment_form
        ));
    }

    public function renderWidgetsAction()
    {
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

    public function renderMenuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $menuItemEntities = $em->getRepository("MylkBlogBundle:MenuItem")->findAll();

        $menuGenerator = $this->get("mylk_blog.menu_generator");
        $menuItems = $menuGenerator->prepareMenu($menuItemEntities);

        return $this->render("MylkBlogBundle:Default:menu.html.twig", array("menu_items" => $menuItems));
    }

    private function getErrorMessages($form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        // forms have no common errors, each form field has its own,
        // just like a form did. fields can have children elements
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    private function mailerIsSetUp()
    {
        return ($this->container->getParameter("mailer_user") !== "YOUR_USERNAME" && $this->container->getParameter("mailer_password") !== "YOUR_PASSWORD");
    }
}
