<?php

namespace AndreasKiessling\FormEditorLauncher\Form\Element;

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
use TYPO3\CMS\Extbase\Object\ObjectManager;

class EditLinkNodeType extends \TYPO3\CMS\Backend\Form\Element\AbstractFormElement
{
    const NODE_NAME = 'formEditLink';

    public function render()
    {
        $result = $this->initializeResultArray();
        $data = $this->data['databaseRow'];
        $editable = false;

        $result['html'] = '';

        if (!is_array($data)) {
            return '';
        }

        $path = 'pi_flexform/data/sDEF/lDEF/settings.persistenceIdentifier/vDEF';

        if (ArrayUtility::isValidPath($data, $path)) {
            $selectedForm = ArrayUtility::getValueByPath($data, $path);
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

                $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                $linkService = $objectManager->get(EditLinkService::class);

                if ($linkService->isEditable($formPath)) {
                    $editable = true;
                    $view->assign('onClick', $linkService->getOnClickCode($formPath));
                }

                $view->assign('isEditable', $editable);
                $result['html'] = $view->render();
            }
        }

        return $result;
    }
}
