<?php
    namespace Mylk\Bundle\BlogBundle\EventListener;

//    use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
    use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

    class Login{
        protected $em;

        public function __construct($em){
            $this->em = $em;
        }
        
        public function onLoginSuccess(InteractiveLoginEvent $event){
            $username = $event->getAuthenticationToken()->getUsername();
            
            $user = $this->em->getRepository("MylkBlogBundle:User")->findOneBy(array("username" => $username));

            $user->setLastLogin(\date("Y-m-d H:i:s"));
            $this->em->persist($user);
            $this->em->flush();
        }
    }
?>