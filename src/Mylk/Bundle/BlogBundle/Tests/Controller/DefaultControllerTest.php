<?php

namespace Mylk\Bundle\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testRss(){
        $client = static::createClient();

        $crawler = $client->request("GET", "/rss");
        
        // rss show articles
        $this->assertTrue($crawler->filter("item")->count() > 0);
    }
    
    public function testIndex(){
        $client = static::createClient();

        $crawler = $client->request("GET", "/");
        
        // articles
        $this->assertTrue($crawler->filter("article")->count() > 0);
        
        // menu
        $this->assertTrue($crawler->filter("div.menu > ul > li")->count() > 0);
        
        // widgets have content
        $this->assertTrue($crawler->filter(".widget-area .widget ul li")->count() > 0);
        
        // search (assumes you use a search term that exists)
        $searchForm = $crawler->selectButton("Search")->form();
        $searchCrawler = $client->submit($searchForm, array("term" => "blog"));
        $this->assertTrue($searchCrawler->filter("article")->count() > 0);
    }
    
    public function testPost(){
        $client = static::createClient();
        
        $crawler = $client->request("GET", "/");
        
        // post has comments (assumes the first post displayed has comments)
        $postLink = $crawler->filter(".post-title a")->eq(0)->link();
        $postCrawler = $client->click($postLink);
        $this->assertTrue($postCrawler->filter(".post-comments .comment-info")->count() > 0);
        
        // posts' comment form loaded
        $this->assertTrue($postCrawler->filter("form[name='mylk_bundle_blogbundle_comment']")->count() > 0);
        
        // posts' comment form has captcha
        $this->assertTrue($postCrawler->filter("input.captcha")->count() > 0);
        $postCaptchaDivCrawler = $postCrawler->filter("input.captcha")->parents()->first();
        $this->assertTrue($postCaptchaDivCrawler->filter("img")->count() > 0);
        
        // comment cannot be submitted with wrong captcha
        $commentForm = $postCrawler->selectButton("Send")->form();
        $client->followRedirects();
        $commentSubmitCrawler = $client->submit($commentForm, array(
            "mylk_bundle_blogbundle_comment[username]" => "mylk",
            "mylk_bundle_blogbundle_comment[email]" => "milonas.ko@gmail.com",
            "mylk_bundle_blogbundle_comment[content]" => "Test content",
            "mylk_bundle_blogbundle_comment[captcha]" => "abcdef"
        ));
        $this->assertTrue($commentSubmitCrawler->filter(".alert-error")->count() > 0);
        $client->followRedirects(false);
    }
}