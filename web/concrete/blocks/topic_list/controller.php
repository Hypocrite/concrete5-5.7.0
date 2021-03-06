<?php

namespace Concrete\Block\TopicList;
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Core;
use Loader;

class Controller extends BlockController
{

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 100;
    protected $btTable = 'btTopicList';

    public function getBlockTypeDescription()
    {
        return t("Displays a list of your site's topics, allowing you to click on them to filter a page list.");
    }

    public function getBlockTypeName()
    {
        return t("Topic List");
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->requireAsset('core/topics');
        $tt = new TopicTree();
        $defaultTree = $tt->getDefault();
        $tree = $tt->getByID(Loader::helper('security')->sanitizeInt($this->topicTreeID));
        if (!$tree) {
            $tree = $defaultTree;
        }
        $trees = $tt->getList();
        $this->set('tree', $tree);
        $this->set('trees', $trees);
    }

    public function view()
    {
        $tt = new TopicTree();
        $tree = $tt->getByID(Loader::helper('security')->sanitizeInt($this->topicTreeID));
        $this->set('tree', $tree);
    }
}