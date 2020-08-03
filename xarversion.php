<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @copyright (C) 2007-2012 2skies.com
 * @subpackage Xarigami Changelog
 * @link http://xarigami.com/project/
*/
$modversion['name']           = 'changelog';
$modversion['directory']           = 'changelog';
$modversion['id']             = '185';
$modversion['version']        = '1.1.2';
$modversion['displayname']    = 'ChangeLog';
$modversion['description']    = 'Keep track of changes to module items';
$modversion['credits']        = '';
$modversion['help']           = '';
$modversion['changelog']      = '';
$modversion['license']        = '';
$modversion['official']       = 1;
$modversion['author']         = 'mikespub';
$modversion['contact']        = 'http://xarigami.com/';
$modversion['admin']          = 1;
$modversion['user']           = 0;
$modversion['class']          = 'Utility';
$modversion['category']       = 'Utilities';
$modversion['dependencyinfo']   = array(
                                    0 => array(
                                            'name' => 'core',
                                            'version_ge' => '1.4.0'
                                         ),
                                );

if (false) { //Load and translate once
    xarML('ChangeLog');
    xarML('Keep track of changes to module items');
}

?>
