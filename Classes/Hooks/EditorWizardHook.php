<?php
namespace AndreasKiessling\FormEditorLauncher\Hooks;

use AndreasKiessling\FormEditorLauncher\Tca\ShowFormNoteEditForm;

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
    ) {
        if (isset($identifier['ext-form-persistenceIdentifier'])) {
            $dataStructure['sheets']['sDEF']['ROOT']['el']['settings.editForm'] = [
                'TCEforms' => [
                    'config' => [
                        'type' => 'user',
                        'userFunc' => ShowFormNoteEditForm::class . '->showNote',
                    ],
                ],
            ];
        }

        return $dataStructure;
    }
}
