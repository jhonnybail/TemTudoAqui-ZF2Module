<?php

namespace TemTudoAqui\Common;

use TemTudoAqui\Object,
	TemTudoAqui\Exception,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\Number;
	
use Doctrine\ORM\EntityManager;

/**
 *  Classe para persistência de dados.
 *
 * @package    TemTudoAqui
 * @copyright  Copyright (c) 2012 Tem Tudo Aqui. (http://www.temtudoaqui.com)
 * @license    http://license.temtudoaqui.com
 */
class Dao {
	
	/**
	 * Classe de conexão ao banco de dados.
	 * @var mixed
     */
    protected 			$con;
	
	/**
	 * Classe de conexão DBAL ao banco de dados.
	 * @var mixed
     */
    protected 			$dbal;
    
	/**
     * Constructor
     *
     * @return void
     */
	public function __construct(EntityManager $em){
		$this->con	= $em;
		$this->dbal = $em->getConnection();
	}
	
	/**
     * Inicia uma transação.
     *
     * @return 	void
     */
	public function beginTransaction(){
		$this->con->getConnection()->beginTransaction();
	}
	
	/**
     * Finaliza a transação.
     *
     * @return 	void
     */
	public function commit(){
		$this->con->getConnection()->commit();
	}
	
	/**
     * Desfaz a transação.
     *
     * @return 	void
     */
	public function rollback(){
		$this->con->getConnection()->rollback();
	}
	
	/**
     * Persiste os dados de um objeto.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @return 	void
     */
	public function save(Object &$obj){
		if($obj->id instanceof Number){
			if($obj->id->getValue() > 0)
				$this->con->merge($obj);
			else
				$this->con->persist($obj);
		}else{
			if($obj->id > 0)
				$this->con->merge($obj);
			else
                $this->con->persist($obj);
		}

		$this->flush();
	}
	
	/**
     * Remove os dados de um objeto no Banco de dados.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @return 	void
     */
	public function delete(Object $obj){
		try{
			$this->con->remove($obj);
		}catch(\Exception $e){
			//$q = $this->con->createQuery("DELETE FROM ".$obj->GetReflection()->getName()." u where u.id = '".$obj->id."'");
			//$q->execute();
			echo $e->getMessage();
		}
		$this->flush();
	}
	
	/**
     * Procura o objeto de acordo com o Id.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @return 	\TemTudoAqui\Object
     */
	public function findById(Object $obj){

		$metaData = $this->con->getMetadataFactory()->getMetadataFor($obj->GetReflection()->getName());
		$ids = $metaData->getIdentifier();
		$query = new ArrayObject;
		foreach($ids as $field){
			
			try{
				$am = $metaData->getAssociationMapping($field);
				$columnName = $am['joinColumns'][0]['name'];
			}catch(\Exception $e){
				if(isset($metaData->fieldMappings[$field]))
					$columnName = $metaData->fieldMappings[$field]['columnName'];
			}
			if(!empty($columnName)){
				if($obj->$field instanceof \TemTudoAqui\Object)
					$query->offsetSet($field, (int)((string)$obj->$field->id));
				elseif($obj->$field instanceof \DateTime){
					$query->offsetSet($field, $obj->$field->format("Y-m-d H:i:s"));
				}else
					$query->offsetSet($field, (string) $obj->$field);
			}
		}

		if($query->count() < 1){
			$rs = $this->con->find((string) $obj->GetReflection()->getName(), $obj->id);
			$result = $rs;
		}else{
			$repository = $this->con->getRepository($obj->GetReflection()->getName());
            $rs = $repository->findBy((array) $query);
			if(!empty($rs))
                $result = $rs[0];
		}
		if(!$rs){
			throw new Exception(26);
		}
		
		return $result;
		
	}
	
	/**
     * Procura o objeto de acordo com os atributos.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @param	array|null			$order
     * @param	integer|null		$limit
     * @param	integer|null		$offset
     * @return 	array
     */
	public function find(Object $obj, $order = array(), $limit = null, $offset = null){

		$repository = $this->con
		    ->getRepository($obj->GetReflection()->getName());

		$query = $repository->createQueryBuilder('p');
		
		$metaData = $this->con->getMetadataFactory()->getMetadataFor($obj->GetReflection()->getName());
		$maps = $metaData->getAssociationMappings();
		$attrs = $metaData->getFieldNames();

		foreach($attrs as $field){
			if($obj->$field != null && $obj->$field != ''){
				if($metaData->getTypeOfField($field) == 'integer'){
					if($obj->$field instanceof Number)
						$query->andWhere('p.'.$field.' = :'.$field)
			    				->setParameter($field, $obj->$field->getValue());
					else
						$query->andWhere('p.'.$field.' = :'.$field)
			    				->setParameter($field, $obj->$field);		
				}elseif($metaData->getTypeOfField($field) == 'float'){
					if($obj->$field instanceof Number)
						if((float)((string)$obj->$field->getValue()) > 0)
							$query->andWhere('p.'.$field.' = :'.$field)
									->setParameter($field, ((float)((string)$obj->$field->getValue())));
					else
						if((float)((string)$obj->$field) > 0)
							$query->andWhere('p.'.$field.' = :'.$field)
									->setParameter($field, ((float)((string)$obj->$field)));
				}elseif($metaData->getTypeOfField($field) == 'string'){
                    if(strpos((string) $obj->$field, "(equals)") !== false)
                        $query->andWhere('p.'.$field.' = :'.$field)
                            ->setParameter($field, addslashes((string) str_replace("(equals)", "", $obj->$field)));
                    else
                        $query->andWhere('p.'.$field.' LIKE :'.$field)
			    			    ->setParameter($field, '%'.addslashes((string) $obj->$field).'%');
				}elseif($obj->$field instanceof \TemTudoAqui\Utils\Data\DateTime){
					$query->andWhere('p.'.$field.' LIKE :'.$field)
			    			->setParameter($field, $obj->$field->format("Y-m-d H:i:s"));
				}elseif($metaData->getTypeOfField($field) == 'boolean'){
					$bool = $obj->$field;
					if((string) $obj->$field == (string)'0')
						$obj->$field = 0;
					elseif((string) $obj->$field == (string)'1')
						$obj->$field = 1;
					$query->andWhere('p.'.$field.' LIKE :'.$field)
			    			->setParameter($field, $obj->$field);
					$obj->$field = $bool;
				}
			}
		}
		
		
		foreach($maps as $fieldName => $field){
			$am = $metaData->getAssociationMapping($fieldName);
			if(($am['type'] == 2 || $am['type'] == 1) && $obj->$fieldName != null && !($obj->$fieldName instanceof String) && !($obj->$fieldName instanceof Number)){
				$id = null;
                if($obj->$fieldName->id instanceof Number || is_int($obj->$fieldName->id)){
                    if($obj->$fieldName->id instanceof Number)
						$id = (int)$obj->$fieldName->id->getValue();
					else
						$id = $obj->$fieldName->id;
					if(($id != null && $id != '' && ((int)((string)$id)) > -1) || ((string) $id) == "0"){
						$query->andWhere('p.'.$fieldName.' = :'.$fieldName)
								->setParameter($fieldName, $id);
					}elseif($id == -1){
						$query->andWhere('p.'.$fieldName.' IS NULL');
					}
                }elseif(is_object($obj->$fieldName)){
					if(($obj->$fieldName->id != null && $obj->$fieldName->id != '' && ((int)((string)$obj->$fieldName->id)) > -1) || ((string) $obj->$fieldName->id) == "0"){
						$query->andWhere('p.'.$fieldName.' = :'.$fieldName)
								->setParameter($fieldName, $obj->$fieldName->id);
					}elseif($obj->$fieldName->id == -1){
						$query->andWhere('p.'.$fieldName.' IS NULL');
					}
                }else{

                    if($obj->$fieldName < 0){
                        $query->andWhere('p.'.$fieldName.' IS NULL');
                    }
                }
			}elseif(($obj->$fieldName instanceof String) || ($obj->$fieldName instanceof Number) || is_string($obj->$fieldName) || is_numeric($obj->$fieldName)){
                if((int)((string)$obj->$fieldName) < 0){
                    $query->andWhere('p.'.$fieldName.' IS NULL');
                }
            }

		}

		if(is_array($order))
			if(current($order))
				$query = $query->orderBy("p.".key($order), current($order));
		
		if($limit)
			$query = $query->setMaxResults($limit);

		if($offset)
			$query = $query->setFirstResult($offset);

        $rs = $query->getQuery()->getResult();
		//echo var_dump($rs);exit;
		return new ArrayObject($rs);
		
	}
	
	/**
     * Procura todos os registros.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @param	array|null			$order
     * @param	integer|null		$limit
     * @param	integer|null		$offset
     * @return 	array
     */
	public function findAll(Object $obj, $order = array(), $limit = null, $offset = null){
		return new ArrayObject((array)$this->con->getRepository($obj->GetReflection()->getName())->findBy(array(), $order, $limit, $offset));
	}
	
	/**
     * Retorna o total de registros.
     *
     * @param	\TemTudoAqui\Object	$obj
     * @return 	integer
     */
	public function getTotalRows(Object $obj){
		
		$repository = $this->con
		    ->getRepository($obj->GetReflection()->getName());
		
		$query = $repository->createQueryBuilder('p');
		
		$metaData = $this->con->getMetadataFactory()->getMetadataFor($obj->GetReflection()->getName());
		$maps = $metaData->getAssociationMappings();
		$attrs = $metaData->getFieldNames();
		
		foreach($attrs as $field){
			if($obj->$field != null && $obj->$field != ''){
				if($metaData->getTypeOfField($field) == 'integer'){
					if($obj->$field instanceof Number)
						$query->andWhere('p.'.$field.' = :'.$field)
			    				->setParameter($field, $obj->$field->getValue());
					else
						$query->andWhere('p.'.$field.' = :'.$field)
			    				->setParameter($field, $obj->$field);		
				}elseif($metaData->getTypeOfField($field) == 'float'){
					if($obj->$field instanceof Number)
						if((float)((string)$obj->$field->getValue()) > 0)
							$query->andWhere('p.'.$field.' = :'.$field)
									->setParameter($field, ((float)((string)$obj->$field->getValue())));
					else
						if((float)((string)$obj->$field) > 0)
							$query->andWhere('p.'.$field.' = :'.$field)
									->setParameter($field, ((float)((string)$obj->$field)));
				}elseif($metaData->getTypeOfField($field) == 'string'){
                    if(strpos((string) $obj->$field, "(equals)") !== false)
                        $query->andWhere('p.'.$field.' = :'.$field)
                            ->setParameter($field, addslashes((string) str_replace("(equals)", "", $obj->$field)));
                    else
                        $query->andWhere('p.'.$field.' LIKE :'.$field)
                            ->setParameter($field, '%'.addslashes((string) $obj->$field).'%');
				}elseif($obj->$field instanceof \TemTudoAqui\Utils\Data\DateTime){
					$query->andWhere('p.'.$field.' LIKE :'.$field)
			    			->setParameter($field, $obj->$field->format("Y-m-d H:i:s"));
				}elseif($metaData->getTypeOfField($field) == 'boolean'){
					$bool = $obj->$field;
					if($obj->$field == (string)'false')
						$obj->$field = 0;
					elseif($obj->$field == (string)'true')
						$obj->$field = true;
					$query->andWhere('p.'.$field.' LIKE :'.$field)
			    			->setParameter($field, $obj->$field);
					$obj->$field = $bool;
				}
			}
		}
		
		foreach($maps as $fieldName => $field){
			$am = $metaData->getAssociationMapping($fieldName);
			if(($am['type'] == 2 || $am['type'] == 1) && $obj->$fieldName != null && !($obj->$fieldName instanceof String) && !($obj->$fieldName instanceof Number)){
				$id = null;
				if($obj->$fieldName->id instanceof Number || is_int($obj->$fieldName->id))
					if($obj->$fieldName->id instanceof Number)
						$id = (int)$obj->$fieldName->id->getValue();
					else
						$id = $obj->$fieldName->id;
					if(($id != null && $id != '' && ((int)((string)$id)) > -1) || ((string) $id) == "0"){
						$query->andWhere('p.'.$fieldName.' = :'.$fieldName)
								->setParameter($fieldName, $id);
					}elseif($id == -1){
						$query->andWhere('p.'.$fieldName.' IS NULL');
					}
				else
					if(($obj->$fieldName->id != null && $obj->$fieldName->id != '' && ((int)((string)$obj->$fieldName->id)) > -1) || ((string) $obj->$fieldName->id) == "0"){
						$query->andWhere('p.'.$fieldName.' = :'.$fieldName)
								->setParameter($fieldName, $obj->$fieldName->id);
					}elseif($obj->$fieldName->id == -1){
						$query->andWhere('p.'.$fieldName.' IS NULL');
					}
			}elseif(($obj->$fieldName instanceof String) || ($obj->$fieldName instanceof Number) || is_string($obj->$fieldName) || is_numeric($obj->$fieldName)){
                if((int)((string)$obj->$fieldName) < 0){
                    $query->andWhere('p.'.$fieldName.' IS NULL');
                }
            }

		}
		
		$query = $query->select('COUNT(p)');
		//echo var_dump($query->getQuery());exit;
		$rs = $query->getQuery()->getResult();
		return (int) $rs[0][1];
				
	}
	
	public function flush(){
		$this->con->flush();
	}
    
}