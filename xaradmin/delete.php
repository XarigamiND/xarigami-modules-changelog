<?php
/**
 * Change Log Module version information
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
 * Delete changelog entries of module items
 */
function changelog_admin_delete()
{
    // Security Check
    if(!xarSecurityCheck('AdminChangeLog')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('editor',   'isset', $editor,    NULL, XARVAR_DONT_SET)) {return;}

    $data = array();
    $data['menulinks'] = xarModAPIFunc('changelog','admin','getmenulinks');
    // Check for confirmation.

    if (empty($confirm)) {
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;
        $data['editor'] = $editor;

        $what = '';
        if (!empty($modid)) {
            $modinfo = xarModGetInfo($modid);
            if (empty($itemtype)) {
                $data['modname'] = ucwords($modinfo['displayname']);
            } else {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                } else {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                }
            }
        }
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

// comment out the following line if you want this
    return xarML('This feature is currently disabled for security reasons');

    if (!xarModAPIFunc('changelog','admin','delete',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'editor' => $editor,
                             'confirm' => $confirm))) {
        $msg = xarML('There was an error when trying to delete the log entry.');
        xarTplSetMessage($msg,'error');
        return;
    } else {
        $msg = xarML('Changelog entry was successfully deleted.');
        xarTplSetMessage($msg,'status');
    }
    xarResponseRedirect(xarModURL('changelog', 'admin', 'view'));
    return true;
}

?>
