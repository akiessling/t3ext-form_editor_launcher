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

use AndreasKiessling\FormEditorLauncher\Service\EditLinkService;
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
        $editable = false;

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
            } else {
                $view = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
                $view->setTemplatePathAndFilename(
                    'EXT:form_editor_launcher/Resources/Private/Templates/EditorWizard.html'
                );
                $view->assign('formPath', $formPath);

                $linkService = GeneralUtility::makeInstance(EditLinkService::class);

                if ($linkService->isEditable($formPath)) {
                    $editable = true;
                    $view->assign('onClick', $linkService->getOnClickCode($formPath));
                }
            }
        }

        $view->assign('isEditable', $editable);

        return $view->render();
    }
}
