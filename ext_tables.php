<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'form_editor_launcher',
            'Configuration/TypoScript',
            'form_editor_launcher'
        );
    }
);
