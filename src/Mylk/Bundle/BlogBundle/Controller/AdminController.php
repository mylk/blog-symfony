<?php
    namespace Mylk\Bundle\BlogBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    }