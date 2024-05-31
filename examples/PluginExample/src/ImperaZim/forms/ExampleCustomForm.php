<?php

declare(strict_types = 1);

namespace ImperaZim\forms;

use pocketmine\player\Player;

use library\interface\Form;
use ImperaZim\PluginExample;
use internal\libform\Form as IForm;
use internal\libform\elements\Title;
use internal\libform\elements\Input;
use internal\libform\elements\Label;
use internal\libform\elements\Slider;
use internal\libform\elements\Toggle;
use internal\libform\elements\Element;
use internal\libform\types\CustomForm;
use internal\libform\elements\Dropdown;
use internal\libform\elements\StepSlider;
use internal\libform\interaction\CustomElementsResponse;

/**
* Class ExampleCustomForm
* @package ImperaZim\forms
*/
class ExampleCustomForm extends Form {

  /**
  * Defines the form structure.
  * @return IForm
  */
  public function structure(): IForm {
    return new CustomForm(
      title: $this->getTitle(),
      elements: $this->getElements(),
      onSubmit: fn($player, $response) => $this->getSubmitCallback($player, $response),
      onClose: fn($player) => $this->getCloseCallback($player)
    );
  }

  /**
  * Gets the title.
  * @return Title
  */
  private function getTitle(): Title {
    return new Title(
      text: PluginExample::getSettings('custom_form_title', '')
    );
  }

  /**
  * Retrieves an array of elements for each available class.
  * @return Element[]
  */
  private function getElements(): array {
    return [
      new Label('Label: Text Element.'),
      new Input('Input: Text Box Element.', 'Example Text', 'Default Value'),
      new Slider('Slider: Number Slider Element.', 1, 16, 1.0, 1),
      new StepSlider('Slider: Option Slider Element.', ['text 1', 'text 2'], 0),
      new Toggle('Toggle: Toggle Button Element', false),
      new Dropdown('Dropdown: Option Box Element', ['text 1', 'text 2'], 0)
    ];
  }

  /**
  * Handles the form submission and returns the next form to display.
  * @param Player $player
  * @param CustomElementsResponse $response
  */
  private function getSubmitCallback(Player $player, CustomElementsResponse $response): void {
    try {
      /**
      * NOTICE: The CustomElementsResponse Cost disregards Label elements as they are not interactive.
      * $id = indicative index of the element defined in getElements starting at 0 and disregarding Label elements.
      * $response->getElementResult($id) 
      * or $response->getValues($id) which returns the direct value
      */
      
      foreach ($response->getElementResult() as $elementResult) {
        var_dump($elementResult->getValue());
      }
      
      /*
      $inputResponse = $response->getElementResult(0);
      $inputResponseValue = $inputResponse->getValue();
      # $sliderResponseValue: Returns the text written in the text box called.

      $sliderResponse = $response->getElementResult(1);
      $sliderResponseValue = $sliderResponse->getValue();
      # $sliderResponseValue: Returns the numeric value chosen in the called slider.

      $stepSliderResponse = $response->getElementResult(2);
      $stepSliderResponseIndexValue = $stepSliderResponse->getValue();
      $stepSliderResponseOptionsValue = $stepSliderResponse->SelectedOption();
      # $stepSliderResponseIndexValue: Returns the index value of the chosen option according to the array passed in the called StepSlider.
      # $stepSliderResponseOptionsValue: Returns the text of the chosen option according to the array passed in the called StepSlider.

      $toggleResponse = $response->getElementResult(3);
      $toggleResponseValue = $toggleResponse->getValue();
      # $toggleResponseValue: Returns true/false according to the button toggle called.

      $dropdownResponse = $response->getElementResult(4);
      $dropdownResponseIndexValue = $dropdownResponse->getValue();
      $dropdownResponseOptionValue = $dropdownResponse->SelectedOption();
      # $dropdownResponseIndexValue: Returns the index value of the chosen option according to the array passed in the called Dropdown.
      # $dropdownResponseOptionValue: Returns the text of the chosen option according to the array passed in the called Dropdown.
      
      $player->sendMessage('Input: ' . $inputResponse);
      $player->sendMessage('Slider: ' . $sliderResponse);
      $player->sendMessage('StepSlider Index: ' . $stepSliderResponseIndexValue);
      $player->sendMessage('StepSlider Option: ' . $stepSliderResponseOptionsValue);
      $player->sendMessage('Toggle: ' . $toggleResponse ? 'true' : 'false');
      $player->sendMessage('Dropdown Index: ' . $dropdownResponseIndexValue);
      $player->sendMessage('Dropdown Option: ' . $dropdownResponseOptionsValue);
      */
    } catch (\Throwable $e) {
      new \crashdump($e);
    }
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