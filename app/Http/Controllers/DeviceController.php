<?php
/**
 * Created by PhpStorm.
 * User: 750371433
 * Date: 01/08/2017
 * Time: 17:34
 */

namespace App\Http\Controllers;

use App\Helpers\Contracts\DeviceFetcherContract;
use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;


class DeviceController extends BaseController
{
    public function index(DeviceFetcherContract $fetcher, $brand = null) {
        $result = $fetcher->getBrands();

        return response()->json($result);
    }
}