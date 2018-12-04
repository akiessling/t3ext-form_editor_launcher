<?php

namespace AndreasKiessling\FormEditorLauncher\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class EditLinkService
{
    /**
     * @param string $formPath Path to the configured form yaml
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function getOnClickCode($formPath)
    {
        $typo3UriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $method = 'buildUriFromModule';
        if (version_compare(TYPO3_branch, '9.3', '>=')) {
            $method = 'buildUriFromRoute';
        }

        $editUri = $typo3UriBuilder->$method(
            'web_FormFormbuilder',
            [
                'tx_form_web_formformbuilder' => [
                    'formPersistenceIdentifier' => $formPath,
                    'action' => 'index',
                    'controller' => 'FormEditor',
                ],
            ]
        );

        return 'top.jump(' . GeneralUtility::quoteJSvalue(
                $editUri
            ) . ', \'web_FormFormbuilder\', \'web\'); return false;';
    }

    public function isEditable($formPath)
    {
        if (StringUtility::beginsWith($formPath, 'EXT:')) {
            return false;
        }

        $resourceFactory = ResourceFactory::getInstance();
        $file = $resourceFactory->retrieveFileOrFolderObject($formPath);

        if (!$file->checkActionPermission('write')) {
            return false;
        }

        if (!isset($GLOBALS['TBE_MODULES']['_configuration']['web_FormFormbuilder'])) {
            return false;
        }

        return $this->hasAccessToFormBuilder();
    }

    /**
     * @return bool
     */
    public function hasAccessToFormBuilder()
    {
        try {
            $GLOBALS['BE_USER']->modAccess($GLOBALS['TBE_MODULES']['_configuration']['web_FormFormbuilder']);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
