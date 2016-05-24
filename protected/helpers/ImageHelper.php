<?php

    class ImageHelper
    {

        public static function parseDimensions($dimensionString)
        {
            $width = 0;
            $height = 0;
            $dimensionString = trim($dimensionString);
            $arr = explode('x', $dimensionString);
            if(is_array($arr))
            {
                $width = $arr[0];
                if($arr[1])
                {
                    $height = $arr[1];
                }
            }

            return array($width, $height);
        }

        public static function getThumb($url, $width, $height, $alt = '', $title = '')
        {
            $htmlOptions = array();
            if($url)
            {
                if($title)
                {
                    $htmlOptions['title'] = $title;
                }

                try
                {
                    $filepath = Yii::getPathOfAlias('webroot').$url;
                    if(file_exists($filepath))
                    {
                        $extension = CFileHelper::getExtension($filepath);
                        $cacheDir = dirname($filepath);
                        $cacheFile = $cacheDir.'/'.basename($filepath, '.'.$extension).'_'.$width.'x'.$height.'.'.$extension;

                        if(file_exists($cacheFile))
                        {
                            return CHtml::image(str_replace(Yii::getPathOfAlias('webroot'), Yii::app()->request->getBaseUrl(true), $cacheFile), $alt, $htmlOptions);
                        }

                        if(!file_exists($cacheDir))
                        {
                            mkdir($cacheDir);
                        }

                        $extension = strtolower($extension);
                        if(in_array($extension, array('jpg', 'jpe', 'jpeg', 'gif', 'png', 'bmp')))
                        {
                            $image = WideImage::load($filepath);
                            $currentWidth = $image->getWidth();
                            $currentHeight = $image->getHeight();
                            
                            $proportion = round($width / $height, 2);
                            $currentProportion = round($currentWidth / $currentHeight, 2);
                            
                            if($proportion != $currentProportion)
                            {
                                $cropWidth = $currentHeight * $proportion;
                                $cropHeight = $currentWidth / $proportion;
                                
                                $image = $image->crop('center', 'center', $cropWidth, $cropHeight);
                            }
                            
                            $image = $image->resize($width, $height, 'outside');
                            
                            $image->saveToFile($cacheFile);
                            return CHtml::image(str_replace(Yii::getPathOfAlias('webroot'), Yii::app()->request->getBaseUrl(true), $cacheFile), $alt, $htmlOptions);
                        }
                    }
                }
                catch(Exception $e)
                {
                    return CHtml::image('', $alt, $htmlOptions);
                }
            }

            return CHtml::image('', $alt, $htmlOptions);
        }

        public static function getThumbLink($url, $width, $height)
        {
            $htmlOptions = array();
            if($url)
            {
                try
                {
                    $filepath = Yii::getPathOfAlias('webroot').$url;
                    if(file_exists($filepath))
                    {
                        $extension = CFileHelper::getExtension($filepath);
                        $cacheDir = dirname($filepath);
                        $cacheFile = $cacheDir.'/'.basename($filepath, '.'.$extension).'_'.$width.'x'.$height.'.'.$extension;

                        if(file_exists($cacheFile))
                        {
                            return str_replace(Yii::getPathOfAlias('webroot'), Yii::app()->request->getBaseUrl(true), $cacheFile);
                        }

                        if(!file_exists($cacheDir))
                        {
                            mkdir($cacheDir);
                        }

                        $extension = strtolower($extension);
                        if(in_array($extension, array('jpg', 'jpe', 'jpeg', 'gif', 'png', 'bmp')))
                        {
                            $image = WideImage::load($filepath);
                            $currentWidth = $image->getWidth();
                            $currentHeight = $image->getHeight();
                            
                            $proportion = round($width / $height, 2);
                            $currentProportion = round($currentWidth / $currentHeight, 2);
                            
                            if($proportion != $currentProportion)
                            {
                                $cropWidth = $currentHeight * $proportion;
                                $cropHeight = $currentWidth / $proportion;
                                
                                $image = $image->crop('center', 'center', $cropWidth, $cropHeight);
                            }
                            
                            $image = $image->resize($width, $height, 'outside');
                            
                            $image->saveToFile($cacheFile);
                            return str_replace(Yii::getPathOfAlias('webroot'), Yii::app()->request->getBaseUrl(true), $cacheFile);
                        }
                    }
                }
                catch(Exception $e)
                {
                    return '';
                }
            }

            return '';
        }

        public static function cleanCacheDir($url, $filename = null)
        {
            if($url)
            {
                $cacheDir = $url;

                $filename = substr($filename, 0, strrpos($filename, '.'));
                if(file_exists($cacheDir))
                {
                    self::deleteRecursive($cacheDir, $filename);
                }
            }
        }

        private static function deleteRecursive($path, $filename = null)
        {
            $it = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($path),
                            RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach($it as $file)
            {
                $baseName = $file->getBasename();
                if(in_array($baseName, array('.', '..')))
                {
                    continue;
                }
                elseif($file->isFile() || $file->isLink())
                {
                    if($filename)
                    {
                        if(substr($baseName, 0, strlen($filename)) == $filename)
                        {
                            unlink($file->getPathname());
                        }
                        else
                        {
                            continue;
                        }
                    }
                    else
                    {
                        unlink($file->getPathname());
                    }
                }
            }
        }

    }