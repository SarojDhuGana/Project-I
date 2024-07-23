<?php
// tests/AdminManagerTest.php

use PHPUnit\Framework\TestCase;

require_once (__DIR__ . '/../admin/category/CategoryManager.php');
// require_once (realpath(dirname(__FILE__) . '/../admin/category/CategoryManager.php'));
class CategoryManagerTest extends TestCase
{
    private $categoryManager;

    protected function setUp(): void
    {
        $this->categoryManager = new CategoryManager();
    }



    public function testCreateCategory()
    {
        $result = $this->categoryManager->createCategory("New Test Category", null);
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }
    public function testgetCategoryDetail()
    {
        $result = $this->categoryManager->getCategoryDetail(11);
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }
    public function testupdateCategory()
    {
        $result = $this->categoryManager->updateCategory("Test Category update", null, 11);
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }
    public function testdeleteCategory()
    {
        $result = $this->categoryManager->deleteCategory(11);
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }
    public function testList()
    {
        $result = $this->categoryManager->list();
        $resultArray = json_decode($result, true);
        error_log(print_r($resultArray, true));

        $this->assertNotEmpty($resultArray);
    }

    protected function tearDown(): void
    {
        // Clean up any test-specific setup
    }
}
