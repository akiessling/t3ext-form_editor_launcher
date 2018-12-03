<?php
namespace AndreasKiessling\FormEditorLauncher\Tca;

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
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ShowFormNoteEditForm
 * @package AndreasKiessling\FormEditorLauncher\Tca
 * @author Andreas Kießling
 */
class ShowFormNoteEditForm
{
    /**
     * @param array|null $params
     * @return string|null
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function showNote(array $params = null)
    {
        if (!is_array($params)) {
            return '';
        }

        $path = 'row/pi_flexform/data/sDEF/lDEF/settings.persistenceIdentifier/vDEF';

        if (ArrayUtility::isValidPath($params, $path)) {
            $selectedForm = ArrayUtility::getValueByPath($params, $path);
            $formPath = current($selectedForm);

            // no valid path saved, nothing to render
            if (!$formPath || empty($formPath)) {
                return '';
            }

            // can't edit a form definition from an extension
            if (\TYPO3\CMS\Core\Utility\StringUtility::beginsWith($formPath, 'EXT:')) {
                return $this->renderNotEditable($formPath);
            }

            $resourceFactory = ResourceFactory::getInstance();
            $file = $resourceFactory->retrieveFileOrFolderObject($formPath);

            if (!$file->checkActionPermission('write')) {
                return $this->renderNotEditable($formPath);
            } else {
                $typo3UriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
                $editUri = $typo3UriBuilder->buildUriFromRoute(
                    'web_FormFormbuilder',
                    [
                        'tx_form_web_formformbuilder' => [
                            'formPersistenceIdentifier' => $formPath,
                            'action' => 'index',
                            'controller' => 'FormEditor',
                        ],
                    ]
                );

                $onClickCode = 'top.jump(' . GeneralUtility::quoteJSvalue(
                    $editUri
                ) . ', \'web_FormFormbuilder\', \'web\'); return false;';

                $label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'edit_file',
                    'form_editor_launcher',
                    [$formPath]
                );

                $editIcon = GeneralUtility::makeInstance(IconFactory::class)->getIcon('actions-open', Icon::SIZE_SMALL);

                return sprintf('<a href="#" onclick="%1$s">%2$s %3$s</a>', $onClickCode, $editIcon, $label);
            }
        }

        return '';
    }

    private function renderNotEditable($formPath)
    {
        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIcon('status-edit-read-only', Icon::SIZE_SMALL);
        $message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
            'not_editable',
            'form_editor_launcher',
            [$formPath]
        );
        return $icon . ' ' . $message;
    }
}
