<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Helper
{
    static function formatPagination($data)
    {
        return [
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ];
    }

    // param price { double }
    // param gst { double }
    // return { double }
    static function getPercentageToValue($price, $percentage)
    {
        return floatVal(($percentage / 100) * floatVal($price));
    }

    // calculate gst
    // param price { double }
    // param gst { double }
    static function calculateGST($price, $gst)
    {
        return self::getPercentageToValue($price, $gst);
    }

    // validate email
    // param email { string }
    // return { boolean }
    static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // param price { double }
    // param offer { double }
    // param isPercentage { boolean }
    // return { boolean }
    static function calculateOffer($productPrice, $offer, $offerType = false)
    {
        // ? isPercentage is true
        // ? then calculate percentage otherwise value offer is price
        return $offerType ? $offer : self::getPercentageToValue($productPrice, $offer);
    }


    // parse normal date string return mysql timestring dataformat
    static function formatTimestamp($date)
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    // check login use is company owner or company employee
    // if company owner set primary key
    // other wise employee set company id
    static function getCompanyId($user)
    {
        return $user->role == 'company' ? $user->id : $user->company_id;
    }

    // generate code <date> - <monthOfAlphapetics> - <fullYear> - <productType>
    // first date
    // 12 months A - L letters based on month
    // middle full year
    // last which type of item
    static function generateCode($prefix, $count)
    {
        $MONTHS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        return $prefix . '-' . date('d') . $MONTHS[intVal(date('m')) - 1] . date('Y') . $count + 1;
    }

    // parse phone number send http client request and send otp
    // successfully sended return true otherwise false
    static function send_otp($phoneNumber)
    {
        $otp = strVal(random_int(100000, 999999));
        return $otp;
    }

    // get access token token to verify otp
    // successfully verified return true otherwise false
    static function verify_otp($exitingOtp, $otp)
    {

        return ($otp == $exitingOtp);
    }

    // get request file object and directory name
    // store File on specified directory and return filename with extension
    static function uploadFile($requestFileObj, $uploadDir)
    {
        $fileName = Str::random(10) . '.' . $requestFileObj->getClientOriginalExtension();
        $requestFileObj->move(public_path('uploads/' . $uploadDir), $fileName);
        return $fileName;
    }

    // parse filename and directory return absolute path
    static function getFilePath($fileName, $uploadDir)
    {
        return asset("uploads/$uploadDir/$fileName");
    }

    static function fileExists($fileName, $uploadDir)
    {
        return file_exists(public_path("uploads/$uploadDir/$fileName"));
    }

    static function formatPrice($price)
    {
        return '₹ ' . preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $price);
    }

    static function formatDate($timestamp)
    {
        return date('M d, Y', strtotime($timestamp));
    }

    static function formatDateTime($timestamp)
    {
        return date('M d, Y h:i A', strtotime($timestamp));
    }

    static function getOrderStatus($status)
    {
        $ORDER_STATUS = ["pending", "confirmed", "arrived", "dispatched", "delivered", "canceled"];

        return $ORDER_STATUS[$status];
    }

    static function getPaymentStatus($status)
    {
        $PAYMENT_STATUS = ["pending", "paid", "unpaid", "failed"];

        return $PAYMENT_STATUS[$status];
    }

    static function getBannerType($type)
    {
        $BANNER_TYPE = ["banner", "category", "item"];
        return $BANNER_TYPE[$type - 1];
    }

    static function getType($type)
    {
        $TYPES = [
            1 => "courses",
            2 => "test",
            3 => "material",
            4 => "subject"
        ];

        return $TYPES[$type];
    }
}

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected $validateRules = [];

    public function apiValidate($requestData)
    {
        $validate = Validator::make($requestData, $this->validateRules);

        if ($validate->fails()) {
            return ['errors' => $validate->errors()];
        }
    }

    public function apiResponse($responseData, $status = 200)
    {
        return response()->json($responseData, $status);
    }
}
