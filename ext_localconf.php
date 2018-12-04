<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][\AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class]
            = \AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class;
        // File list edit icons
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['fileList']['editIconsHook'][]
            = \AndreasKiessling\FormEditorLauncher\Hooks\FileListEditIconsHook::class;
    }
);