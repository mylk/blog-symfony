<?php

namespace Mylk\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mylk\Bundle\BlogBundle\Form\PostType;
use Mylk\Bundle\BlogBundle\Entity\Post;
use Mylk\Bundle\BlogBundle\Form\TagType;
use Mylk\Bundle\BlogBundle\Entity\Tag;
use Mylk\Bundle\BlogBundle\Form\CategoryType;
use Mylk\Bundle\BlogBundle\Entity\Category;
use Mylk\Bundle\BlogBundle\Form\ConfirmType;
use Mylk\Bundle\BlogBundle\Entity\MenuItem;
use Mylk\Bundle\BlogBundle\Form\MenuItemType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render("MylkBlogBundle:Admin:index.html.twig");
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        $lastErrorField = SecurityContextInterface::AUTHENTICATION_ERROR;

        // get the login error if there is one
        if ($request->attributes->has($lastErrorField)) {
            $error = $request->attributes->get($lastErrorField);
        } elseif ($session !== null && $session->has($lastErrorField)) {
            $error = $session->get($lastErrorField);
            $session->remove($lastErrorField);
        } else {
            $error = "";
        }

        if ($error) {
            $session->getFlashBag()->add("error", $error->getMessage());
        }

        return $this->render("MylkBlogBundle:Admin:login.html.twig");
    }

    public function postNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $post = new Post();
        $form = $this->createForm(new PostType(), $post, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_post_new")
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // get the logged in user
                $user = $this->getUser();

                $post->setCreatedBy($user);
                $em->persist($post);
                $em->flush();

                $session->getFlashBag()->add("success", "Post was successfully created!");
                return $this->redirect($this->generateUrl("admin_post_new"));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:post.html.twig", array("form" => $form->createView()));
    }

    public function postEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $postId = $request->get("postId");
        $post = $em->getRepository("MylkBlogBundle:Post")->find($postId);
        if (!$post) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new PostType(), $post, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_post_edit", array("postId" => $postId))
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $user = $this->getUser();

                $post->setUpdatedAt(new \DateTime())
                    ->setUpdatedBy($user);
                $em->flush();

                $session->getFlashBag()->add("success", "Post was successfully updated!");
                return $this->redirect($this->generateUrl("admin_post_edit", array("postId" => $postId)));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:post.html.twig", array("form" => $form->createView()));
    }

    public function postListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postRepo = $em->getRepository("MylkBlogBundle:Post");
        $session = $request->getSession();

        $delete = $request->get("delete");

        if ($delete) {
            foreach ($delete as $postId) {
                $post = $postRepo->find($postId);
                $comments = $post->getComments();

                if ($comments) {
                    foreach ($comments as $comment) {
                        $em->remove($comment);
                    }
                }

                $em->remove($post);
            }

            $em->flush();

            $session->getFlashBag()->add("success", "Post(s) successfully removed!");
            return $this->redirect($this->generateUrl("admin_post_list"));
        }

        $posts = $postRepo->findBy(array(), array("createdAt" => "DESC"));

        return $this->render("MylkBlogBundle:Admin:postList.html.twig", array("posts" => $posts));
    }

    public function categoryNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_category_new")
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();

                $session->getFlashBag()->add("success", "Category was successfully created!");
                return $this->redirect($this->generateUrl("admin_category_new"));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:category.html.twig", array("form" => $form->createView()));
    }

    public function categoryEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $categoryId = $request->get("categoryId");
        $category = $em->getRepository("MylkBlogBundle:Category")->find($categoryId);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new CategoryType(), $category, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_category_edit", array("categoryId" => $categoryId))
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                $session->getFlashBag()->add("success", "Category was successfully updated!");
                return $this->redirect($this->generateUrl("admin_category_edit", array("categoryId" => $categoryId)));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
    }

    public function categoryListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryRepo = $em->getRepository("MylkBlogBundle:Category");
        $session = $request->getSession();

        $delete = $request->get("delete");

        $form = $this->createForm(new ConfirmType(), null, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_category_list")
        ));
        $confirm = $request->get("mylk_bundle_blogbundle_confirm");

        if ($request->isMethod("POST") && $delete) {
            $session->set("delete", $delete);

            return $this->render("MylkBlogBundle:Admin:categoryList.html.twig", array("form" => $form->createView()));
        } elseif ($request->isMethod("POST") && (isset($confirm["yes"]) || isset($confirm["no"]))) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($form->get("yes")->isClicked()) {
                    $this->categoryRemove();

                    $session->getFlashBag()->add("success", "Category/ies successfully removed!");
                    return $this->redirect($this->generateUrl("admin_category_list"));
                }
            } else {
                $this->getErrorMessages($form);
            }
        }

        $categories = $categoryRepo->findAll();

        return $this->render("MylkBlogBundle:Admin:categoryList.html.twig", array("categories" => $categories));
    }

    public function tagNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_tag_new")
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->persist($tag);
                $em->flush();

                $session->getFlashBag()->add("success", "Tag was successfully created!");
                return $this->redirect($this->generateUrl("admin_tag_new"));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
    }

    public function tagEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $tagId = $request->get("tagId");
        $tag = $em->getRepository("MylkBlogBundle:Tag")->find($tagId);
        if (!$tag) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new TagType(), $tag, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_tag_edit", array("tagId" => $tagId))
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                $session->getFlashBag()->add("success", "Tag was successfully updated!");
                return $this->redirect($this->generateUrl("admin_tag_edit", array("tagId" => $tagId)));
            } else {
                $this->getErrorMessages($form);
            }
        }

        return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
    }

    public function tagListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tagRepo = $em->getRepository("MylkBlogBundle:Tag");
        $session = $request->getSession();

        $delete = $request->get("delete");

        if ($delete) {
            foreach ($delete as $tagId) {
                $tag = $tagRepo->find($tagId);
                $posts = $tag->getPosts();

                if ($posts) {
                    foreach ($posts as $post) {
                        // remove related tag from post
                        $post->getTags()->removeElement($tag);
                        $em->persist($post);
                    }
                }

                $em->remove($tag);
            }

            $em->flush();

            $session->getFlashBag()->add("success", "Tag(s) successfully removed!");
            return $this->redirect($this->generateUrl("admin_tag_list"));
        }

        $tags = $tagRepo->findAll();

        return $this->render("MylkBlogBundle:Admin:tagList.html.twig", array("tags" => $tags));
    }

    public function commentListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commentRepo = $em->getRepository("MylkBlogBundle:Comment");
        $session = $request->getSession();

        $delete = $request->get("delete");

        if ($delete) {
            foreach ($delete as $commentId) {
                $comment = $commentRepo->find($commentId);

                $em->remove($comment);
            }

            $em->flush();

            $session->getFlashBag()->add("success", "Comment(s) successfully removed!");
            return $this->redirect($this->generateUrl("admin_comment_list"));
        }

        $comments = $commentRepo->findPendingApproval();

        return $this->render("MylkBlogBundle:Admin:commentList.html.twig", array("comments" => $comments));
    }

    public function commentApproveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commentRepo = $em->getRepository("MylkBlogBundle:Comment");

        $commentId = $request->get("id");
        $outcome = $request->get("outcome");

        $comment = $commentRepo->find($commentId);

        if ($comment) {
            if ($outcome === "approved") {
                $comment->setApproved(true);
            } elseif ($outcome === "rejected") {
                $comment->setApproved(false);
            }
        }

        $em->flush();

        return new Response();
    }

    public function menuItemNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $menuItem = new MenuItem();
        $form = $this->createForm(new MenuItemType(), $menuItem, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_menu_item_new")
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $em->persist($menuItem);
            $em->flush();
        }

        return $this->render("MylkBlogBundle:Admin:menuItem.html.twig", array("form" => $form->createView()));
    }

    public function menuItemEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $menuItemId = $request->get("menuItemId");
        $menuItem = $em->getRepository("MylkBlogBundle:MenuItem")->find($menuItemId);
        if (!$menuItem) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new MenuItemType(), $menuItem, array(
            "method" => "POST",
            "action" => $this->generateUrl("admin_menu_item_edit", array("menuItemId" => $menuItemId))
        ));

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);

            $em->persist($menuItem);
            $em->flush();
        }

        return $this->render("MylkBlogBundle:Admin:menuItem.html.twig", array("form" => $form->createView()));
    }

    public function menuItemListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $menuItemRepo = $em->getRepository("MylkBlogBundle:MenuItem");
        $session = $request->getSession();

        $menuItemIds = $request->get("delete");
        if ($menuItemIds) {
            $menuItems = array();
            foreach ($menuItemIds as $menuItemId) {
                $menuItem = $menuItemRepo->find($menuItemId);
                $menuItems = \array_merge($menuItems, $menuItem->getChildrenTree());
            }

            // reverse to start deleting from grand children
            // for foreign key constraints not to fail
            $menuItems = \array_reverse($menuItems);

            foreach ($menuItems as $menuItem) {
                $em->remove($menuItem);
            }
            $em->flush();

            $session->getFlashBag()->add("success", "Menu item(s) successfully removed!");
            return $this->redirect($this->generateUrl("admin_menu_item_list"));
        }

        $menuItems = $menuItemRepo->findAll();

        return $this->render("MylkBlogBundle:Admin:menuItemList.html.twig", array("menuItems" => $menuItems));
    }

    private function getErrorMessages($form)
    {
        $errors = array();
        $session = new Session();
        if (!$session->isStarted()) {
            $session->start();
        }

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

        // itterate through the errors of each form field
        foreach ($errors as $errorKey => $errorMsgs) {
            // itterate through the errors of each form field's children
            if (\is_array($errorMsgs)) {
                foreach ($errorMsgs as $errorMsg) {
                    // $errorKey is the field name
                    $session->getFlashBag()->add("error", "$errorKey: $errorMsg");
                }
            }
        }

        return $errors;
    }

    private function categoryRemove()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $categoryRepo = $em->getRepository("MylkBlogBundle:Category");
        $postRepo = $em->getRepository("MylkBlogBundle:Post");
        $commentRepo = $em->getRepository("MylkBlogBundle:Comment");
        $delete = $request->getSession()->get("delete");

        if ($delete) {
            foreach ($delete as $categoryId) {
                $category = $categoryRepo->find($categoryId);
                $posts = $postRepo->findBy(array("category" => $category));

                if ($posts) {
                    foreach ($posts as $post) {
                        $comments = $commentRepo->findBy(array("post" => $post));

                        if ($comments) {
                            foreach ($comments as $comment) {
                                $em->remove($comment);
                            }
                        }

                        $em->remove($post);
                    }
                }

                $em->remove($category);
            }

            $em->flush();
        }
    }
}
