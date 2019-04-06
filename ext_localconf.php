<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][\AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class]
            = \AndreasKiessling\FormEditorLauncher\Hooks\EditorWizardHook::class;
        // File list edit icons
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['fileList']['editIconsHook'][]
            = \AndreasKiessling\FormEditorLauncher\Hooks\FileListEditIconsHook::class;

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1543956015] = [
            'nodeName' => \AndreasKiessling\FormEditorLauncher\Form\Element\EditLinkNodeType::NODE_NAME,
            'priority' => 40,
            'class' => \AndreasKiessling\FormEditorLauncher\Form\Element\EditLinkNodeType::class,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = \AndreasKiessling\FormEditorLauncher\Hooks\TtContentDrawItemHook::class;
    }
);
