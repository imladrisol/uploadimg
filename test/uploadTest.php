<?php
$loader = require('../vendor/autoload.php');
$loader->add('', __DIR__ . '/test');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
}

use TestBootstrap\UploadImgTest;

class uploadTest extends  \PHPUnit\Framework\TestCase
{
    public $uploadimg,
        $testingImage,
        $_files = array();

    /**
     *  Initialize an array to mimic the properties $_FILES global
     */
    public function __construct()
    {
        $files = array(
            'ikea' => array(
                'name' => $this->testingImage = __DIR__ . "/monkey.jpg",
                'type' => 'image/jpg',
                'tmp_name' => $this->testingImage = __DIR__ . "/monkey.jpg",
                'error' => 0,
                //'size' => 17438,
            )
        );

        $this->uploadimg = new UploadImgTest($files);

    }


    /**
     * test array access offset name is created from $_FILES
     */
    public function testArrayAccessReadsFileNameFromArray()
    {
        $this->assertEquals($this->uploadimg['ikea'], true);
    }

    /**
     * test custom image renaming
     */
    public function testImageRenameReturnsNewName()
    {
        $this->uploadimg->setName('foo');
        $this->assertEquals($this->uploadimg->getName(), 'foo');
    }

    /**
     * test storage creation
     */
    public function testImageLocationReturnsAssignedValue()
    {
        $this->uploadimg->setLocation('family_pics');
        $this->assertEquals($this->uploadimg->getLocation(), 'family_pics');
    }

    /**
     * test image is uploaded based on the mime types set
     */
    public function testImageUploadAcceptsOnlyAllowedMimeTypes()
    {
        $this->uploadimg['ikea'];
        $this->uploadimg->setMime(array("png"));
        $upload = $this->uploadimg->upload();
        $this->assertEquals(
            $this->uploadimg["error"],
            "Invalid File! Only (png) image types are allowed");
    }

    /**
     * test image mime return
     */
    public function testReturnValueOfImageMimeAfterImageUpload()
    {
        $this->uploadimg['ikea'];
        $upload = $this->uploadimg->upload();
        $this->assertSame($upload->getMime(), 'jpeg');
    }

    /**
     * test image location return
     */
    public function testReturnValueOfImageLocationAfterImageUpload()
    {
        $this->uploadimg['ikea'];
        $this->uploadimg->setLocation('images');
        $upload = $this->uploadimg->upload();
        $this->assertSame($upload->getLocation(), 'images');
    }

    /**
     * test image full path return
     */
    public function testReturnValueOfImageFullPathAfterImageUpload()
    {
        $this->uploadimg['ikea'];
        $this->uploadimg->setLocation('images');
        $this->uploadimg->setName('2012');
        $upload = $this->uploadimg->upload();
        $getMime = $this->uploadimg->getMime();
        $this->assertSame($upload->getFullPath(), 'images/2012.' . $getMime);
    }

    /**
     * test image json value return
     */
    public function testReturnValueOfImageJsonInfoAfterImageUpload()
    {
        $this->uploadimg['ikea'];
        $upload = $this->uploadimg->setName('we_belive_in_json')->upload();
        $this->assertSame($upload->getJson(), 
            '{"name":"we_belive_in_json","mime":"jpeg", "location":"uploadimg","fullpath":"uploadimg\/we_belive_in_json.jpeg"}');

    }


}

