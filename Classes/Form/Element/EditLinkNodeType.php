<?php declare(strict_types = 1);

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

        $result['html'] = '';

        if (!\is_array($data)) {
            return '';
        }

        $path = 'pi_flexform/data/sDEF/lDEF/settings.persistenceIdentifier/vDEF';

        if (ArrayUtility::isValidPath($data, $path)) {
            $selectedForm = ArrayUtility::getValueByPath($data, $path);
            $formPath = \current($selectedForm);

            // no valid path saved, nothing to render
            if (!$formPath || empty($formPath)) {
                return '';
            }

            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $linkService = $objectManager->get(EditLinkService::class);
            $result['html'] = $linkService->renderLink($formPath);
        }

        return $result;
    }
}
