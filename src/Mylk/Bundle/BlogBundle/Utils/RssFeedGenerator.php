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
                // the first 150 chars as description
                $item->addChild("description", substr($post->getContent(), 0, 150));
                $item->addChild("guid", "http://localhost:8000/post/" . $post->getId());
                // converts the date to the rss standards
                $item->addChild("pubDate", $this->toRFC2822($post->getCreatedAt()));
            };
            
            return $rss->asXML();
        }

        // converts dates to format of "Tue, 04 Feb 2014 00:33:05 +0200"
        private function toRFC2822($datetime){
            list($date, $time) = explode(" ", $datetime);
            list($y, $m, $d) = explode("-", $date);
            list($h, $i, $s) = explode(":", $time);
            
            // set an arbitrary timezone
            // date_default_timezone_set("Europe/Athens");

            // uses default timezone as set in php.ini, date.timezone
            return date("r", mktime($h, $i, $s, $m, $d, $y));
        }
    }
?>
