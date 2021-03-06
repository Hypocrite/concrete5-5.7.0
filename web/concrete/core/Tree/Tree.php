<?
namespace Concrete\Core\Tree;
use \Concrete\Core\Foundation\Object;
use Loader;
use Core;
abstract class Tree extends Object {

	protected $treeNodeSelectedID = 0;

	abstract protected function loadDetails();
	abstract protected function deleteDetails();
	abstract public function getTreeDisplayName();

	public function setSelectedTreeNodeID($nodeID) {
		$this->treeNodeSelectedID = $nodeID;
	}

	public function getSelectedTreeNodeID() {
		return $this->treeNodeSelectedID;
	}

	public function getTreeTypeID() {
		return $this->treeTypeID;
	}
	public function getTreeTypeObject() {
		return TreeType::getByID($this->treeTypeID);
	}

	public function getTreeTypeHandle() {
		$type = $this->getTreeTypeObject();
		if (is_object($type)){
			return $type->getTreeTypeHandle();
		}
	}

	public function getTreeID() {return $this->treeID;}
	public function getRootTreeNodeObject() {return \Concrete\Core\Tree\Node\Node::getByID($this->rootTreeNodeID);}

	public function setRequest($data) {
		$this->requestData = $data;
	}

	public function delete() {
		$db = Loader::db();
		// delete top level node
		$node = $this->getRootTreeNodeObject();
		$node->delete();
		$this->deleteDetails();
		$db->Execute('delete from Trees where treeID = ?', array($this->treeID));
	}

	public function duplicate() {
		$root = $this->getRootTreeNodeObject();
		$newRoot = $root->duplicate();
		$type = $this->getTreeTypeObject();
		$tree = $type->addTree($newRoot);
		$db = Loader::db();
		$nodes = $newRoot->getAllChildNodeIDs();
		foreach($nodes as $nodeID) {
			$db->Execute('update TreeNodes set treeID = ? where treeNodeID = ?', array($tree->getTreeID(), $nodeID));
		}
		return $tree;
	}

	public function getJSON() {
		$root = $this->getRootTreeNodeObject();
		$root->populateDirectChildrenOnly();
		if(is_array($this->getSelectedTreeNodeID())) {
			foreach($this->getSelectedTreeNodeID() as $magicNodes) {
				$root->selectChildrenNodesByID($magicNodes);
			}
		}
		if ($this->getSelectedTreeNodeID() > 0) {
			$root->selectChildrenNodesByID($this->getSelectedTreeNodeID());
		}
		return $root->getTreeNodeJSON();
	}

	protected static function add(\Concrete\Core\Tree\Node\Node $rootNode) {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$treeTypeHandle = Loader::helper('text')->uncamelcase(strrchr(get_called_class(), '\\'));
		$type = TreeType::getByHandle($treeTypeHandle);
		$db->Execute('insert into Trees (treeDateAdded, rootTreeNodeID, treeTypeID) values (?, ?, ?)', array(
			$date, $rootNode->getTreeNodeID(), $type->getTreeTypeID()
		));
		$treeID = $db->Insert_ID();
		$rootNode->setTreeNodeTreeID($treeID);
		return $treeID;
	}


	final public static function getByID($treeID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from Trees where treeID = ?', array($treeID));
		if (is_array($row) && $row['treeID']) {
			$tt = TreeType::getByID($row['treeTypeID']);
			$class = $tt->getTreeTypeClass();
			$tree = Core::make($class);
			$tree->setPropertiesFromArray($row);
			$tree->loadDetails();
			return $tree;
		}
	}
	
}