<?
namespace Concrete\Core\Page\Search\Result;

use \Concrete\Core\Search\Result\Item as SearchResultItem;
use \Concrete\Core\Search\Result\ItemColumn as SearchResultItemColumn;
use \Concrete\Core\Search\Result\Result as SearchResult;
use \Concrete\Core\Search\Column\Set;
use Permissions;

class Item extends SearchResultItem {

	public $cID;

	public function __construct(SearchResult $result, Set $columns, $item) {
		$list = $result->getItemListObject();
		if ($list->isFulltextSearch()) {
			$this->columns[] = new SearchResultItemColumn(t('Score'), $item->getPageIndexScore());
		}
		parent::__construct($result, $columns, $item);
		$this->populateDetails($item);
	}

	protected function populateDetails($item) {
		$this->cID = $item->getCollectionID();
		$cp = new Permissions($item);        
		$this->canEditPageProperties = $cp->canEditPageProperties();
		$this->canEditPageSpeedSettings = $cp->anEditPageSpeedSettings();
		$this->canEditPagePermissions = $cp->canEditPagePermissions();
		$this->canEditPageDesign = $cp->canEditPageDesign();
		$this->canViewPageVersions = $cp->canViewPageVersions();
		$this->canDeletePage = $cp->canDeletePage();
		$this->cvName = $item->getCollectionName();
	}


}
