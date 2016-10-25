<?php

namespace Mylk\Bundle\BlogBundle\Service;

class RssFeedGeneratorService
{
    private $title;
    private $urlHomepage;
    private $urlFeed;
    private $description;

    public function setupDependencies($title, $urlHomepage, $urlFeed, $description)
    {
        $this->title = $title;
        $this->urlHomepage = $urlHomepage;
        $this->urlFeed = $urlFeed;
        $this->description = $description;
    }

    public function generate($posts)
    {
        $feed = new \SimpleXMLElement("<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" />");
        $feed->addAttribute("version", "2.0");
        
        $channel = $feed->addChild("channel");
        
        $atomLink = $channel->addChild("link", "", "http://www.w3.org/2005/Atom"); 
        $atomLink->addAttribute("href", $this->urlFeed);
        $atomLink->addAttribute("rel", "self");
        $atomLink->addAttribute("type", "application/rss+xml");
        
        $channel->addChild("title", $this->title);
        $channel->addChild("link", $this->urlHomepage);
        $channel->addChild("description", $this->description);

        foreach ($posts as $post) {
            $item = $channel->addChild("item");
            $item->addChild("title", $post->getTitle());
            // set first 150 chars as the description
            $item->addChild("description", \substr($post->getContent(), 0, 150));
            $item->addChild("guid", \sprintf("http://example.com/post/%s", $post->getId()));
            // convert date to the rss standard
            $item->addChild("pubDate", $post->getCreatedAt()->format("r"));
        }
        
        return $feed->asXML();
    }
}
