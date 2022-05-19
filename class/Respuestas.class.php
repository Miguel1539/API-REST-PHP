<?php

class Respuestas
{
    private $response = [
        'status' => "OK",
        'result' => array()
    ];
    //success 
    public function success($arr)
    {
        $this->response['result'] = $arr;
        return $this->response;
    }


    public function error_200(String $msg = "Datos Incorrectos")
    {
        $this->response['status'] = "error";
        $this->response['result'] = [
            'error_id' => '200',
            'error_msg'  => $msg
        ];
        return $this->response;
    }
    public function error_405()
    {
        $this->response['status'] = "error";
        $this->response['result'] = [
            'error_id' => '405',
            'error_msg'  => 'Metodo no encontrado'
        ];
        return $this->response;
    }

    public function error_403()
    {
        $this->response['status'] = "error";
        $this->response['result'] = [
            'error_id' => '403',
            'error_msg'  => 'No tienes permisos'
        ];
        return $this->response;
    }

    public function error_400()
    {
        $this->response['status'] = "error";
        $this->response['result'] = [
            'error_id' => '400',
            'error_msg'  => 'Datos enviados incompletos o con formato incorrecto'
        ];
        return $this->response;
    }

    public function error_500($error = "Error en el servidor")
    {
        $this->response['status'] = "error";
        $this->response['result'] = [
            'error_id' => '500',
            'error_msg'  => $error
        ];
        return $this->response;
    }

    


}
