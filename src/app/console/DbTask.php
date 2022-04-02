<?php

// declare(strict_types=1);

namespace App\Console;

use Orders;
use Phalcon\Cli\Task;
use Products;
use Settings; 


class DbTask extends Task
{
    public function task3Action($price)
    {
        // php cli.php Db task3
        echo " Db task 3 Default Price =>".$price;
        $set=Settings::find();
        $set[0]->default_price=$price;
        $set[0]->save();
        print_r(json_decode(json_encode($set)));

        
    }

    public function task4Action()
    { 
        // php cli.php Db task4
        echo " Db task 4 ";
        $prods=Products::find(
            [
                'conditions' => 'Stock < :qty:',
                'bind'       => [
                    'qty' => 10,
                ],
            ]  
        );
        print_r(json_decode(json_encode($prods)));
        
    }
    //  Task To Get Recent Orders 
    public function task6Action()
    { 
        // php cli.php Db task6
        echo " Db task 6 today Date=> ".date("Y-m-d");
        $orders=Orders::find(
            [
                'conditions' => 'date >= :today:',
                'bind'       => [
                    'today' =>date("Y-m-d"),
                ],
            ]  
        );
        print_r(json_decode(json_encode($orders)));
        
    }
    // Create a task to remove ACL cache file
}
