<?php

namespace Fasterfy;

class Fasterfy
{
    protected $lastEvents=array();
    protected $rootEvent;
    protected $outputDir;
    protected $registering = true;
    protected static $logFile = false;
    /**
     * @param int onceEvery -> set to 0 to never register the file
     *
     * @return void
     */
    public function __construct($outputDir,int $onceEvery=10)
    {
        if (!$onceEvery) {
            $this->setRegistering(false);
        } else {
            $rand = rand(1,$onceEvery);
            if ($rand !== 1) {
                $this->setRegistering(false);
            }
        }


        $this->registerCycle();
        $event = new Event("cycle",$_SERVER['REQUEST_URI']);
        $this->setRootEvent($event);
        $this->prependLastEvent($event);
        $this->setOutputDir($outputDir);
    }

    public function registerCycle()
    {
        if (!defined("FASTERFY_CYCLE")) {
            define("FASTERFY_CYCLE",md5(microtime(true)."_".mt_rand()));
        }
    }

    public function track($category,$name="")
    {
        $event = new Event($category,$name);
        $lastEvent = $this->getLastEvent();
        $lastEvent->addChild($event);
        $this->prependLastEvent($event);
        return $event;
    }

    /**
     * Ends the cycle and writes the file
     *
     * @return Fasterfy/Event
     */
    function end($categoryCounterAsProperty=true)
    {
      try {
        $root = $this->getRootEvent();
        $root->stop(true);
        $counter = Filter::getCategoryCounter();
        $root->categoryCounter = $counter;
        $array = $root->toArray(true);
        if (count($array['childs'])) {
            if ($this->getRegistering()) {
                $json = json_encode($array,JSON_PRETTY_PRINT);
                $file = fopen($this->getOutputDir()."/".FASTERFY_CYCLE.".json","w+");
                fwrite($file,$json);
                fclose($file);
            }
        }
      } catch (Exception $e) {
        if ($file = $this->getLogFile()) {
          $message = $e->getMessage();
          $fhandler = fopen($file,"a+");
          fwrite($fhandler,$message."\n");
          fclose($fhandler);
        }
      }
    }



    /**
     * Get the value of Root Event
     *
     * @return mixed
     */
    public function getRootEvent()
    {
        return $this->rootEvent;
    }

    /**
     * Set the value of Root Event
     *
     * @param mixed rootEvent
     *
     * @return self
     */
    public function setRootEvent(Event $rootEvent)
    {
        $this->rootEvent = $rootEvent;

        return $this;
    }

    /**
     * Get the value of Output Dir
     *
     * @return mixed
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * Set the value of Output Dir
     *
     * @param mixed outputDir
     *
     * @return self
     */
     public function setOutputDir($outputDir)
     {
         if (!is_dir($outputDir)) {
             throw new Exception("$outputDir isn't a valid directory", 1);
         }

         if (!is_writable($outputDir)) {
             throw new Exception("$outputDir isn't writable", 1);
         }
         $this->outputDir = $outputDir;

         return $this;
     }


    protected function getLastEvent()
    {
        foreach ($this->lastEvents as $event) {
            if (!$event->isStopped()) {
                return $event;
            }
        }
        throw new Exception("All events stopped.", 1);

    }

    protected function prependLastEvent(Event $event)
    {
        array_unshift($this->lastEvents,$event);
        return $this;
    }


    /**
     * Get the value of Last Events
     *
     * @return mixed
     */
    public function getLastEvents()
    {
        return $this->lastEvents;
    }

    /**
     * Set the value of Last Events
     *
     * @param mixed lastEvents
     *
     * @return self
     */
    public function setLastEvents($lastEvents)
    {
        $this->lastEvents = $lastEvents;

        return $this;
    }

    function stop()
    {
        $this->getLastEvent()->stop();
        return $this;
    }
    /**
     * Get the value of Registering
     *
     * @return mixed
     */
    public function getRegistering()
    {
        return $this->registering;
    }

    /**
     * Set the value of Registering
     *
     * @param mixed registering
     *
     * @return self
     */
    public function setRegistering($registering)
    {
        $this->registering = $registering;

        return $this;
    }

    public function compress($compressedFileName=false){
      $outputDir = $this->getOutputDir();
      $files = glob($outputDir."/*.json");
      if (count($files)) {
          if (!$compressedFileName) {
            $compressedFileName = $outputDir.'/compressed'.date("Y-m-d-H-i-s").'.tar';
          }
          if (file_exists($compressedFileName)) {
              unlink($compressedFileName);
          }
          if (file_exists($compressedFileName.".gz")) {
              unlink($compressedFileName.".gz");
          }
          $phar = new \PharData($compressedFileName);
          foreach ($files as $file) {
            $phar->addFile($file,basename($file));
          }
          $p1 = $phar->compress(\Phar::GZ);
          foreach ($files as $file) {
            unlink($file);
          }
          unlink($compressedFileName);
          return $compressedFileName.".gz";
      }
      return null;

    }





    /**
     * Get the value of Log File
     *
     * @return mixed
     */
    public static function getLogFile()
    {
        return self::$logFile;
    }

    /**
     * Set the value of Log File
     *
     * @param mixed logFile
     *
     * @return self
     */
    public static function setLogFile($pathLog)
    {
        self::$logFile = $pathLog;
    }
}
