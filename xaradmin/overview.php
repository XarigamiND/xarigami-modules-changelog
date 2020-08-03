<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @copyright (C) 2007-2011 2skies.com
 * @subpackage Xarigami Changelog
 * @link http://xarigami.com/project/
*/
/**
 * Overview function that displays the standard Overview page
 *
 */
function changelog_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminChangeLog',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */
    $data['menulinks'] = xarModAPIFunc('changelog','admin','getmenulinks');
    return xarTplModule('changelog', 'admin', 'main', $data,'main');
}

?>
