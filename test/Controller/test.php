<?php
namespace App\Test\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class test extends TestCase{

    public $client;
    
    public function __construct(HttpClientInterface $client)
    {
        $this -> client = $client;
    }

    public function testGetPeliculas($client){
        $client-> request ('GET', '/peliculas');
        $this -> assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetPeliculasID ($client)
    {
        $client-> request ('GET', '/peliculas/:id');
        $this -> assertEquals(200, $client->getResponse()->getStatusCode());
    }

}