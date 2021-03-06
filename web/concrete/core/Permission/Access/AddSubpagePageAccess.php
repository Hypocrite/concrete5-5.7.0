<?
namespace Concrete\Core\Permission\Access;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use Loader;
use \Concrete\Core\Permission\Key\PageKey as PagePermissionKey;

class AddSubpagePageAccess extends PageAccess {

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from PagePermissionPageTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission'], $row['externalLink']);
			$db->Execute('insert into PagePermissionPageTypeAccessList (peID, paID, permission, externalLink) values (?, ?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from PagePermissionPageTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['ptID']);
			$db->Execute('insert into PagePermissionPageTypeAccessListCustom  (peID, paID, ptID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function removeListItem(PermissionAccessEntity $pe) {
		parent::removeListItem($pe);
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPageTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));	
		$db->Execute('delete from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $this->getPermissionAccessID()));	
	}

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from PagePermissionPageTypeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from PagePermissionPageTypeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['pageTypesIncluded'])) { 
			foreach($args['pageTypesIncluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksIncluded'][$peID])) {
					$ext = $args['allowExternalLinksIncluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}
		
		if (is_array($args['pageTypesExcluded'])) { 
			foreach($args['pageTypesExcluded'] as $peID => $permission) {
				$ext = 0;
				if (!empty($args['allowExternalLinksExcluded'][$peID])) {
					$ext = $args['allowExternalLinksExcluded'][$peID];
				}
				$v = array($this->getPermissionAccessID(), $peID, $permission, $ext);
				$db->Execute('insert into PagePermissionPageTypeAccessList (paID, peID, permission, externalLink) values (?, ?, ?, ?)', $v);
			}
		}

		if (is_array($args['ptIDInclude'])) { 
			foreach($args['ptIDInclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['ptIDExclude'])) { 
			foreach($args['ptIDExclude'] as $peID => $ptIDs) {
				foreach($ptIDs as $ptID) { 
					$v = array($this->getPermissionAccessID(), $peID, $ptID);
					$db->Execute('insert into PagePermissionPageTypeAccessListCustom (paID, peID, ptID) values (?, ?, ?)', $v);
				}
			}
		}

	}


	public function getAccessListItems($accessType = PagePermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		$list = PermissionDuration::filterByActive($list);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			$prow = $db->GetRow('select permission, externalLink from PagePermissionPageTypeAccessList where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
			if (is_array($prow) && $prow['permission']) { 
				$l->setPageTypesAllowedPermission($prow['permission']);
				$l->setAllowExternalLinks($prow['externalLink']);
				$permission = $prow['permission'];
			} else if ($l->getAccessType() == PagePermissionKey::ACCESS_TYPE_INCLUDE) {
				$l->setPageTypesAllowedPermission('A');
				$l->setAllowExternalLinks(1);
			} else {
				$l->setPageTypesAllowedPermission('N');
				$l->setAllowExternalLinks(0);
			}
			if ($permission == 'C') { 
				$ptIDs = $db->GetCol('select ptID from PagePermissionPageTypeAccessListCustom where peID = ? and paID = ?', array($pe->getAccessEntityID(), $l->getPermissionAccessID()));
				$l->setPageTypesAllowedArray($ptIDs);
			}
		}
		return $list;
	}
}