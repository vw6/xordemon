<?php   
class main{
    //const
    const url = "https://syn.su/testwork.php";
    const mailerror = "proshin11091977@gmail.com";

    //получение
    public function connectPull()
    {
        $url = self::url;
        $post_data = array("method" => "get"); 
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $cart = json_decode($output);
        if ($cart==null){
           self::log("connection error \n");
            exit;
        }else{
                    $message = $cart->response->message;
        $key = $cart->response->key;
        return $array = array(
            "message" => $message,
            "key" => $key,);    
        }
      
    }
    //отправка
    public function connectPush($otv = null)
    {
        $url = self::url;
        $post_data = array("method"=>'update', "message"=>$otv);   
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $cart = json_decode($output);
        if ($cart->response != "Success"){
            return $cart;
        }else{
            return $cart->response;
        }
        
    }
    // шифрование
    function xorAndBase($string = '', $key = ''){
        for ($i = 0;$i<strlen($string);$i++){
            $string[$i] = ($string[$i]^$key[$i % strlen($key)]);
        }
        return $xorbasestring = base64_encode($string);
    }
    //логирование
    public function log(string $var){
        $file = file_exists("logfile.log");
        if($file){ 
        $text = file_get_contents("logfile.log");
        $m = $var;
        file_put_contents("logfile.log", $text.$m);
        }else{ 
        $m = $var;
        file_put_contents("logfile.log", $m);  
        }    
    }
}
    // основной цикл
    // отправка раз в час
    $main = new main;  
    while(1){
        if($main->connectPull() == null){
            $main->log("connection error");
            exit;
        }else{
            $connectpull = $main->connectPull();
            $message = $connectpull['message'];
            $key = $connectpull['key']; 
            $otv = $main->xorAndBase($message, $key);
            if($main->connectPush($otv) != "Success"){
                $loge = ("error: ".date("F j, Y, g:i a")."\n");
                $main->log($loge);
                // mail(self::mailerror, "error", $main->connectPush($otv)); 
                exit;
            }else{
                $logs = ("Success: ".date("F j, Y, g:i a") . "\n");
                $main->log($logs);
            }
        }
        sleep(3600);
    }
?>

