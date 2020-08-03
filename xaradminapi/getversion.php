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
 * get a particular entry for a module item
 *
 * @param $args['modid'] module id
 * @param $args['itemtype'] item type
 * @param $args['itemid'] item id
 * @param $args['logid'] log id
 * @return array of changes
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_getversion($args)
{
    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'admin', 'getversion', 'changelog');
        throw new BadParameterException(null,$msg);
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'getversion', 'changelog');
         throw new BadParameterException(null,$msg);
    }
    if (!isset($logid) || !is_numeric($logid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'log id', 'admin', 'getversion', 'changelog');
        throw new BadParameterException(null,$msg);
    }

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $changelogtable = $xartable['changelog'];
    $rolestable = $xartable['roles'];

    // Get changes for this module item
    $query = "SELECT $changelogtable.xar_logid,
                     $changelogtable.xar_editor,
                     $changelogtable.xar_hostname,
                     $changelogtable.xar_date,
                     $changelogtable.xar_status,
                     $changelogtable.xar_remark,
                     $changelogtable.xar_content,
                     $rolestable.xar_name
                FROM $changelogtable
           LEFT JOIN $rolestable
                  ON $changelogtable.xar_editor = $rolestable.xar_uid
               WHERE $changelogtable.xar_moduleid = ?
                 AND $changelogtable.xar_itemtype = ?
                 AND $changelogtable.xar_itemid = ?
                 AND $changelogtable.xar_logid = ?";

    $bindvars = array((int) $modid, (int) $itemtype, (int) $itemid, (int) $logid);

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    $version = array();
    if ($result->EOF) {
        return $version;
    }
    list($version['logid'],
         $version['editor'],
         $version['hostname'],
         $version['date'],
         $version['status'],
         $version['remark'],
         $version['content'],
         $version['editorname']) = $result->fields;
    $result->Close();

    return $version;
}
?>
