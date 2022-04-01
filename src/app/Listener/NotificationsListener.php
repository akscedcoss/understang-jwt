<?php

namespace App\Listener;

use Phalcon\Events\Event;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class NotificationsListener
{
    public function beforeHandleRequest(Event $event, $application)
    {

        $aclfile = APP_PATH . '/security/acl.cache';
        if (is_file($aclfile) == true) {

            $acl = unserialize(
                file_get_contents($aclfile)
            );
            //  $role = $application->request->get('role');
            $bearer = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vQ2VkY29zcy5vcmciLCJhdWQiOiJodHRwOi8vSW5kaWEuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDAsInJvbGUiOiJDdXN0b21lcjEifQ.GMcnj5XHfN0QrTspUh7c0MuQa6UcNZZkNZwJheKeeHY";
            if ($bearer) {
                try {
                    $key = "example_key";
                    $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
                    print_r($decoded->role);
                    $role=$decoded->role;
                
                    // // Parse the token
                    // $parser      = new Parser();
                    // // Phalcon\Security\JWT\Token\Token object
                    // $tokenObject = $parser->parse($bearer);

                    // $now           = new \DateTimeImmutable();
                    // $expires        = $now->getTimestamp();
            
                    // $validator = new Validator($tokenObject, 100);

                    // $validator->validateExpiration($expires);

                    // $claims = $tokenObject->getClaims()->getPayload();
                    // // Role set 
                    // $role = $claims['sub'];
                    

                } catch (\Exception $e) {
                    echo $e->getMessage();
                    die;
                }
            } else {
                echo "Token Not Providedd";
                die;
            }
       
            $req = explode("/", $_SERVER['REQUEST_URI']);

            // var_dump($acl);
            $controller = ucwords($req[1]) ?? 'index';
            $action = $req[2] ?? 'index';
            // echo $controller;
            // echo $action;
            //  die;
            if (!$role || true !== $acl->isAllowed($role,   $controller, $action)) {
                echo "access denied";
                //  echo "<pre>";
                //  print_r($acl);
                //  echo "</pre>";
                 die();
            }
        } else {

            echo "No ACL ";
            //  die();
        }
    }










    public function beforeSend(Event $event, $component, $setting)
    {
        $data = [
            'Name' => $component->request->getPost('Name'),
            'Description' => $component->request->getPost('Description'),
            'Tags' => $component->request->getPost('Tags'),
            'Price' => $component->request->getPost('Price'),
            'Stock' => $component->request->getPost('Stock')
        ];
        // print_r($component->request->getPost('Name'));

        // echo($setting->title_optimization);
        // with tags.. witout tags
        if ($setting->title_optimization == 'With tags') {
            //Marge name and tags
            $data['Name'] = $data['Name'] . ' ' . $data['Tags'];
        }
        // if price is empty
        if (empty($data['Price'])) {
            $data['Price'] = $setting->default_price;
        }
        // if stock i empty 
        if (empty($data['Stock'])) {
            $data['Stock'] = $setting->default_stock;
        }

        return $data;
    }
}
