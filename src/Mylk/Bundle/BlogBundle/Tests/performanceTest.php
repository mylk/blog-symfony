<?php

namespace Mylk\Bundle\BlogBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class performanceTest extends WebTestCase
{
    public function testIndex(){
        $client = static::createClient();

        $client->enableProfiler();
        $client->request("GET", "/");

        if($client->getProfile()){
            $profile = $client->getProfile();
            $profileToken = $profile->getToken();

            // queries
            $this->assertLessThan(20, $profile->getCollector("db")->getQueryCount(), sprintf("Failed while checking DB queries count (token %s)", $profileToken));

            // execution time
            $this->assertLessThan(200, $profile->getCollector("time")->getDuration(), sprintf("Failed while checking execution time (token %s)", $profileToken));

            // memory load
            $this->assertLessThan(26.0, ($profile->getCollector("memory")->getMemory() / 1024000), sprintf("Failed while checking memory load (token %s)", $profileToken));
        }
    }
}