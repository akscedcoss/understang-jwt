<?php

// declare(strict_types=1);

namespace App\Console;



use Phalcon\Cli\Task;


class FilestTask extends Task
{  
    //  Create a task that will remove all logs files
    //php cli.php Filest delLogFiles
    public function delLogFilesAction()
    {
        //define('BASE_PATH', dirname(__DIR__));
        //define('APP_PATH', BASE_PATH . '/app');

        echo 'This is the Delete File task and the del LogAction' . PHP_EOL;
        echo "Deleteing All Log Files...";
        $files = glob(APP_PATH . "/logger/*.log");

        if ($files) {
            foreach ($files as $key => $value) {
                if (unlink($value)) {
                    echo $value . "File deleted Sucesss";
                } else {
                    echo "Something Went Wrong";
                }
            }
        } else {
            echo "No Files";
        }
    }

     // task 5 Create a task to remove ACL cache file
     //php cli.php Filest task5
     public function task5Action()
    {
         echo 'Task to remove ACL cache file' . PHP_EOL;
 
        $file=APP_PATH ."/security/acl_copy.cache";
        if($file)
        {
            echo "Removing File.....". PHP_EOL;
            if (unlink($file)) {
                echo " File Removed Succes ....". PHP_EOL;
            }
            else {
                echo "Failed to remove File.....". PHP_EOL;
            }

        }
        else{
            echo "File doest Not exist";
        }
     
    }


}
