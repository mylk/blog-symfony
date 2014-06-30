<?php
    namespace Mylk\Bundle\BlogBundle\Utils;
    
    class RssFeedGenerator{
        public function generate($posts, $config){
            $rss = new \SimpleXMLElement("<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" />");
            $rss->addAttribute("version", "2.0");

            $channel = $rss->addChild("channel");
            
            $atomLink = $channel->addChild("link", "", "http://www.w3.org/2005/Atom"); 
            $atomLink->addAttribute("href", $config["rssURL"]); 
            $atomLink->addAttribute("rel", "self"); 
            $atomLink->addAttribute("type", "application/rss+xml");
            
            $channel->addChild("title", $config["blogTitle"]);
            $channel->addChild("link", $config["homepageURL"]);
            $channel->addChild("description", $config["blogDescription"]);

            foreach($posts as $post){
                $item = $channel->addChild("item");
                $item->addChild("title", $post->getTitle());
                $item->addChild("description", $post->getContent());
                $item->addChild("guid", "http://localhost:8000/post/" . $post->getId());
                $item->addChild("pubDate", $post->getCreatedAt());
            };
            
            return $rss->asXML();
        }
    }
?>
