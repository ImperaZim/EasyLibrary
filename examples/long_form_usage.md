# Step-by-Step Guide for Using Form/LongForm

The `Form-LongForm` class provides a convenient way to create and display long forms with multiple buttons in PocketMine-MP plugins. This guide will walk you through the process of using `Form-LongForm` effectively in your plugin.

## Step 1: Installation

Ensure that you have the `EasyLibrary` plugin installed in your PocketMine-MP server. If not, download the `EasyLibrary.phar` file and place it in the `plugins` folder of your server. Restart the server to load the plugin.

## Step 2: Create a Form Class

You need to create a PHP class that extends the `Form` class provided by the `library\interface` namespace. This class will define the structure of your form and handle its behavior.

```php
<?php

namespace forms;

use library\interface\Form;
use internal\libform\Form as IForm;
use internal\libform\types\LongForm;
use pocketmine\player\Player;
use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\handler\ButtonResponse;

/**
* Class ExampleMenuForm
* @package forms
*/
class ExampleMenuForm extends Form {

  /**
  * Generates and sends the form to the player.
  */
  protected function structure(): IForm {
    return new LongForm(
      title: "Example Menu Form",
      content: '§7Any text:',
      buttons: $this->getButtons(),
      onClose: fn($player) => $this->getCloseCallback($player)
    );
  }

  /**
  * Retrieves an array of buttons for each available class.
  * @return Button[]
  */
  private function getButtons(): array {
    // Define your buttons here
  }

  /**
  * Handles the form closure and returns the next form to display.
  * @param Player $player
  * @return Form|null
  */
  private function getCloseCallback(Player $player): ?Form {
    // Handle form closure here
  }
}
```

## Step 3: Define Buttons

Inside your form class, define the buttons you want to include in your form. Each button is represented by an instance of the `Button` class.

## Step 4: Handle Button Responses

For each button, define a callback function that will be executed when the button is clicked. You can use the `ButtonResponse` class to define these callbacks.

## Step 5: Send the Form

Create an instance of your form class and call the `send()` method to send the form to the player.

```php
// Example of creating and sending the form
$player = $this->getPlayer();
$form = new ExampleMenuForm($player);
```

## Step 6: Process Button Responses

Handle button responses inside the callback functions defined for each button. You can perform any actions or display additional forms based on the button clicked by the player.

## Step 7: Customize and Extend

Feel free to customize and extend the `Form-LongForm` class to suit your plugin's needs. You can add more elements, modify form behavior, or integrate with other features of your plugin.