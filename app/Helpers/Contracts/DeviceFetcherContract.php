<?php
/**
 * Created by PhpStorm.
 * User: 750371433
 * Date: 04/08/2017
 * Time: 16:57
 */

namespace App\Helpers\Contracts;


interface DeviceFetcherContract
{
    public function getBrands();

    public function getAll();

    public function getByBrand($brand_name);
}