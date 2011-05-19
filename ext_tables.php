<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_formulae_formulas'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas',		
		'label'     => 'formula',
		'label_alt' => 'firstname,lastname',
		'label_alt_force' => 1,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate DESC',	
		'delete' => 'deleted',
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_formulae_formulas.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:formulae/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform_ds.xml');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_formulae_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_formulae_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/energy_formula/', 'Energy Formula');
?>