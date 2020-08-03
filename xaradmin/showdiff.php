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
 * show the differences between 2 versions of a module item
 */
function changelog_admin_showdiff($args)
{
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
// Note : this is an array or a string here
    if (!xarVarFetch('logids',    'isset', $logids,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    // get all changes
    $changes = xarModAPIFunc('changelog','admin','getchanges',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    if (empty($changes) || !is_array($changes)) return;

    if (empty($logids)) {
        $logidlist = array();
    } elseif (is_string($logids)) {
        $logidlist = explode('-',$logids);
    } else {
        $logidlist = $logids;
    }
    sort($logidlist,SORT_NUMERIC);
    if (count($logidlist) < 2) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'number of versions', 'admin', 'showdiff', 'changelog');
         throw new BadParameterException(null,$msg);
    } elseif (!isset($changes[$logidlist[0]]) || !isset($changes[$logidlist[1]])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'version ids', 'admin', 'showdiff', 'changelog');
         throw new BadParameterException(null,$msg);
    }

    $data = array();
    $data['menulinks'] = xarModAPIFunc('changelog','admin','getmenulinks');
    $oldid = $logidlist[0];
    $newid = $logidlist[1];

    $numchanges = count($changes);
    $data['numversions'] = $numchanges;
    $nextid = 0;
    $previd = 0;
    $lastid = 0;
    $version = array();
    foreach (array_keys($changes) as $id) {
        $version[$id] = $numchanges;
        $numchanges--;
        if ($id == $newid) {
            $nextid = $lastid;
        } elseif ($lastid == $oldid) {
            $previd = $id;
        }
        $lastid = $id;
    }

    $data['oldversion'] = $version[$oldid];
    $data['newversion'] = $version[$newid];
    if (!empty($nextid)) {
        $data['nextdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $newid.'-'.$nextid));
    }
    if (!empty($previd)) {
        $data['prevdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $previd.'-'.$oldid));
    }

    $data['changes'] = array();
    $data['changes'][$newid] = $changes[$newid];
    $data['changes'][$oldid] = $changes[$oldid];

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }

    foreach (array_keys($data['changes']) as $logid) {
        $data['changes'][$logid]['profile'] = xarModURL('roles','user','display',
                                                        array('uid' => $data['changes'][$logid]['editor']));
        if (!$data['showhost']) {
            $data['changes'][$logid]['hostname'] = '';
            $data['changes'][$logid]['link'] = '';
        } else {
            $data['changes'][$logid]['link'] = xarModURL('changelog','admin','showversion',
                                                         array('modid' => $modid,
                                                               'itemtype' => $itemtype,
                                                               'itemid' => $itemid,
                                                               'logid' => $logid));
        }
        if (!empty($data['changes'][$logid]['remark'])) {
            $data['changes'][$logid]['remark'] = xarVarPrepForDisplay($data['changes'][$logid]['remark']);
        }
        // 2template $data['changes'][$logid]['date'] = xarLocaleFormatDate($data['changes'][$logid]['date']);
        $data['changes'][$logid]['version'] = $version[$logid];
    }

    $data['link'] = xarModURL('changelog','admin','showlog',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $modinfo = xarModGetInfo($modid);
    if (empty($modinfo['name'])) {
        return $data;
    }
    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);
    if (isset($itemlinks[$itemid])) {
        $data['itemlink'] = $itemlinks[$itemid]['url'];
        $data['itemtitle'] = $itemlinks[$itemid]['title'];
        $data['itemlabel'] = $itemlinks[$itemid]['label'];
    }

    if (!empty($itemtype)) {
        $getlist = xarModGetVar('changelog',$modinfo['name'].'.'.$itemtype);
    }
    if (!isset($getlist)) {
        $getlist = xarModGetVar('changelog',$modinfo['name']);
    }
    if (!empty($getlist)) {
        $fieldlist = explode(',',$getlist);
    }

    $original = xarModAPIFunc('changelog','admin','getversion',
                         array('modid' => $modid,
                               'itemtype' => $itemtype,
                               'itemid' => $itemid,
                               'logid' => $oldid));
    if (empty( $original) || !is_array( $original)) return;

    if (!empty( $original['content'])) {
        $fields = unserialize( $original['content']);
         $original['content'] = '';

        ksort($fields);
        foreach ($fields as $field => $value) {
            // skip some common uninteresting fields
            if ($field == 'module' || $field == 'itemtype' || $field == 'itemid' ||
                $field == 'mask' || $field == 'pass' || $field == 'changelog_remark') {
                continue;
            }
            // skip fields we don't want here
            if (!empty($fieldlist) && !in_array($field,$fieldlist)) {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
             $original['fields'][$field] = $value;
        }
    }
    if (!isset( $original['fields'])) {
         $original['fields'] = array();
    }

    $latest = xarModAPIFunc('changelog','admin','getversion',
                         array('modid' => $modid,
                               'itemtype' => $itemtype,
                               'itemid' => $itemid,
                               'logid' => $newid));
    if (empty($latest) || !is_array($latest)) return;

    if (!empty($latest['content'])) {
        $fields = unserialize($latest['content']);
        $new['content'] = '';

        ksort($fields);
        foreach ($fields as $field => $value) {
            // skip some common uninteresting fields
            if ($field == 'module' || $field == 'itemtype' || $field == 'itemid' ||
                $field == 'mask' || $field == 'pass' || $field == 'changelog_remark') {
                continue;
            }
            // skip fields we don't want here
            if (!empty($fieldlist) && !in_array($field,$fieldlist)) {
                continue;
            }
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            $latest['fields'][$field] = $value;
        }
    }
    if (!isset($latest['fields'])) {
        $latest['fields'] = array();
    }

    $fieldlist = array_unique(array_merge(array_keys($original['fields']),array_keys($latest['fields'])));
    ksort($fieldlist);

    $difftype = xarModGetVar('changelog','difftype');
    $data['difftype'] = $difftype;
    if ($difftype == 1) { //color diff

        require 'modules/changelog/xarincludes/class.simplediff.php';

    } else { //traditional diff
         include 'modules/changelog/xarincludes/difflib.php';
    }

    $data['fields'] = array();

    foreach ($fieldlist as $field) {
        $out = '';
        if (!isset($original['fields'][$field])) {
            $original['fields'][$field] = '';
        }
        if (!isset($latest['fields'][$field])) {
            $latest['fields'][$field] = '';
        }


        if ($difftype == 1) {
            $diff = simpleDiff::diff_to_array(false, $original['fields'][$field], $latest['fields'][$field], 1);

            if (is_array($diff)) {
                $prev = key($diff);
                foreach ($diff as $i=>$line)
                {
                     if ($i > $prev + 1)
                    {
                        $out .= '<tr><td colspan="5" class="separator"><hr /></td></tr>';
                    }
                    list($type, $oldinfo, $newinfo) = $line;

                    $class1 = $class2 = '';
                    $t1 = $t2 = '';

                    if ($type == simpleDiff::INS)
                    {
                        $class2 = 'ins';
                        $t2 = '+';
                    }
                    elseif ($type == simpleDiff::DEL)
                    {
                        $class1 = 'del';
                        $t1 = '-';
                    }
                    elseif ($type == simpleDiff::CHANGED)
                    {
                        $class1 = 'del';
                        $class2 = 'ins';
                        $t1 = '-';
                        $t2 = '+';

                        $lineDiff = simpleDiff::wdiff($oldinfo, $newinfo);

                        // Don't show new things in deleted line
                        $oldinf = preg_replace('!\{\+(?:.*)\+\}!U', '', $lineDiff);
                        $oldinf = str_replace('  ', ' ', $oldinfo);
                        $oldinf = str_replace('-] [-', ' ', $oldinfo);
                        $oldinf = preg_replace('!\[-(.*)-\]!U', '<del>\\1</del>', $oldinfo);

                        // Don't show old things in added line
                        $newinfo = preg_replace('!\[-(?:.*)-\]!U', '', $lineDiff);
                        $newinfo = str_replace('  ', ' ', $newinfo);
                        $newinfo = str_replace('+} {+', ' ', $newinfo);
                        $newinfo = preg_replace('!\{\+(.*)\+\}!U', '<ins>\\1</ins>', $newinfo);
                    }

                    $out .= '<tr>';
                    $out .= '<td class="line">'.($i+1).'</td>';
                    $out .= '<td class="leftChange">'.$t1.'</td>';
                    $out .= '<td class="leftText '.$class1.'">'.$oldinfo.'</td>';
                    $out .= '<td class="rightChange">'.$t2.'</td>';
                    $out .= '<td  class="rightText '.$class2.'">'.$newinfo.'</td>';
                    $out .= '</tr>';

                    $prev = $i;
                }

            } else {
                //no diffs
            }
            $data['fields'][$field]['out'] = $out;
        } else {

            foreach ($fieldlist as $field) {
                if (!isset($original['fields'][$field])) {
                    $original['fields'][$field] = '';
                }
                if (!isset($new['fields'][$field])) {
                   $latest['fields'][$field] = '';
                }
                $diff = new Diff( preg_split("/\n/",$original['fields'][$field]), preg_split("/\n/",$latest['fields'][$field]));
                if ($diff->isEmpty()) {
                    $data['fields'][$field] = nl2br(xarVarPrepForDisplay($original['fields'][$field]));
                } else {
                    $fmt = new UnifiedDiffFormatter;
                    $difference = $fmt->format($diff);
                    $data['fields'][$field] = nl2br(xarVarPrepForDisplay($difference));
                }
            }


        }
    }
    return $data;
}

?>
