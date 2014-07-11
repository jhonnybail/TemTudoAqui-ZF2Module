<?php

namespace TemTudoAqui\Utils\Doctrine;

use TemTudoAqui\Utils\Data,
	Doctrine\DBAL\Types\DateTimeType,
	Doctrine\DBAL\Platforms\AbstractPlatform;

class DateTime extends DateTimeType {
	
	public function convertToPHPValue($value, AbstractPlatform $platform){
        $dateTime = parent::convertToPHPValue($value, $platform);

        if ( ! $dateTime) {
            return $dateTime;
        }

        return new Data\DateTime($dateTime->format('Y-m-d H:i:s'), "Y-m-d H:i:s");
    }
	
	public function convertToDatabaseValue($value, AbstractPlatform $platform){
		if($value){
			if(is_string($value))
				$value = new Data\DateTime($value, "Y-m-d H:i:s");
			
			return $value->format("Y-m-d H:i:s");		
		}
    }

    public function getName(){
        return 'tta_datetime';
    }
	
}