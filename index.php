<?php 

set_time_limit(3000);
date_default_timezone_set("America/Sao_Paulo");
ini_set('error_reporting', E_ALL ^ E_NOTICE);
gc_enable();

error_clear_last();


class SAAS
{
    function __construct()
    {
        session_start(); 
        echo 'Iniciando '.PHP_EOL;
        sleep(2);
        $this->socketConn();
    }

    protected function socketConn()
    {
        try
        {
            $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $conn = $this->connect();
            while($server!= null)  
            {
                socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, PHP_NORMAL_READ);
                socket_bind($server, self::ADDRESS, self::PORT);
                socket_listen($server,5);
                $client = socket_accept($server); 
                socket_set_nonblock($server);
                $go =true;
                $data = null;
                do
                {
        
                    
                    $result = socket_read($client, 1024, 1);
                    if("" != bin2hex($result))
                    {
                       continue;
                    }

                    if(strlen($result)>='1020')
                    {
                        $data = bin2hex($result);
                        echo $data.PHP_EOL;
                        $SQL ="INSERT INTO predatasets (uid, datetime, raw_data, raw_bytes, rmc_ip, rmc_mac , processed ) VALUES (? , ? , ? , ? , ? , ? , ?)";
                        if($conn->prepare($SQL)->execute([uniqid(), 'NOW()',$data, $data, '--__--', '--__--', '0' ]) == true)
                        {
                            echo 'saved';
                        }
                    }
                    else echo 'len < 1000';
                    // $output = str_split($data);
                    // $mac_addr = $output['12'].$output['13'].$output['14'].$output['15'].$output['16'].$output['17'].$output['18'].$output['19'].$output['20'].$output['21'].$output['22'].$output['23'];
                    // $mac_addr = $this->formatMacAddr($mac_addr);
                    // if (false === filter_var( $mac_addr, FILTER_VALIDATE_MAC)) {
                    //    $mac_addr = "NOT PRESENT";
                    // }
                    // print_r($mac_addr); echo "<br>";  
                    // print_r($data); echo "<br>";  
                    //echo "<br>";   print_r(strlen(bin2hex($result))); echo "<br>"; a
                }
                while ($go == true);
            }
        }
        catch(\Exception $err)
        {
            var_dump($err); die();
        }

    }

    private function formatMacAddr($string)
    {
        return  implode(":", str_split(str_replace(".", "", $string), 2));
    }

    private function connect()
    {
            $host = "127.0.0.1";
            $port = "5432";
            $dbname = "IOTDatabase";
            $user= "postgres";
            $password = 'QVb2joc4/4$xzfSD';	
            
            $conn  = new \PDO('pgsql:host='.$host.';port='.$port.';dbname='.$dbname.';user='.$user.';password='.$password);
            $conn ->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        
    }


    const ADDRESS = "192.168.1.50";
    const PORT = 502;

}
new SAAS();


?>