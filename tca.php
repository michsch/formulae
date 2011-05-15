<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_formulae_formulas'] = array (
	'ctrl' => $TCA['tx_formulae_formulas']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,formula,firstname,lastname,title,company,street,city,email,gtc,votes,finalvotes'
	),
	'feInterface' => $TCA['tx_formulae_formulas']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'formula' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.formula',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '4',
			)
		),
		'firstname' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.firstname',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'lastname' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.lastname',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.title',		
			'config' => array (
				'type' => 'radio',
				'items' => array (
					array('LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.title.I.0', '1'),
					array('LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.title.I.1', '2'),
				),
			)
		),
		'company' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.company',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'street' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.street',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'city' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.city',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'gtc' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.gtc',		
			'config' => array (
				'type' => 'check',
			)
		),
		'votes' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.votes',		
			'config' => array (
				'type' => 'none',
			)
		),
		'finalvotes' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:formulae/locallang_db.xml:tx_formulae_formulas.finalvotes',		
			'config' => array (
				'type' => 'none',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, formula, firstname, lastname, title;;;;2-2-2, company;;;;3-3-3, street, city, email, gtc, votes, finalvotes')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>