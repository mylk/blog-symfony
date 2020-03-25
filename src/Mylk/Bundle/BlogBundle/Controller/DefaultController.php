<?php

namespace Mylk\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mylk\Bundle\BlogBundle\Form\CommentType;
use Mylk\Bundle\BlogBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Mylk\Bundle\BlogBundle\Event\CommentEvent;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $posts = $postRepo->findAllByStickyAndDate();

        return $this->renderPosts($posts, $request);
    }

    public function postViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $postId = $request->get("postId");
        $post = $postRepo->find($postId);

        if ($post) {
            $post->addView();
            $em->flush();
        } else {
            throw $this->createNotFoundException();
        }

        return $this->renderPosts(array($post), $request);
    }

    public function categoryViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categoryId = $request->get("categoryId");
        $category = $em->getRepository("MylkBlogBundle:Category")->find($categoryId);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $posts = $category->getPosts();

        return $this->renderPosts($posts, $request);
    }

    public function tagViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tagRepo = $em->getRepository("MylkBlogBundle:Tag");

        $tagId = $request->get("tagId");
        $tag = $tagRepo->find($tagId);

        if (!$tag) {
            throw $this->createNotFoundException();
        }

        $posts = $tag->getPosts();

        return $this->renderPosts($posts, $request);
    }

    public function archiveViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("MylkBlogBundle:Post");

        $yearMonth = array("year" => $request->get("year"), "month" => $request->get("month"));
        $posts = $repo->findByYearMonth($yearMonth);

        return $this->renderPosts($posts, $request);
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $term = $request->get("term");
        $posts = $postRepo->findBySearchTerm($term);

        return $this->renderPosts($posts, $request);
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

    public function commentSubmitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            $session = $request->getSession();

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
                } elseif ($post && $post->getCommentsClosed()) {
                    $session->getFlashBag()->add("error", "Comment submission failed. Comments are closed for this post.");
                } else {
                    // user faked the hidden field that contains the post id?
                    $session->getFlashBag()->add("error", "Comment could not be added to the post.");

                    $lastPostUrl = $request->headers->get("referer");
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

            return new RedirectResponse($this->generateUrl("post", array("postId" => $postId)));
        }
    }

    private function renderPosts($posts, $request)
    {
        $page_globals = $this->container->getParameter("page_globals");
        $paginator = $this->get("knp_paginator");
        $em = $this->getDoctrine()->getManager();

        $comments = array();
        $commentForm = null;

        // showing a single article
        if ($request->get("_route") === "post") {
            $post = $posts[0];

            if ($post !== null) {
                // generate comment form
                $commentForm = $this->createForm(CommentType::class, new Comment(), array(
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
            $posts, $request->get("page", 1), $page_globals["posts_per_page"]
        );

        return $this->render("MylkBlogBundle:Default:index.html.twig", array(
            "pagination" => $pagination,
            "comments" => $comments,
            "comment_form" => $commentForm
        ));
    }

    public function renderWidgetsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");

        $categories = $em->getRepository("MylkBlogBundle:Category")->findBy(array(), array("title" => "ASC"));
        $archive = $postRepo->getArchive();
        $comments = $em->getRepository("MylkBlogBundle:Comment")->findLatests();
        $popular = $postRepo->findPopular();
        $most_commented = $postRepo->findMostCommented();
        $tags = $em->getRepository("MylkBlogBundle:Tag")->findAll();

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
        $container = $this->container;
        return (
            $container->getParameter("mailer_user") !== "YOUR_USERNAME"
            && $container->getParameter("mailer_password") !== "YOUR_PASSWORD"
        );
    }
}
