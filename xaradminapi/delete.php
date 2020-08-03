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
 * delete changelog entries
 *
 * @param $args['modid'] int module id, or
 * @param $args['modname'] name of the calling module
 * @param $args['itemtype'] optional item type for the item
 * @param $args['itemid'] int item id
 * @param $args['editor'] optional editor of the changelog entries
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_delete($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminChangeLog')) return;

    // Database information
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    $query = "DELETE FROM $changelogtable ";
    $bindvars = array();
    if (!empty($modid)) {
        if (!is_numeric($modid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'module id', 'admin', 'delete', 'Hitcount');
             throw new BadParameterException(null,$msg);
        }
        if (empty($itemtype) || !is_numeric($itemtype)) {
            $itemtype = 0;
        }
        $query .= " WHERE xar_moduleid = ?
                      AND xar_itemtype = ?";

        $bindvars[] = (int) $modid;
        $bindvars[] = (int) $itemtype;

        if (!empty($itemid) && is_numeric($itemid)) {
            $query .= " AND xar_itemid = ?";
            $bindvars[] = (int) $itemid;
        }

        if (!empty($editor) && is_numeric($editor)) {
            $query .= " AND xar_editor = ?";
            $bindvars[] = (int) $editor;
        }

    } elseif (!empty($editor) && is_numeric($editor)) {
        $query .= " WHERE xar_editor = ?";
        $bindvars[] = (int) $editor;
    }

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    return true;
}

?>