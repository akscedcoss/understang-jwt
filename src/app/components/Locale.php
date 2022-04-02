<?php


namespace App\Components;
use Phalcon\Mvc\Components;
use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        // Ask browser what is the best language
        $language = $_GET['lan']??'en';
        $messages = [];
        
        $translationFile = APP_PATH.'/message/' . $language . '.php';
       
        if (true !== file_exists($translationFile)) {
            $translationFile =APP_PATH.'/message/en.php';
            die;

        }
        
        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);
        
        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}