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
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\SecurityContextInterface;

    class AdminController extends Controller
    {
        public function indexAction(){
            return $this->render("MylkBlogBundle:Admin:index.html.twig");
        }
        
        public function loginAction(Request $request){
            $session = $request->getSession();

            $lastErrorField = SecurityContextInterface::AUTHENTICATION_ERROR;

            // get the login error if there is one
            if($request->attributes->has($lastErrorField)){
                $error = $request->attributes->get($lastErrorField);
            }elseif($session !== null && $session->has($lastErrorField)){
                $error = $session->get($lastErrorField);
                $session->remove($lastErrorField);
            }else{
                $error = "";
            };
            
            if($error) $session->getFlashBag()->add("error", $error->getMessage());

            return $this->render("MylkBlogBundle:Admin:login.html.twig");
        }
        
        public function postNewAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $userRepo = $em->getRepository("MylkBlogBundle:User");
            
            $form = $this->createForm(new PostType(), new Post(), array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_post_new")
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);

                if($form->isValid()){
                    // get the username of the logged in user
                    $username = $this->getUser()->getUsername();
                    $user = $userRepo->findOneBy(array("username" => $username));
                    
                    $post = $form->getData();
                    $post->setCreatedBy($user);
                    
                    $em->persist($post);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Post was successfully created!");
                    return $this->redirect($this->generateUrl("admin_post_new"));
                }else{
                    $this->getErrorMessages($form);
                };
            };

            return $this->render("MylkBlogBundle:Admin:post.html.twig", array("form" => $form->createView()));
        }
        
        public function postEditAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $userRepo = $em->getRepository("MylkBlogBundle:User");
            
            $postId = $request->get("postid");
            $post = $em->getRepository("MylkBlogBundle:Post")->findOneBy(array("id" => $postId));
            
            $form = $this->createForm(new PostType(), $post, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_post_edit", array("postid" => $postId))
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    $post = $form->getData();
                    
                    $username = $this->getUser()->getUsername();
                    $user = $userRepo->findOneBy(array("username" => $username));
                    
                    $post->setUpdatedAt();
                    $post->setUpdatedBy($user);
                    
                    $em->persist($post);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Post was successfully updated!");
                    return $this->redirect($this->generateUrl("admin_post_edit", array("postid" => $postId)));
                }else{
                    $this->getErrorMessages($form);
                };
            };
            
            return $this->render("MylkBlogBundle:Admin:post.html.twig", array("form" => $form->createView()));
        }
        
        public function postListAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            $commentRepo = $em->getRepository("MylkBlogBundle:Comment");
            $session = new Session();
            
            $delete = $request->get("delete");
            
            if($delete){
                foreach($delete as $postId){
                    $post = $postRepo->find($postId);
                    $comments = $commentRepo->findBy(array("post" => $post));
                    
                    if($comments){
                        foreach($comments as $comment){
                            $em->remove($comment);
                        };
                    };
                    
                    $em->remove($post);
                };
                
                $em->flush();
                
                $session->getFlashBag()->add("success", "Post(s) successfully removed!");
                return $this->redirect($this->generateUrl("admin_post_list"));
            };
            
            $posts = $postRepo->findBy(array(), array("createdAt" => "DESC"));
            
            return $this->render("MylkBlogBundle:Admin:postList.html.twig", array("posts" => $posts));
        }
        
        public function categoryNewAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $form = $this->createForm(new CategoryType, new Category, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_category_new")
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    $category = $form->getData();
                    
                    $em->persist($category);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Category was successfully created!");
                    return $this->redirect($this->generateUrl("admin_category_new"));
                }else{
                    $this->getErrorMessages($form);
                };
            };
            
            return $this->render("MylkBlogBundle:Admin:category.html.twig", array("form" => $form->createView()));
        }
        
        public function categoryEditAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $categoryRepo = $em->getRepository("MylkBlogBundle:Category");
            $categoryId = $request->get("categoryid");
            
            $category = $categoryRepo->findOneBy(array("id" => $categoryId));
            
            $form = $this->createForm(new CategoryType(), $category, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_category_edit", array("categoryid" => $categoryId))
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    $category = $form->getData();
                    
                    $em->persist($category);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Category was successfully updated!");
                    return $this->redirect($this->generateUrl("admin_category_edit", array("categoryid" => $categoryId)));
                }else{
                    $this->getErrorMessages($form);
                };
            };
            
            return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
        }
        
        public function categoryListAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $categoryRepo = $em->getRepository("MylkBlogBundle:Category");
            $session = new Session();

            $delete = $request->get("delete");
            
            
            $form = $this->createForm(new ConfirmType(), null, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_category_list")
            ));
            $confirm = $request->get("mylk_bundle_blogbundle_confirm");
                
            if($request->isMethod("POST") && $delete){
                $delete = $request->get("delete");
                $request->getSession()->set("delete", $delete);

                return $this->render("MylkBlogBundle:Admin:categoryList.html.twig", array("form" => $form->createView()));
            }else if($request->isMethod("POST") && (isset($confirm["yes"]) || isset($confirm["no"]))){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    if($form->get("yes")->isClicked()){
                        $this->categoryRemove();
                        
                        $session->getFlashBag()->add("success", "Category/ies successfully removed!");
                        return $this->redirect($this->generateUrl("admin_category_list"));
                    };
                }else{
                    $this->getErrorMessages($form);
                };
            };

            $categories = $categoryRepo->findAll();
            
            return $this->render("MylkBlogBundle:Admin:categoryList.html.twig", array("categories" => $categories));
        }
        
        public function tagNewAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $form = $this->createForm(new TagType, new Tag, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_tag_new")
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    $tag = $form->getData();
                    
                    $em->persist($tag);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Tag was successfully created!");
                    return $this->redirect($this->generateUrl("admin_tag_new"));
                }else{
                    $this->getErrorMessages($form);
                };
            };
            
            return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
        }
        
        public function tagEditAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $session = new Session();
            
            $tagRepo = $em->getRepository("MylkBlogBundle:Tag");
            $tagId = $request->get("tagid");
            
            $tag = $tagRepo->findOneBy(array("id" => $tagId));
            
            $form = $this->createForm(new TagType(), $tag, array(
                "method" => "POST",
                "action" => $this->generateUrl("admin_tag_edit", array("tagid" => $tagId))
            ));
            
            if($request->isMethod("POST")){
                $form->handleRequest($request);
                
                if($form->isValid()){
                    $tag = $form->getData();
                    
                    $em->persist($tag);
                    $em->flush();
                    
                    $session->getFlashBag()->add("success", "Tag was successfully updated!");
                    return $this->redirect($this->generateUrl("admin_tag_edit", array("tagid" => $tagId)));
                }else{
                    $this->getErrorMessages($form);
                };
            };
            
            return $this->render("MylkBlogBundle:Admin:tag.html.twig", array("form" => $form->createView()));
        }
        
        public function tagListAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $tagRepo = $em->getRepository("MylkBlogBundle:Tag");
            $session = new Session();
            
            $delete = $request->get("delete");
            
            if($delete){
                foreach($delete as $tagId){
                    $tag = $tagRepo->find($tagId);
                    $posts = $tag->getPosts();
                    
                    if($posts){
                        foreach($posts as $post){
                            // remove related tag from post
                            $post->getTags()->removeElement($tag);
                            $em->persist($post);
                        };
                    };
                   
                    $em->remove($tag);
                };

                $em->flush();
                
                $session->getFlashBag()->add("success", "Tag(s) successfully removed!");
                return $this->redirect($this->generateUrl("admin_tag_list"));
            };
            
            $tags = $tagRepo->findAll();
            
            return $this->render("MylkBlogBundle:Admin:tagList.html.twig", array("tags" => $tags));
        }
        
        public function commentListAction(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $commentRepo = $em->getRepository("MylkBlogBundle:Comment");

            $delete = $request->get("delete");

            if($delete){
                foreach($delete as $commentId){
                    $comment = $commentRepo->find($commentId);

                    $em->remove($comment);
                };

                $em->flush();
                
                $session->getFlashBag()->add("success", "Comment(s) successfully removed!");
                return $this->redirect($this->generateUrl("admin_comment_list"));
            };
            
            $comments = $commentRepo->findPendingApproval();
            
            return $this->render("MylkBlogBundle:Admin:commentList.html.twig", array("comments" => $comments));
        }
        
        private function getErrorMessages($form){
            $errors = array();
            $session = new Session();

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

            // itterate through the errors of each form field
            foreach($errors as $errorKey => $errorMsgs){
                // itterate through the errors of each form field's children
                if(\is_array($errorMsgs)){
                    foreach($errorMsgs as $errorMsg){
                        // $errorKey is the field name
                        $session->getFlashBag()->add("error", "$errorKey: $errorMsg");
                    };
                };
            };
            
            return $errors;
        }
        
        private function categoryRemove(){
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            
            $categoryRepo = $em->getRepository("MylkBlogBundle:Category");
            $postRepo = $em->getRepository("MylkBlogBundle:Post");
            $commentRepo = $em->getRepository("MylkBlogBundle:Comment");
            $delete = $request->getSession()->get("delete");

            if($delete){
                foreach($delete as $categoryId){
                    $category = $categoryRepo->find($categoryId);
                    $posts = $postRepo->findBy(array("category" => $category));

                    if($posts){
                        foreach($posts as $post){
                            $comments = $commentRepo->findBy(array("post" => $post));

                            if($comments){
                                foreach($comments as $comment){
                                    $em->remove($comment);
                                };
                            };

                            $em->remove($post);
                        };
                    };

                    $em->remove($category);
                };

                $em->flush();
            };
        }
    }