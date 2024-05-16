<?php

namespace form\forms;

use form\FormBase;
use pocketmine\player\Player;
use libraries\form\CustomForm;
use libraries\form\element\Label;
use libraries\form\element\Input;
use libraries\form\element\Slider;
use libraries\form\element\Toggle;
use libraries\form\element\Dropdown;
use libraries\form\element\StepSlider;
use libraries\form\CustomFormResponse;

/**
* Class ExampleCustomForm
* @package form\forms
*/
class ExampleCustomForm extends BaseForm {

  /**
  * Generates and sends the form to the player.
  */
  public function makeForm() : void {
    try {
      $this->setBaseForm(
        form: new CustomForm(
          title: 'Example Custom Form',
          elements: $this->getElements(),
          onSubmit: fn($player, $response) => $this->getSubmitCallback(
            player: $player,
            response: $response
          ),
          onClose: fn($player) => $this->getCloseCallback($player)
        )
      )->send();
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
  }

  /**
  * Retrieves an array of form elements.
  * @return array
  */
  private function getElements(): array {
    return [
      new Label('Label: Text Element.'),
      new Input('Input: Text Box Element.', 'Example Text', 'Default Value'),
      new Slider('Slider: Number Slider Element.', 1, 16, 1.0, 0),
      new StepSlider('Slider: Option Slider Element.', ['text 1', 'text 2'], 0),
      new Toggle('Toggle: Toggle Button Element', false),
      new Dropdown('Dropdown: Option Box Element', ['text 1', 'text 2'], 0)
    ];
  }

  /**
  * Handles the form submission and returns the next form to display.
  * @param Player $player
  * @param CustomFormResponse $response
  * @return FormBase|null
  */
  private function getSubmitCallback(Player $player, CustomFormResponse $response): ?FormBase {
    try {
      /**
      * NOTICE: The FormResponse Cost disregards Label elements as they are not interactive.
      * $id = indicative index of the element defined in getElements starting at 0 and disregarding Label elements.
      * $response->getValues()[$id]
      */

      $inputResponse = $response->getValues()[0];
      # $inputResponse: Returns the text written in the text box called.

      $sliderResponse = $response->getValues()[1];
      # $stepSliderResponse: Returns the numeric value chosen in the called slider.

      $stepSliderResponse = $response->getValues()[2];
      # $stepSliderResponse: Returns the index value of the chosen option according to the array passed in the called StepSlider.

      $toggleResponse = $response->getValues()[3];
      # $toggleResponse: Returns true/false according to the button toggle called.

      $dropdownResponse = $response->getValues()[4];
      # $dropdownResponse: Returns the index value of the chosen option according to the array passed in the called Dropdown.

    } catch (\Throwable $e) {
      new \crashdump($e);
    }
    return null;
  }

  /**
  * Handles the form closure and returns the next form to display.
  * @param Player $player
  * @return FormBase|null
  */
  private function getCloseCallback(Player $player): ?FormBase {
    $player->sendMessage('Form closed!');
    return null;
  }

}