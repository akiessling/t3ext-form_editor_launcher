<?php
defined('TYPO3_MODE') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][\AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class] = \AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class;
