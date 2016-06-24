<?php
    class autoloader
    {
        private $paths=array();
        private $suffixes=array('.php');
        private $prefixes=array();

        public function __construct($paths='')
        {
            $path_arr=array();
            if(!empty($paths)){
                if(is_string($paths)){
                    array_push ($path_arr ,$paths);
                }
                if(is_array($paths)){
                    $path_arr = $paths;
                }
            }
            array_push ($path_arr ,'');
            $this->paths=array_unique($path_arr);
            
        }

        public function register()
        {
            spl_autoload_register(array($this, '_loadClass'));
        }

        //文件名添加后缀
        public function addSuffix($val)
        {
            $val_arr=array_merge($this->suffixes,$this->_fixHandel($val));
            $this->suffixes=array_unique($val_arr);
        }

        //文件名添加前缀
        public function addPrefix($val)
        {
            $val_arr=array_merge($this->prefixes,$this->_fixHandel($val));
            $this->prefixes=array_unique($val_arr);
        }

        private function _fixHandel($val)
        {
            $val_arr=array();
            if(!empty($val)){
                if(is_string($val)){
                    array_push ($val_arr ,$val);
                }
                if(is_array($val)){
                    $val_arr = $val;
                }
            }
            return $val_arr;
        }

        private function _loadClass($className)
        {
            $fileName = '';
            $namespace = '';
            // Sets the include path as the "src" directory
            // 
            $basePath = dirname(__FILE__).DIRECTORY_SEPARATOR;

            if (false !== ($lastNsPos = strripos($className, '\\'))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            //$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            $fileNameArr=array();

            if(!empty($this->prefixes)){
                foreach($this->suffixes as $suffix){
                    foreach($this->prefixes as $prefix){
                        array_push($fileNameArr, $prefix.$fileName.str_replace('_', DIRECTORY_SEPARATOR, $className).$suffix);
                    }
                }
            }else{
                foreach($this->suffixes as $suffix){
                    array_push($fileNameArr, $fileName.str_replace('_', DIRECTORY_SEPARATOR, $className).$suffix);
                }
            }

            

            $class_exist=false;
            foreach($this->paths as $path){
                if(substr($path,0,1)==='/'){
                    $path=substr($path,1);
                }
                if(substr($path,-1,1)==='/'){
                    $path=substr($path,0,-1);
                }
                if(!empty($path)){
                    $path.=DIRECTORY_SEPARATOR;
                }

                foreach($fileNameArr as $f){
                    $fullFileName = $basePath .$path  . $f;
                    if (file_exists($fullFileName)) {
                        require $fullFileName;
                        $class_exist=true;
                        break 2;
                    }
                }
            }
            if(!$class_exist){
                echo 'Class "'.$className.'" does not exist.';
            }
            
        }
    }


