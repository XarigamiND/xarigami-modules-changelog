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
 * Update configuration
 */
function changelog_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('changelog', 'isset', $changelog, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('includedd', 'isset', $includedd, NULL, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    if (isset($changelog) && is_array($changelog)) {
        foreach ($changelog as $modname => $value) {
            if ($modname == 'default') {
                xarModSetVar('changelog', 'default', $value);
            } else {
                xarModSetVar('changelog', $modname, $value);
            }
        }
    }
    if (isset($includedd) && is_array($includedd)) {
        $withdd = join(';',array_keys($includedd));
        // Set the sort order of the changelog hooks to 999 to make sure they're called last
        if (defined('XARCORE_GENERATION') && XARCORE_GENERATION == 2) {

// FIXME: change hook order in 2.x core

        } else {
            $dbconn = xarDBGetConn();
            $xartable = xarDBGetTables();
            $query = "UPDATE $xartable[hooks]
                         SET xar_order = 999
                       WHERE xar_tmodule = 'changelog'";
            $result = $dbconn->Execute($query);
            if (!$result) return;
        }
    } else {
        $withdd = '';
    }
    xarModSetVar('changelog','withdd',$withdd);

    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('difftype', 'checkbox', $difftype, false, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('changelog', 'numstats', $numstats);
    xarModSetVar('changelog', 'difftype', $difftype);
    xarModSetVar('changelog', 'showtitle', $showtitle);
    $msg = xarML('Changelog configuration settings successfully updated.');
    xarTplSetMessage($msg,'status');
    xarResponseRedirect(xarModURL('changelog', 'admin', 'modifyconfig'));

    return true;
}

?>
