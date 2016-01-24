<?php
namespace JYLibrary\Model;
use Zend\Db\TableGateway\TableGateway,
	Zend\Db\Sql\Where;
class SessionTable
{
	protected $table ='session';
	protected $tableName ='session';

	protected $tableGateway;
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet(new Session);
		$this->initialize();
	}
	
	public function getSession($id)
	{
		$id = (int) $id;
		$rowset = $this->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
}