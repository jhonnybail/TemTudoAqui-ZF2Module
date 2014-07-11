<?php

namespace TemTudoAqui\Utils\Doctrine;

use TemTudoAqui\Utils\Data,
    TemTudoAqui\Utils\Net,
	Doctrine\DBAL\Types\StringType,
	Doctrine\DBAL\Platforms\AbstractPlatform;

class File extends StringType {

	public function convertToPHPValue($value, AbstractPlatform $platform){
        $value = parent::convertToPHPValue($value, $platform);
        if(!empty($value)){
            try{
                $urlRequest = new Net\URLRequest($value);
                $file = new Data\File($urlRequest);
            }catch(Net\NetException $e){
                if($e->getCode() == 6)
                    $file = new Data\String($value);
                else
                    throw $e;
            }
        }else
            $file = new Data\File();
        return $file;
    }
	
	public function convertToDatabaseValue($value, AbstractPlatform $platform){
		if($value){
			if($value instanceof Data\File)
				$value = $value->fileName.".".$value->extension;
			
			return $value;
		}
    }

    public function getName(){
        return 'tta_file';
    }
	
}