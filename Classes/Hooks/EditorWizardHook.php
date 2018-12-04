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

use AndreasKiessling\FormEditorLauncher\Form\Element\EditLinkNodeType;

class EditorWizardHook
{
    /**
     * @param $dataStructure
     * @param $identifier
     * @return mixed
     */
    public function parseDataStructureByIdentifierPostProcess(
        $dataStructure,
        $identifier
    )
    {
        if (isset($identifier['ext-form-persistenceIdentifier'])) {
            $dataStructure['sheets']['sDEF']['ROOT']['el']['settings.editForm'] = [
                'TCEforms' => [
                    'config' => [
                        'type' => 'user',
                        'renderType' => EditLinkNodeType::NODE_NAME,
                    ],
                ],
            ];
        }

        return $dataStructure;
    }
}
