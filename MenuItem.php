<?php

namespace dokuwiki\plugin\userpage;

use dokuwiki\Menu\Item\AbstractItem;

/** @inheritdoc */
class MenuItem extends AbstractItem
{
    protected $type = 'show';

    /** @inheritdoc */
    public function __construct()
    {
        parent::__construct();

        $actionComponent = plugin_load('action', 'userpage');
        $linkData = $actionComponent->getLinkData();

        $this->id = $linkData['goto'];
        $this->label = $linkData['text'];
        $this->svg = __DIR__ . '/icon.svg';
    }
}
