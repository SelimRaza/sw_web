<?php

namespace App\Http\Controllers\API;

use App\BusinessObject\MqttMessage;
use App\BusinessObject\MqttTopic;

use App\BusinessObject\MqttTopicEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AWS;

class Image extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }

    public function upImage(Request $request)
    {
        $dataDB = array();
        $path_name = "";
        $s3 = AWS::createClient('s3');
        if ($request->hasFile('fileUpload')) {
            $fileUpload = $request->file('fileUpload');
            $image_name = $request['path_name'].'/'.$fileUpload->getClientOriginalName();
            $s3->putObject(array(
                'Bucket' => 'prgfms',
                'Key' => $image_name,
                'SourceFile' => $fileUpload,
                'ACL' => 'public-read',
                'ContentType' => $fileUpload->getMimeType(),
            ));
            $dataDB["image_name"] = $fileUpload->getClientOriginalName();
        }
        if (isset($request['string_image'])) {
            $binaryData = base64_decode($request['string_image']);
            $s3->putObject(array(
                'Bucket' => 'prgfms',
                'Key' => $path_name,
                'Body' => $binaryData,
                'ContentType' => 'base64',
                'ContentEncoding' => 'image/jpeg',
                'ACL' => 'public-read',
            ));
          //  $dataDB["image_name"] = $image_name;
        }
        return $dataDB;
    }


}
