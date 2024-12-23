<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
  public function getClient()
  {
    $client = new Client(['base_uri' => 'http://localhost']);
    return $client;
  }

  public function testLogin()
  {
    $data = [
      'email' => 'user@test.com',
      'password' => 'passwordTest',
    ];
    $request = $this->getClient()->request('POST', '/wsTest/login', ['body' => json_encode($data)]);
    $this->assertSame(200, $request->getStatusCode());
  }

  public function testBadLogin()
  {
    $data = [
      'email' => 'wrong user',
      'password' => 'wrong password',
    ];
    $this->expectException(ClientException::class);
    $this->getClient()->request('POST', '/wsTest/login', ['body' => json_encode($data)]);
  }

  public function testStoreListing()
  {
    $request = $this->getClient()->request('GET', '/wsTest/testStores');
    $this->assertSame(200, $request->getStatusCode());
    $storesList = json_decode($request->getBody()->getContents(),true);
    $this->assertSame("success", $storesList['status']);
    $this->assertSame("owner 1",$storesList['stores'][0]['store_owner']);
  }

  public function testUniqueStore()
  {
    $request = $this->getClient()->request('GET', '/wsTest/testStore/1');
    $this->assertSame(200, $request->getStatusCode());
    $store = json_decode($request->getBody()->getContents(),true);
    $this->assertSame("success", $store['status']);
    $this->assertSame("store 2",$store['store']['store_name']);
  }

  public function testCreatingStore()
  {
    $data = [
      'store_name' => 'name test 2',
      'store_adress' => 'adress test',
      'store_owner' => 'owner test',
      'store_created_at' => '2024-07-17',
    ];

    $request = $this->getClient()->request('POST', '/wsTest/testStore', ['body' => json_encode($data)]);
    $this->assertSame(201, $request->getStatusCode());
    $store = json_decode($request->getBody()->getContents(),true);
    $this->assertSame("success", $store['status']);
    $this->assertSame("Ressource créée",$store['message']);
  }

  public function testUpdatingStore()
  {
    $data = [
      'store_name' => 'name test 2',
      'store_adress' => 'adress test',
      'store_owner' => 'owner test',
      'store_created_at' => '2024-07-17',
    ];

    $request = $this->getClient()->request('PUT', '/wsTest/testStore/1', ['body' => json_encode($data)]);
    $this->assertSame(200, $request->getStatusCode());
    $store = json_decode($request->getBody()->getContents(),true);
    $this->assertSame("success", $store['status']);
    $this->assertSame("Ressource 1 mis à jour",$store['message']);
  }

  public function testDeletingStore()
  {
    $request = $this->getClient()->request('DELETE', '/wsTest/testStore/1');
    $this->assertSame(200, $request->getStatusCode());
    $store = json_decode($request->getBody()->getContents(),true);
    $this->assertSame("success", $store['status']);
    $this->assertSame("Ressource 1 supprimée",$store['message']);
  }
}