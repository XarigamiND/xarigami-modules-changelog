<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @copyright (C) 2007-2012 2skies.com
 * @subpackage Xarigami Changelog
 * @link http://xarigami.com/project/
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @author mikespub
 * @return array containing the menulinks for the main menu items.
 */
function changelog_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminChangeLog',0)) {
        $menulinks[] = Array('url'   => xarModURL('changelog','admin','view'),
                              'title' => xarML('View changelog entries per module'),
                              'label' => xarML('View Changes'),
                              'active'=> array('view'));

        $menulinks[] = Array('url'   => xarModURL('changelog','admin','modifyconfig'),
                              'title' => xarML('Modify the changelog configuration'),
                              'label' => xarML('Modify Config'),
                              'active' => array('modifyconfig'));
    }

    return $menulinks;
}
?>
