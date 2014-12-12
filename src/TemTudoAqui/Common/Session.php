<?php

namespace TemTudoAqui\Common;

use Zend\Http\Request,
    TemTudoAqui\System,
    TemTudoAqui\Utils\Data\File,
    TemTudoAqui\Utils\Net\URLRequest,
    TemTudoAqui\Utils\Net\FileReference,
    TemTudoAqui\Utils\Data\ArrayObject,
    Zend\Json;

class Session extends ArrayObject {

    private static  $file   = null;

    public function __construct(Request $request)
    {

        if(!(self::$file instanceof File)){
            self::$file = new File(new URLRequest(System::GetVariable('pathSession').$request->getCookie()->session.".cok"));
            self::$file->open();
            $this->merge(Json\Decoder::decode(self::$file->getData(), Json\Json::TYPE_ARRAY));
        }

    }

    public function offsetSet($index, $value){

        if(!empty($value)){
            if($value instanceof \TemTudoAqui\Object)
                parent::offsetSet($index, $value->toArray());
            else
                parent::offsetSet($index, $value);
        }elseif($this->offsetExists($index))
            parent::offsetUnset($index);

    }

    public function get($index){
        parent::offsetGet($index);
    }

    public function set($index, $value){
        parent::offsetSet($index, $value);
    }

    public function register(){
        self::$file->data   = Json\Encoder::encode($this->getArrayCopy());
        FileReference::Save(self::$file);
        return true;
    }

}