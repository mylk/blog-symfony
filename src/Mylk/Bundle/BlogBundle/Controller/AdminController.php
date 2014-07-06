<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Mylk\Bundle\BlogBundle\Form\PostType;
    use Mylk\Bundle\BlogBundle\Entity\Post;
    use Symfony\Component\HttpFoundation\Session\Session;

    class AdminController extends Controller
    {
        public function indexAction(){
            return $this->render("MylkBlogBundle:Admin:index.html.twig");
        }
        
        public function loginAction(){
            return $this->render("MylkBlogBundle:Admin:login.html.twig");
        }
        
        public function loginCheckAction(){}

        public function logoutAction(){}
        
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
            };
            
            return $this->render("MylkBlogBundle:Admin:postNew.html.twig", array("form" => $form->createView()));
        }
        
        public function postEditAction(){
            
        }
        
        public function postViewAction(){
            $em = $this->getDoctrine()->getManager();
            $posts = $em->getRepository("MylkBlogBundle:Post")->findBy(array(), array("createdAt" => "DESC"));
            
            return $this->render("MylkBlogBundle:Admin:postView.html.twig", array("posts" => $posts));
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
    }