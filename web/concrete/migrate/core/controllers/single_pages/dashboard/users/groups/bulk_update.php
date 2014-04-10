<? 

class Concrete5_Controller_Dashboard_Users_Groups_BulkUpdate extends DashboardPageController {

	public function confirm() {
		$this->move();

		if (!$this->error->has()) {
			$selectedGroups = $this->get('selectedGroups');
			$gParentNode = $this->get('gParentNode');

			foreach($selectedGroups as $g) {
				$node = GroupTreeNode::getTreeNodeByGroupID($g->getGroupID());
				if (is_object($node)) {
					$node->move($gParentNode);
				}
			}
		}

		$this->redirect('/dashboard/users/groups', 'bulk_update_complete');
	}

	public function move() {
		$this->search();
		$gParentNodeID = Loader::helper('security')->sanitizeInt($_REQUEST['gParentNodeID']);
		if ($gParentNodeID) {
			$node = TreeNode::getByID($gParentNodeID);
		}
		if (!($node instanceof GroupTreeNode)) {
			$this->error->add(t("Invalid target parent group."));
		}
		$selectedGroups = array();
		if (is_array($_POST['gID'])) {
			foreach($_POST['gID'] as $gID) {
				$group = Group::getByID($gID);
				if (is_object($group)) {
					$selectedGroups[] = $group;
				}
			}
		}

		if (count($selectedGroups) == 0) {
			$this->error->add(t("You must select at least one group to move"));
		}

		if (!$this->error->has()) {
			$gParent = $node->getTreeNodeGroupObject();
			$this->set('selectedGroups', $selectedGroups);
			$this->set('gParent', $gParent);
			$this->set('gParentNode', $node);
		}

	}
	public function search() {
		$this->requireAsset('core/groups');
		$tree = GroupTree::get();
		$this->set("tree", $tree);
		$gName = Loader::helper('security')->sanitizeString($_REQUEST['gName']);
		if (!$gName) {
			$this->error->add(t('You must specify a search string.'));
		}
		if (!$this->error->has()) {
			$gl = new GroupList();
			$gl->filterByKeywords($gName);
			$gl->setItemsPerPage(-1);
			foreach($gl->get() as $g) {
				$g = Group::getByID($g['gID']);
				if (is_object($g)) {
					$groups[] = $g;
				}
			}
			$this->set('groups', $groups);
		}
	}

}