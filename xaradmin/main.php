<?php
/**
 * Change Log Module
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
 * the main administration function
 * Redirect to modifyconfig
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return bool true on success of redirect or void on failure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function changelog_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    xarResponseRedirect(xarModURL('changelog', 'admin', 'view'));
    // success
    return array(); //true;
}

?>
