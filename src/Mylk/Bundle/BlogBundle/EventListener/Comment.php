<?php
    namespace Mylk\Bundle\BlogBundle\EventListener;
    
    use Mylk\Bundle\BlogBundle\Event\Comment as CommentEvent;

    class Comment{
        protected $mailer;
        protected $router;
        protected $mailerUser;
        protected $commentNotifiedEmails;

        public function __construct($mailer, $router, $mailerUser, $commentNotifiedEmails){
            $this->mailer = $mailer;
            $this->router = $router;
            $this->mailerUser = $mailerUser; 
            $this->commentNotifiedEmails = $commentNotifiedEmails;
        }
        
        public function onComment(CommentEvent $event){
            $post = $event->getPost();
            $comment = $event->getComment();
            
            $postUrl = $this->router->generate("post", array("postid" => $post->getId()), true);
            $messageBody = sprintf("New comment posted on <a href=\"%s\">%s</a> by the user named \"%s\":<br /><br />%s", $postUrl, $post->getTitle(), $comment->getUsername(), $comment->getContent());
            
            foreach($this->commentNotifiedEmails as $recipient){
                $message = \Swift_Message::newInstance()
                    ->setSubject("New comment posted on " . $post->getTitle())
                    ->setFrom($this->mailerUser)
                    ->setTo($recipient)
                    ->setBody($messageBody, "text/html");
            
                $this->mailer->send($message);
            };
        }
    }
?>