<?php
namespace App\Traits;

trait ApiResponseTrait {

    public function success($message=null,$data=array(),$status=200){
        return response()->json(
            [
                'success'=>true,
                'message'=>$message,
                'data'=>$data
            ],
            $status
        );
    }

    public function fail($message=null,$data=array(),$status=400){
        return response()->json(
            [
                'success'=>false,
                'message'=>$message,
                'data'=>$data
            ],
            $status
        );
    }


}
