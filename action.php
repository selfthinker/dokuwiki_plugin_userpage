<?php
/**
 * Userpage Plugin: places userpage link in usertools
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Blake Martin
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Anika Henke <anika@selfthinker.org>
 */

class action_plugin_userpage extends DokuWiki_Action_Plugin
{
    /** @inheritdoc */
    function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TEMPLATE_USERTOOLS_DISPLAY', 'BEFORE', $this, 'addLegacyMenuLink');
        $controller->register_hook('MENU_ITEMS_ASSEMBLY', 'AFTER', $this, 'addMenuLink');
    }

    /**
     * Get all relevant data to build link to the user page
     *
     * @return array text, url and attributes for the link
     */
    function getLinkData() {
        global $INFO;
        global $conf;
        global $ACT;

        $userPage = $this->getConf('userPage');
        $userPage = str_replace('@USER@', $_SERVER['REMOTE_USER'], $userPage);
        if (substr($userPage, -strlen(':')) === ':') {
            $userPage = $userPage.$conf['start'];
        }

        $title = $this->getLang('userpage');
        // highlight when on user page (only works with old menu)
        $activeClass = ($userPage == $INFO['id'] && act_clean($ACT) == 'show') ? ' active' : '';

        $attr = array();
        $attr['href'] = wl($userPage);
        $attr['class'] = 'userpage'.$activeClass;

        return array(
            'goto' => $userPage,
            'text' => $title,
            'attr' => $attr,
        );
    }

    /**
     * Add user page to the old menu (before Greebo)
     *
     * @param Doku_Event $event
     * @return bool
     */
    public function addLegacyMenuLink(Doku_Event $event)
    {
        if (empty($_SERVER['REMOTE_USER'])) return false;
        if (!$event->data['view'] == 'main') return false;

        $data = $this->getLinkData();
        $data['attr']['class'] .= ' action';

        $link = '<li><a '.buildAttributes($data['attr']).'><span>'.$data['text'].'</span></a></li>';

        // insert at second position
        $event->data['items'] = array_slice($event->data['items'], 0, 1, true) +
            array('userpage' => $link) +
            array_slice($event->data['items'], 1, null, true);

        return true;
    }

    /**
     * Add user page to the menu system
     *
     * @param Doku_Event $event
     * @return bool
     */
    public function addMenuLink(Doku_Event $event)
    {
        if (empty($_SERVER['REMOTE_USER'])) return false;
        if ($event->data['view'] !== 'user') return false;

        array_splice($event->data['items'], 1, 0, [new \dokuwiki\plugin\userpage\MenuItem()]);

        return true;
    }
}
