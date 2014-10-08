<?php

namespace Mylk\Bundle\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    private $em;

    public function __construct(){
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get("doctrine.orm.entity_manager");
    }
    
    public function testLogin(){
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request("GET", "/admin");
        
        // redirection to login form,
        // there is a csrf token field
        $this->assertTrue($crawler->filter("input[name='_csrf_token']")->count() > 0);

        
        // error on wrong credentials
        $form = $crawler->selectButton("Login")->form();
        $loginCrawler = $client->submit($form, array(
            "_username" => "admin",
            "_password" => "WRONG_PASS"
        ));
        $this->assertTrue($loginCrawler->filter(".alert-error")->count() === 1);

        
        // login with correct credentials,
        // last login timestamp before and after login
        /*
        $userRepo = $this->em->getRepository("MylkBlogBundle:User");
        $previousLogin = $userRepo->findOneBy(array("username" => "admin"))->getLastLogin();
        
        // trying to overcome lazy loading, doesn't work either :-(
        $query = $this->em->createQuery("SELECT u FROM MylkBlogBundle:User u WHERE u.username = 'admin'");
        $queryFinal = $query->setFetchMode("MylkBlogBundle\User", "lastLogin", "EAGER");
        echo("\n" . $queryFinal->getSingleResult()->getLastLogin());
         */
        
        $form = $crawler->selectButton("Login")->form();
        $loginCrawler = $client->submit($form, array(
            "_username" => "admin",
            "_password" => "adminpass"
        ));
        $this->assertTrue($loginCrawler->filter(".alert-error")->count() === 0);

        // get lastLogin again and then assert
        // $this->assertTrue($previousLogin !== $lastLogin);

        // logout works,
        // leads to login form
        $logoutLink = $loginCrawler->selectLink("Logout")->link();        
        $logoutCrawler = $client->click($logoutLink);
        $this->assertTrue($logoutCrawler->filter("input[name='_csrf_token']")->count() > 0);
    }
}