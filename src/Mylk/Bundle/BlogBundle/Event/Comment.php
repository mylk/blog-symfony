<?php
    namespace Mylk\Bundle\BlogBundle\Event;

    use Symfony\Component\EventDispatcher\Event;

    class Comment extends Event{
        protected $post;
        protected $comment;
        
        public function __construct($post, $comment){
            $this->post = $post;
            $this->comment = $comment;
        }
        
        public function getPost(){
            return $this->post;
        }
        
        public function getComment(){
            return $this->comment;
        }
    }
?>