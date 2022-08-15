<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Images
{
    public function product_images($product_image)
    {
        $files1 = scandir('assets/img/500');
        $images = array();
        foreach ($files1 as $value) {
            if (strpos($value,$product_image) !== false) {
                $images[] = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value);
            }
        }
        return $images;
    }
}
