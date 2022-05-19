<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API-REST</title>
</head>

<body>
    <h1>hola mundo index</h1>
    <pre>
        <?php
        print_r('hola<br>');
        // print_r($_SERVER);
        // print_r($_SERVER["REQUEST_URI"]);
        require_once '../PHP-Mailer/index.php';
        require_once './class/assets/emailTemplates/plantillasCorreos.php';
        $emailCodeTemplate = emailCodeTemplate(123456);
//         $str = <<<'EOD'
//         <div style="width: 100%; height: 300px; background-color: #D7F8FF; border-radius: 20px;">
//         <div style="background-color: #0095E5;border-top-left-radius: 20px;border-top-right-radius: 20px;">
//           <h1 style="color:white;text-align: center;padding-top: 10px;padding-bottom: 10px;">Tu código de verificación</h1>
//         </div>
//         <div style="text-align: center;margin-top: 40px;">
//           <p>Ingrese este código de verificación en el campo del formulario:</p>
//         </div> 
//         <div style="margin-top: 60px;">
//           <div style="text-align: center;">
//             <span style="background-color: #ddd;border-radius: 40px;padding: 20px;padding-left: 25px;font-size: 36px;letter-spacing: 10px;">666666</span>
//           </div>
//         </div> 
//         </div>
        
// EOD;
        $mail = new Mail();
        // print_r($str);
        print_r($emailCodeTemplate);


$mail->sendMail("pacopill63@hotmail.com", "Email Subject2", $emailCodeTemplate);
// $mail->sendMail("pacopill63@gmail.com", "Email Subject3", $str);
?>
        </pre>
</body>

</html>