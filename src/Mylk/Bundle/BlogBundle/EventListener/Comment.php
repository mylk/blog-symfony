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
            
            // @TODO check if commentators have subscribed to get notifications for new comments
            $commentatorsNotCurrent = \array_diff($post->getCommentators(), array($comment->getEmail()));
            $adminsCommentators = \array_merge($this->commentNotifiedEmails, $commentatorsNotCurrent);
            $adminsCommentatorsUniq = \array_unique($adminsCommentators);

            $postUrl = $this->router->generate("post", array("postid" => $post->getId()), true);
            $messageBody = sprintf("New comment posted on <a href=\"%s\">%s</a> by the user named \"%s\":<br /><br />%s", $postUrl, $post->getTitle(), $comment->getUsername(), $comment->getContent());
            
            foreach($adminsCommentatorsUniq as $recipient){
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