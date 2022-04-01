<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class IndexController extends Controller
{
    public function indexAction()
    {


        // return '<h1>Hello World!</h1>';
    }
    public function SettingsAction()
    {
        //   fetch settings frm db 
        // send it to view 
        // update the exsisting id of seeting;
        $Setting = new Settings();
        $Setting = Settings::find();
        $this->view->setting = $Setting[0];

        if (true === $this->request->isPost()) {
            $Setting = new Settings();
            $Setting = Settings::find(['set_id' => '1']);
            // var_dump($Setting[0]);
            $Setting[0]->assign(
                $this->request->getPost(),
                [
                    'title_optimization',
                    'default_price',
                    'default_stock',
                    'default_zipcode',

                ]
            );
            $success =  $Setting[0]->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->message = "Updated  succesfully";
            } else {
                $this->view->message = "Updation Failed :<br>" . implode("<br>", $Setting[0]->getMessages());
            }
        }
    }

    public function getTokenAction()
    {
        $roles = new Roles();
        $roles = Roles::find();
        $this->view->roles = $roles;


        if (true === $this->request->isPost()) {
             print_r($this->request->getPost());


             $key = "example_key";
            $payload = array(
            "iss" => "http://Cedcoss.org",
            "aud" => "http://India.com",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role"=>$this->request->getPost('roles') );
            $jwt = JWT::encode($payload, $key, 'HS256');
            echo $jwt;
            // Phalcon Jwt code Commented 
            // // Defaults to 'sha512'
            // $signer  = new Hmac();

            // // Builder object
            // $builder = new Builder($signer);

            // $now        = new DateTimeImmutable();
            // $issued     = $now->getTimestamp();
            // $notBefore  = $now->modify('-1 minute')->getTimestamp();
            // $expires    = $now->modify('+1 day')->getTimestamp();
            // $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            // // Setup
            // $builder
            //     ->setAudience('https://cedcoss.io')  // aud
            //     ->setContentType('application/json')        // cty - header
            //     ->setExpirationTime($expires)               // exp 
            //     ->setId('101')                    // JTI id 
            //     ->setIssuedAt($issued)                      // iat 
            //     ->setIssuer('AKS')           // iss 
            //     ->setNotBefore($notBefore)                  // nbf
            //     ->setSubject($this->request->getPost('roles'))   // sub
            //     ->setPassphrase($passphrase)                // password 
            // ;

            // // Phalcon\Security\JWT\Token\Token object
            // $tokenObject = $builder->getToken();

            // // The token
            // echo "<br>";
            // echo "<h1>". $tokenObject->getToken()."</h1>";
            die;
        }
    }
}
