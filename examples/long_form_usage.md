# Step-by-Step Guide for Using Form/LongForm

The `Form-LongForm` class provides a convenient way to create and display long forms with multiple buttons in PocketMine-MP plugins. This guide will walk you through the process of using `Form-LongForm` effectively in your plugin.

## Step 1: Installation

Ensure that you have the `EasyLibrary` plugin installed in your PocketMine-MP server. If not, download the `EasyLibrary.phar` file and place it in the `plugins` folder of your server. Restart the server to load the plugin.

## Step 2: Create a Form Class

You need to create a PHP class that extends the `Form` class provided by the `library\interface` namespace. This class will define the structure of your form and handle its behavior.

```php
<?php

namespace forms;

use pocketmine\player\Player;

use library\interface\Form;
use internal\libform\Form as IForm;
use internal\libform\types\LongForm;
use internal\libform\elements\Image;
use internal\libform\elements\Button;
use internal\libform\interaction\ButtonResponse;

/**
* Class ExampleMenuForm
* @package forms
*/
class ExampleMenuForm extends Form {

  /**
  * Defines the form structure.
  */
  public function structure(): IForm {
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

```php 
// Example of creating a button.
new Button(
  text: 'Button with url image',
  image: Image::url('https://picsum.photos/200/200'),
  value: 'button_value'
);
```

## Step 4: Handle Button responses

For each button, define a callback function that will be executed when the button is clicked. You can use the `ButtonResponse` class to define these callbacks.
```php 
// Example of creating a button with interactions.
new Button(
  text: 'Button with resourcepack image',
  image: Image::path('textures/items/diamond_sword.png'),
  value: 'button_value',
  onclick: new ButtonResponse(
    function (Player $player, Button $button): void {
      $player->sendMessage("you clicked {$button->getValue()}");
    }
  ),
  reopen: false // Defines whether the form should be opened again after clicking.
);
```

## Step 5: Send the Form

Create an instance of your form class and call the `send()` method to send the form to the player.

```php
// Example of creating and sending the form
public function example(): void {
  new ExampleMenuForm($this->getPlayer());
}
```

## Step 6: Submit the form with predefined data

You can pass values when calling the form class as a temporary value

```php
// Example of creating and sending the form with data
public function example(): void {
  new ExampleMenuForm(
    $this->getPlayer(), 
    [
      'money' => 100
    ]
  );
}
```

You can obtain this value in your form class using:
```php 
public function structure(): IForm {
  // To get a specific value 
  $money = $this->getProcessedData('money');
  
  // To get all values 
  $data = $this->getProcessedData();
}
```

## Step 7: Customize and Extend

Feel free to customize and extend the `Form-LongForm` class to suit your plugin's needs. You can add more elements, modify form behavior, or integrate with other features of your plugin.
