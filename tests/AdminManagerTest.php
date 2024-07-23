<?php
// tests/AdminManagerTest.php

use PHPUnit\Framework\TestCase;

// require_once '../admin/AdminManager.php'; // Adjust path as needed
// require_once (__DIR__ . './../admin/AdminManager.php');
require_once (realpath(dirname(__FILE__) . '/../admin/AdminManager.php'));
class AdminManagerTest extends TestCase
{
    private $adminManager;

    protected function setUp(): void
    {
        $this->adminManager = new AdminManager();
    }

    public function testCreateAdmin()
    {
        $result = $this->adminManager->createAdmin('password123', 'test@example.com', 'Test Admin');
        $resultArray = json_decode($result, true);

        $this->assertTrue($resultArray['success']);
        $this->assertEquals('Data stored successfully', $resultArray['message']);
    }

    public function testLogin()
    {
        // Assuming test data already exists in your database for testing
        $result = $this->adminManager->login('test@example.com', 'password123');
        $resultArray = json_decode($result, true);

        // Log the result array to inspect what's returned
        error_log(print_r($resultArray, true));
        $this->assertNotEmpty($resultArray);

    }

    public function testSellerList()
    {
        $result = $this->adminManager->sellerList();
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }

    public function testBuyerList()
    {
        $result = $this->adminManager->buyerList();
        $resultArray = json_decode($result, true);

        $this->assertNotEmpty($resultArray);
    }
    public function testdeleteUserById()
    {
        $result = $this->adminManager->deleteUserById(4);
        $resultArray = json_decode($result, true);

        $this->assertNotEmpty($resultArray);
    }

    public function testsearchBuyersName()
    {
        $result = $this->adminManager->searchBuyersName("Dan");
        $resultArray = json_decode($result, true);

        $this->assertNotEmpty($resultArray);
        // Add more assertions based on expected seller list data
    }

    public function testsearchSellerName()
    {
        $result = $this->adminManager->searchSellersName("Dan");
        $resultArray = json_decode($result, true);

        $this->assertNotEmpty($resultArray);
        // Add more assertions based on expected seller list data
    }


    protected function tearDown(): void
    {
        // Clean up any test-specific setup
    }
}
