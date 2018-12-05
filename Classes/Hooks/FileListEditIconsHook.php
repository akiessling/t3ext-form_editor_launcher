<?php

namespace AndreasKiessling\FormEditorLauncher\Hooks;

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

use AndreasKiessling\FormEditorLauncher\Service\EditLinkService;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Filelist\FileListEditIconHookInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;

class FileListEditIconsHook implements FileListEditIconHookInterface
{
    /**
     * @param array $cells
     * @param \TYPO3\CMS\Filelist\FileList $parentObject
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function manipulateEditIcons(&$cells, &$parentObject)
    {
        $fileOrFolderObject = $cells['__fileOrFolderObject'];
        if (!$fileOrFolderObject instanceof \TYPO3\CMS\Core\Resource\File) {
            return;
        }
        $fullIdentifier = $fileOrFolderObject->getCombinedIdentifier();
        $isFormDefinition = StringUtility::endsWith(
            $fullIdentifier,
            FormPersistenceManager::FORM_DEFINITION_FILE_EXTENSION
        );

        if (!$isFormDefinition) {
            return;
        }

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $linkService = $objectManager->get(EditLinkService::class);

        if ($linkService->isInWritableMount($fileOrFolderObject)
            && $fileOrFolderObject->checkActionPermission('write')
            && $linkService->hasAccessToFormBuilder()) {

            $editOnClick = $linkService->getOnClickCode($fileOrFolderObject->getCombinedIdentifier());

            $label = $GLOBALS['LANG']->sL(
                'LLL:EXT:form_editor_launcher/Resources/Private/Language/locallang.xlf:open_in_forms_module'
            );
            $icon = $iconFactory->getIcon('content-form', Icon::SIZE_SMALL)->render();

            $cells['edit'] = '<a href="#" class="btn btn-default" onclick="' . htmlspecialchars(
                    $editOnClick
                ) . '" title="' . $label . '">' . $icon . '</a>';
        }
    }
}
