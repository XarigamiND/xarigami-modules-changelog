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
 * get the list of modules where we're tracking item changes
 *
 * @param $args['editor'] optional editor of the changelog entries
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'changes' => $numchanges);
 */
function changelog_userapi_getmodules($args)
{
    extract($args);

// Security Check
   if (!xarSecurityCheck('ReadChangeLog')) return;

    // Database information
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    // Get items
    if (!empty($editor) && is_numeric($editor)) {
        $query = "SELECT xar_moduleid, xar_itemtype, COUNT(DISTINCT xar_itemid), COUNT(*)
                FROM $changelogtable
                WHERE xar_editor = ?
                GROUP BY xar_moduleid, xar_itemtype";
        $result = $dbconn->Execute($query, array((int)$editor));
    } else {
        $query = "SELECT xar_moduleid, xar_itemtype, COUNT(DISTINCT xar_itemid), COUNT(*)
                FROM $changelogtable
                GROUP BY xar_moduleid, xar_itemtype";
        $result = $dbconn->Execute($query);
    }

    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numchanges) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'changes' => $numchanges);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
