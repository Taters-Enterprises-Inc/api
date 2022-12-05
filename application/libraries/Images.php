<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Images
{
    public function product_images($url,$product_image, $image_extension)
    {
        $files1 = scandir($url);
        $images = array();
        foreach ($files1 as $value) {
            if (strpos($value,$product_image) !== false) {
                $images[] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value) . $image_extension;
            }
        }
        return $images;
    }
}
