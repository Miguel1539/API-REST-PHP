<?php
function emailCodeTemplate($code)
{
  $str = <<<'EOD'
<div style="width: 100%; height: 300px; background-color: #D7F8FF; border-radius: 20px;">
<div style="background-color: #0095E5;border-top-left-radius: 20px;border-top-right-radius: 20px;">
  <h1 style="color:white;text-align: center;padding-top: 10px;padding-bottom: 10px;">Tu código de verificación</h1>
</div>
<div style="text-align: center;margin-top: 40px;">
  <p>Ingrese este código de verificación en el campo del formulario:</p>
</div> 
<div style="margin-top: 60px;">
  <div style="text-align: center;">
    <span style="background-color: #ddd;border-radius: 40px;padding: 20px;padding-left: 25px;font-size: 36px;letter-spacing: 10px;">
EOD;
  $str .= $code;
  $str .= <<<'EOD'
    </span>
  </div>
</div>
</div>
EOD;

  return $str;
}
function emailCodeTemplateUserNames($arr)
{
  $str = <<<'EOD'
<div style="width: 100%;height: 300px;background-color: #d7f8ff;border-radius: 20px;">
<div style="background-color: #0095e5;border-top-left-radius: 20px;border-top-right-radius: 20px;">
  <h1 style="color: white;text-align: center;padding-top: 10px;padding-bottom: 10px;">Tus nombres de usuario</h1>
</div>
<div style="margin-top: 60px">
  <div style="text-align: center">
    <ul style="list-style: none;padding: 0;margin: 0;">
EOD;
  foreach ($arr as $key => $value) {
    $str .= <<<'EOD'
<li style="display: inline-block;margin: 0 10px;padding: 10px;border: 1px solid #0095e5;border-radius: 20px;background-color: #0095e5;color: white;font-size: 20px;font-weight: bold;">
EOD;
    $str .= $value['username'];
    $str .= '</li>';
  }
  $str .= <<<'EOD'
    </ul>
  </div>
</div>
EOD;
  return $str;
}
